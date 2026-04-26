<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassSession;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Program;
use App\Models\User;
use Carbon\Carbon;
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
            'total_estudiantes' => User::where('role', 'estudiante')->where('status', 'activo')->count(),
            'total_profesores' => User::where('role', 'profesor')->where('status', 'activo')->count(),
            'total_programas' => Program::where('status', 'activo')->count(),
            'ingresos_mes' => Payment::whereMonth('created_at', Carbon::now()->month)
                                    ->whereYear('created_at', Carbon::now()->year)
                                    ->where('status', 'pagado')
                                    ->sum('amount'),
        ];

        $pagos_pendientes = Payment::where('status', 'pendiente')
            ->with(['enrollment.student'])
            ->orderBy('due_date')
            ->take(5)
            ->get();

        $estudiantes_recientes = User::where('role', 'estudiante')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $inscripciones_por_mes = Enrollment::selectRaw('EXTRACT(MONTH FROM created_at) as mes, COUNT(*) as total')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        return view('dashboard.admin', compact(
            'stats',
            'pagos_pendientes',
            'estudiantes_recientes',
            'inscripciones_por_mes'
        ));
    }

    private function profesorDashboard(User $user)
    {
        // Get programs assigned to this teacher
        $mis_programas = Program::where('teacher_id', $user->id)
            ->with(['courses', 'enrollments.student'])
            ->get();
        
        // Get courses from those programs
        $mis_cursos = $mis_programas->pluck('courses')->flatten();
        
        $sesiones_hoy = [];
        $proximas_sesiones = [];

        if (class_exists('App\Models\ClassSession') && $mis_cursos->isNotEmpty()) {
            $sesiones_hoy = ClassSession::whereIn('course_id', $mis_cursos->pluck('id'))
                ->whereDate('session_date', Carbon::today())
                ->with('course')
                ->get();

            $proximas_sesiones = ClassSession::whereIn('course_id', $mis_cursos->pluck('id'))
                ->where('session_date', '>', Carbon::now())
                ->orderBy('session_date')
                ->take(5)
                ->get();
        }

        // Count students enrolled in teacher's programs
        $total_estudiantes = Enrollment::whereIn('program_id', $mis_programas->pluck('id'))
            ->where('status', 'activo')
            ->count();
        
        // Get recent students with their enrollments
        $mis_estudiantes = User::whereHas('enrollments', function($query) use ($mis_programas) {
            $query->whereIn('program_id', $mis_programas->pluck('id'))
                  ->where('status', 'activo');
        })->with(['enrollments' => function($query) use ($mis_programas) {
            $query->whereIn('program_id', $mis_programas->pluck('id'))
                  ->with('program');
        }])->take(10)->get();

        return view('dashboard.profesor', compact(
            'mis_programas',
            'mis_cursos',
            'sesiones_hoy',
            'proximas_sesiones',
            'total_estudiantes',
            'mis_estudiantes'
        ));
    }

    private function estudianteDashboard(User $user)
    {
        $mis_inscripciones = $user->enrollments()
            ->with(['program.courses.modules'])
            ->where('status', 'activo')
            ->get();

        $mis_pagos = Payment::whereHas('enrollment', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->orderBy('due_date')->get();

        $pago_pendiente = $mis_pagos->where('status', 'pendiente')->first();

        $mi_asistencia = Attendance::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $progreso_contenidos = 0;
        if (method_exists($user, 'contentProgress')) {
            $progreso_contenidos = $user->contentProgress()
                ->where('completed', true)
                ->count();
        }

        return view('dashboard.estudiante', compact(
            'mis_inscripciones',
            'mis_pagos',
            'pago_pendiente',
            'mi_asistencia',
            'progreso_contenidos'
        ));
    }
}
