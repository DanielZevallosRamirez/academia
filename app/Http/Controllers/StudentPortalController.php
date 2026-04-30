<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\ContentProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentPortalController extends Controller
{
    public function myProgram()
    {
        $user = Auth::user();
        $enrollment = $user->getActiveEnrollment();

        if (!$enrollment) {
            return view('student-portal.no-program');
        }

        $program = $enrollment->program->load(['courses.modules.contents']);

        // Calcular progreso por módulo
        $moduleProgress = [];
        foreach ($program->courses as $course) {
            foreach ($course->modules as $module) {
                $moduleProgress[$module->id] = $module->getCompletionPercentage($user);
            }
        }

        return view('student-portal.my-program', compact('enrollment', 'program', 'moduleProgress'));
    }

    public function viewContent(Content $content)
    {
        $user = Auth::user();

        // Verificar que el estudiante tiene acceso a este contenido
        $enrollment = $user->getActiveEnrollment();
        if (!$enrollment) {
            abort(403, 'No tienes una inscripción activa.');
        }

        $programId = $content->module->course->program_id;
        if ($enrollment->program_id !== $programId) {
            abort(403, 'No tienes acceso a este contenido.');
        }

        // Obtener o crear progreso
        $progress = ContentProgress::firstOrCreate(
            ['user_id' => $user->id, 'content_id' => $content->id],
            ['progress_percent' => 0, 'completed' => false]
        );

        // Contenido anterior y siguiente
        $module = $content->module;
        $contents = $module->contents()->orderBy('order')->get();
        $currentIndex = $contents->search(fn($c) => $c->id === $content->id);
        
        $prevContent = $currentIndex > 0 ? $contents[$currentIndex - 1] : null;
        $nextContent = $currentIndex < $contents->count() - 1 ? $contents[$currentIndex + 1] : null;

        return view('student-portal.view-content', compact(
            'content', 
            'progress', 
            'prevContent', 
            'nextContent',
            'module'
        ));
    }

    public function updateProgress(Request $request, Content $content)
    {
        $validated = $request->validate([
            'progress_percent' => 'required|integer|min:0|max:100',
        ]);

        $user = Auth::user();

        $progress = ContentProgress::updateOrCreate(
            ['user_id' => $user->id, 'content_id' => $content->id],
            [
                'progress_percent' => $validated['progress_percent'],
                'completed' => $validated['progress_percent'] >= 100,
                'completed_at' => $validated['progress_percent'] >= 100 ? now() : null,
            ]
        );

        return response()->json([
            'success' => true,
            'progress' => $progress,
        ]);
    }

    public function myPayments()
    {
        $user = Auth::user();
        
        $payments = $user->payments()
            ->with('enrollment.program')
            ->orderBy('due_date', 'desc')
            ->paginate(10);

        // Calculate totals based on amount_paid field
        $allPayments = $user->payments()->get();
        
        // Total pagado = sum of all amount_paid
        $totalPagado = $allPayments->sum('amount_paid');
        
        // Total pendiente = sum of (amount - amount_paid) for unpaid payments
        $totalPendiente = $allPayments->sum(function ($payment) {
            if ($payment->real_status !== 'pagado') {
                return $payment->amount - ($payment->amount_paid ?? 0);
            }
            return 0;
        });

        $stats = [
            'total_pendiente' => $totalPendiente,
            'total_pagado' => $totalPagado,
            'proximo_pago' => $user->payments()->whereIn('status', ['pendiente', 'parcial'])->orderBy('due_date')->first(),
        ];

        return view('student-portal.my-payments', compact('payments', 'stats'));
    }

    public function myAttendance()
    {
        $user = Auth::user();

        $attendances = $user->attendances()
            ->with(['classSession.course.program'])
            ->latest()
            ->paginate(15);

        $stats = [
            'total_sesiones' => $user->attendances()->count(),
            'presentes' => $user->attendances()->present()->count(),
            'ausentes' => $user->attendances()->absent()->count(),
            'tasa_asistencia' => $user->getAttendanceRate(),
        ];

        return view('student-portal.my-attendance', compact('attendances', 'stats'));
    }

    public function myQr()
    {
        $user = Auth::user();
        return view('student-portal.my-qr', compact('user'));
    }
}
