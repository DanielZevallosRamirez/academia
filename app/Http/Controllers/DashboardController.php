<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassSession;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return match($user->role) {
            'admin' => $this->adminDashboard(),
            'profesor' => $this->profesorDashboard($user),
            'estudiante' => $this->estudianteDashboard($user),
            default => redirect()->route('login'),
        };
    }

    private function adminDashboard()
    {
        $stats = [
            'total_estudiantes' => User::estudiantes()->active()->count(),
            'total_profesores' => User::profesores()->active()->count(),
            'total_programas' => Program::active()->count(),
            'ingresos_mes' => Payment::paid()->thisMonth()->sum('amount'),
            'pagos_pendientes' => Payment::pending()->count(),
            'pagos_vencidos' => Payment::overdue()->count(),
        ];

        $recentEnrollments = Enrollment::with(['student', 'program'])
            ->latest()
            ->take(5)
            ->get();

        $recentPayments = Payment::with(['student', 'enrollment.program'])
            ->latest()
            ->take(5)
            ->get();

        $upcomingSessions = ClassSession::with(['course.program', 'professor'])
            ->upcoming()
            ->take(5)
            ->get();

        return view('dashboard.admin', compact(
            'stats',
            'recentEnrollments',
            'recentPayments',
            'upcomingSessions'
        ));
    }

    private function profesorDashboard(User $user)
    {
        $todaySessions = ClassSession::with(['course.program', 'attendances'])
            ->byProfessor($user->id)
            ->today()
            ->get();

        $upcomingSessions = ClassSession::with(['course.program'])
            ->byProfessor($user->id)
            ->upcoming()
            ->take(5)
            ->get();

        $courses = $user->courses()->with('program')->get();

        $stats = [
            'total_cursos' => $courses->count(),
            'sesiones_hoy' => $todaySessions->count(),
            'sesiones_mes' => ClassSession::byProfessor($user->id)
                ->whereMonth('session_date', now()->month)
                ->count(),
        ];

        return view('dashboard.profesor', compact(
            'stats',
            'todaySessions',
            'upcomingSessions',
            'courses'
        ));
    }

    private function estudianteDashboard(User $user)
    {
        $enrollment = $user->getActiveEnrollment();
        
        $program = $enrollment?->program?->load(['courses.modules.contents']);
        
        $recentAttendances = Attendance::with(['classSession.course'])
            ->byStudent($user->id)
            ->latest()
            ->take(5)
            ->get();

        $pendingPayments = Payment::where('user_id', $user->id)
            ->pending()
            ->orderBy('due_date')
            ->get();

        $stats = [
            'asistencia_rate' => $user->getAttendanceRate(),
            'pagos_pendientes' => $pendingPayments->count(),
            'dias_restantes' => $enrollment?->days_remaining ?? 0,
        ];

        // Calcular progreso por curso
        $courseProgress = [];
        if ($program) {
            foreach ($program->courses as $course) {
                $totalContents = 0;
                $completedContents = 0;
                
                foreach ($course->modules as $module) {
                    foreach ($module->contents as $content) {
                        $totalContents++;
                        if ($content->isCompletedBy($user)) {
                            $completedContents++;
                        }
                    }
                }
                
                $courseProgress[$course->id] = $totalContents > 0 
                    ? round(($completedContents / $totalContents) * 100, 1) 
                    : 0;
            }
        }

        return view('dashboard.estudiante', compact(
            'enrollment',
            'program',
            'recentAttendances',
            'pendingPayments',
            'stats',
            'courseProgress'
        ));
    }
}
