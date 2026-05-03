<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassSession;
use App\Models\Course;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = ClassSession::with(['course.program', 'professor', 'attendances']);

        if ($request->filled('date')) {
            $query->whereDate('session_date', $request->date);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('professor_id')) {
            $query->where('professor_id', $request->professor_id);
        }

        $sessions = $query->latest('session_date')->paginate(15);
        $courses = Course::active()->get();
        $professors = User::profesores()->active()->get();

        return view('attendance.index', compact('sessions', 'courses', 'professors'));
    }

    public function createSession()
    {
        $courses = Course::with('program')->active()->get();
        $professors = User::profesores()->active()->get();

        return view('attendance.create-session', compact('courses', 'professors'));
    }

    public function storeSession(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'professor_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'session_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'location' => 'nullable|string|max:255',
        ]);

        $session = ClassSession::create($validated);

        // Crear registros de asistencia para todos los estudiantes inscritos
        $course = Course::find($validated['course_id']);
        $enrolledStudents = User::estudiantes()
            ->whereHas('enrollments', function ($q) use ($course) {
                $q->where('program_id', $course->program_id)
                    ->where('status', 'activo');
            })
            ->get();

        foreach ($enrolledStudents as $student) {
            Attendance::create([
                'class_session_id' => $session->id,
                'user_id' => $student->id,
                'status' => 'ausente',
            ]);
        }

        // Notificar a administradores
        Notification::notifyAdmins(
            Notification::TYPE_ATTENDANCE,
            'Nueva sesion de clase creada',
            "Se ha creado la sesion '{$session->title}' para el curso {$course->name}.",
            route('attendance.session', $session),
            ['session_id' => $session->id]
        );

        return redirect()
            ->route('attendance.session', $session)
            ->with('success', 'Sesión de clase creada exitosamente.');
    }

    public function session(ClassSession $session)
    {
        $session->load(['course.program', 'professor', 'attendances.student']);

        // Sincronizar estudiantes inscritos que aun no tienen registro de asistencia
        $this->syncEnrolledStudents($session);
        
        // Recargar las asistencias
        $session->load('attendances.student');

        return view('attendance.session', compact('session'));
    }

    /**
     * Sincroniza los estudiantes inscritos al programa con la sesion de clase
     */
    private function syncEnrolledStudents(ClassSession $session)
    {
        $course = $session->course;
        
        // Obtener estudiantes inscritos al programa del curso
        $enrolledStudents = User::estudiantes()
            ->whereHas('enrollments', function ($q) use ($course) {
                $q->where('program_id', $course->program_id)
                    ->where('status', 'activo');
            })
            ->get();

        // Crear registros de asistencia para estudiantes que no lo tengan
        foreach ($enrolledStudents as $student) {
            Attendance::firstOrCreate(
                [
                    'class_session_id' => $session->id,
                    'user_id' => $student->id,
                ],
                [
                    'status' => 'ausente',
                ]
            );
        }
    }

    public function scanner(ClassSession $session)
    {
        $session->load(['course.program', 'attendances.student']);

        return view('attendance.scanner', compact('session'));
    }

    public function scanQr(Request $request, ClassSession $session)
    {
        $validated = $request->validate([
            'qr_code' => 'required|string',
        ]);

        // Buscar estudiante por código QR
        $student = User::where('qr_code', $validated['qr_code'])
            ->where('role', 'estudiante')
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Código QR no válido o estudiante no encontrado.',
            ], 404);
        }

        // Buscar o crear registro de asistencia
        $attendance = Attendance::where('class_session_id', $session->id)
            ->where('user_id', $student->id)
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'El estudiante no está inscrito en este curso.',
            ], 400);
        }

        if ($attendance->status === 'presente') {
            return response()->json([
                'success' => false,
                'message' => 'El estudiante ya registró su asistencia.',
            ], 400);
        }

        // Determinar si es tardanza
        $sessionStart = $session->session_date->setTimeFromTimeString($session->start_time->format('H:i:s'));
        $isLate = now()->gt($sessionStart->addMinutes(15));

        $attendance->update([
            'status' => $isLate ? 'tardanza' : 'presente',
            'check_in_time' => now(),
            'check_in_method' => 'qr',
        ]);

        // Notificar al estudiante sobre su asistencia
        Notification::notifyUser(
            $student->id,
            Notification::TYPE_ATTENDANCE,
            'Asistencia registrada',
            $isLate 
                ? "Tu asistencia fue registrada con tardanza en '{$session->title}'."
                : "Tu asistencia fue registrada exitosamente en '{$session->title}'.",
            null,
            ['session_id' => $session->id, 'status' => $isLate ? 'tardanza' : 'presente']
        );

        return response()->json([
            'success' => true,
            'message' => $isLate 
                ? "Asistencia registrada con tardanza: {$student->name}"
                : "Asistencia registrada: {$student->name}",
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'photo' => $student->photo_url,
                'status' => $isLate ? 'tardanza' : 'presente',
            ],
        ]);
    }

    public function updateAttendance(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'status' => 'required|in:presente,ausente,tardanza,justificado',
            'notes' => 'nullable|string',
        ]);

        $attendance->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'check_in_time' => in_array($validated['status'], ['presente', 'tardanza']) ? now() : null,
            'check_in_method' => 'manual',
        ]);

        // Notificar al estudiante sobre cambio en su asistencia
        $statusLabels = [
            'presente' => 'Presente',
            'ausente' => 'Ausente',
            'tardanza' => 'Tardanza',
            'justificado' => 'Justificado'
        ];
        $session = $attendance->classSession;
        Notification::notifyUser(
            $attendance->user_id,
            Notification::TYPE_ATTENDANCE,
            'Asistencia actualizada',
            "Tu asistencia en '{$session->title}' ha sido marcada como: {$statusLabels[$validated['status']]}.",
            null,
            ['session_id' => $session->id, 'status' => $validated['status']]
        );

        return back()->with('success', 'Asistencia actualizada exitosamente.');
    }

    public function editSession(ClassSession $session)
    {
        $courses = Course::with('program')->active()->get();
        $professors = User::profesores()->active()->get();

        return view('attendance.edit-session', compact('session', 'courses', 'professors'));
    }

    public function updateSession(Request $request, ClassSession $session)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'professor_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'session_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:programada,en_curso,finalizada,cancelada',
        ]);

        $session->update($validated);

        // Notificar a administradores
        Notification::notifyAdmins(
            Notification::TYPE_ATTENDANCE,
            'Sesion de clase actualizada',
            "La sesion '{$session->title}' ha sido actualizada.",
            route('attendance.session', $session),
            ['session_id' => $session->id]
        );

        return redirect()
            ->route('attendance.index')
            ->with('success', 'Sesion actualizada exitosamente.');
    }

    public function startSession(ClassSession $session)
    {
        $session->update(['status' => 'en_curso']);
        return back()->with('success', 'Sesión iniciada.');
    }

    public function endSession(ClassSession $session)
    {
        $session->update(['status' => 'finalizada']);
        return back()->with('success', 'Sesión finalizada.');
    }

    public function report(Request $request)
    {
        $query = Attendance::with(['student', 'classSession.course.program']);

        if ($request->filled('student_id')) {
            $query->where('user_id', $request->student_id);
        }

        if ($request->filled('course_id')) {
            $query->whereHas('classSession', function ($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereHas('classSession', function ($q) use ($request) {
                $q->whereDate('session_date', '>=', $request->date_from);
            });
        }

        if ($request->filled('date_to')) {
            $query->whereHas('classSession', function ($q) use ($request) {
                $q->whereDate('session_date', '<=', $request->date_to);
            });
        }

        $attendances = $query->latest()->paginate(20);
        $students = User::estudiantes()->active()->get();
        $courses = Course::active()->get();

        // Calcular estadísticas
        $stats = [
            'total' => Attendance::count(),
            'presente' => Attendance::present()->count(),
            'ausente' => Attendance::absent()->count(),
            'rate' => Attendance::count() > 0 
                ? round((Attendance::present()->count() / Attendance::count()) * 100, 1) 
                : 0,
        ];

        return view('attendance.report', compact('attendances', 'students', 'courses', 'stats'));
    }
}
