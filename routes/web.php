<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentPortalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas autenticadas
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');

    // Notificaciones
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/all', [NotificationController::class, 'all'])->name('notifications.all');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('/notifications/clear-read', [NotificationController::class, 'clearRead'])->name('notifications.clear-read');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto'])->name('profile.photo.delete');

    // Rutas de permisos (admin only)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/permisos', [PermissionController::class, 'index'])->name('permissions.index');
        Route::post('/permisos/toggle', [PermissionController::class, 'toggle'])->name('permissions.toggle');
        Route::post('/permisos/reset', [PermissionController::class, 'reset'])->name('permissions.reset');
    });

    // ==================== RUTAS ADMIN ====================
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        
        // Estudiantes
        Route::resource('students', StudentController::class);
        Route::get('students/{student}/qr', [StudentController::class, 'qrCode'])->name('students.qr');
        Route::post('students/{student}/regenerate-qr', [StudentController::class, 'regenerateQr'])->name('students.regenerate-qr');

        // Programas, Cursos, Módulos, Contenidos
        Route::resource('programs', ProgramController::class);
        Route::post('programs/{program}/courses', [ProgramController::class, 'storeCourse'])->name('programs.courses.store');
        Route::put('courses/{course}', [ProgramController::class, 'updateCourse'])->name('courses.update');
        Route::delete('courses/{course}', [ProgramController::class, 'destroyCourse'])->name('courses.destroy');
        
        Route::post('courses/{course}/modules', [ProgramController::class, 'storeModule'])->name('courses.modules.store');
        Route::put('modules/{module}', [ProgramController::class, 'updateModule'])->name('modules.update');
        Route::delete('modules/{module}', [ProgramController::class, 'destroyModule'])->name('modules.destroy');
        
        Route::post('modules/{module}/contents', [ProgramController::class, 'storeContent'])->name('modules.contents.store');
        Route::put('contents/{content}', [ProgramController::class, 'updateContent'])->name('contents.update');
        Route::delete('contents/{content}', [ProgramController::class, 'destroyContent'])->name('contents.destroy');

        // Pagos
    Route::resource('payments', PaymentController::class);
    Route::get('payments-pending', [PaymentController::class, 'pending'])->name('payments.pending');
    Route::get('payments/{payment}/process', [PaymentController::class, 'process'])->name('payments.process');
    Route::post('payments/{payment}/process', [PaymentController::class, 'processPayment'])->name('payments.process.store');
    Route::get('payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');
    Route::get('payments-next-installment', [PaymentController::class, 'getNextInstallment'])->name('payments.next-installment');
        Route::post('payments/{payment}/mark-paid', [PaymentController::class, 'markAsPaid'])->name('payments.mark-paid');
        Route::post('payments/{payment}/documents', [PaymentController::class, 'uploadDocument'])->name('payments.upload-document');
        Route::delete('payment-documents/{document}', [PaymentController::class, 'deleteDocument'])->name('payment-documents.destroy');
        Route::post('enrollments/{enrollment}/payment-plan', [PaymentController::class, 'createPaymentPlan'])->name('enrollments.payment-plan');
        Route::get('payments-report', [PaymentController::class, 'report'])->name('payments.report');
        Route::get('payments-overdue', [PaymentController::class, 'overdue'])->name('payments.overdue');

        // Asistencia
        Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('attendance/create-session', [AttendanceController::class, 'createSession'])->name('attendance.create-session');
        Route::post('attendance/sessions', [AttendanceController::class, 'storeSession'])->name('attendance.store-session');
        Route::get('attendance/sessions/{session}/edit', [AttendanceController::class, 'editSession'])->name('attendance.edit-session');
        Route::put('attendance/sessions/{session}', [AttendanceController::class, 'updateSession'])->name('attendance.update-session');
        Route::get('attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');

        // Permisos
        Route::get('permisos', [PermissionController::class, 'index'])->name('permissions.index');
        Route::post('permisos/update', [PermissionController::class, 'update'])->name('permissions.update');
        Route::post('permisos/toggle', [PermissionController::class, 'toggle'])->name('permissions.toggle');
        Route::post('permisos/reset', [PermissionController::class, 'reset'])->name('permissions.reset');
    });

    // ==================== RUTAS PROFESOR ====================
    Route::middleware(['role:admin,profesor'])->prefix('profesor')->name('profesor.')->group(function () {
        
        // Sesiones de clase
        Route::get('sessions/{session}', [AttendanceController::class, 'session'])->name('attendance.session');
        Route::get('sessions/{session}/scanner', [AttendanceController::class, 'scanner'])->name('attendance.scanner');
        Route::post('sessions/{session}/scan', [AttendanceController::class, 'scanQr'])->name('attendance.scan');
        Route::post('sessions/{session}/start', [AttendanceController::class, 'startSession'])->name('attendance.start');
        Route::post('sessions/{session}/end', [AttendanceController::class, 'endSession'])->name('attendance.end');
        Route::put('attendances/{attendance}', [AttendanceController::class, 'updateAttendance'])->name('attendance.update');
    });

    // ==================== RUTAS ESTUDIANTE ====================
    Route::middleware(['role:admin,estudiante'])->prefix('estudiante')->name('estudiante.')->group(function () {
        Route::get('mi-programa', [StudentPortalController::class, 'myProgram'])->name('my-program');
        Route::get('contenido/{content}', [StudentPortalController::class, 'viewContent'])->name('content.view');
        Route::post('contenido/{content}/progress', [StudentPortalController::class, 'updateProgress'])->name('content.progress');
        Route::get('mis-pagos', [StudentPortalController::class, 'myPayments'])->name('my-payments');
        Route::get('mi-asistencia', [StudentPortalController::class, 'myAttendance'])->name('my-attendance');
        Route::get('mi-qr', [StudentPortalController::class, 'myQr'])->name('my-qr');
    });
});

// Rutas sin prefijo para mantener compatibilidad
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Estudiantes - con permisos (rutas específicas ANTES de rutas con parámetros)
    Route::get('students/create', [StudentController::class, 'create'])->name('students.create')->middleware('permission:students.create');
    Route::post('students', [StudentController::class, 'store'])->name('students.store')->middleware('permission:students.create');
    Route::get('students', [StudentController::class, 'index'])->name('students.index')->middleware('permission:students.view');
    Route::get('students/{student}', [StudentController::class, 'show'])->name('students.show')->middleware('permission:students.view');
    Route::get('students/{student}/qr', [StudentController::class, 'qrCode'])->name('students.qr')->middleware('permission:students.view');
    Route::get('students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit')->middleware('permission:students.edit');
    Route::put('students/{student}', [StudentController::class, 'update'])->name('students.update')->middleware('permission:students.edit');
    Route::post('students/{student}/regenerate-qr', [StudentController::class, 'regenerateQr'])->name('students.regenerate-qr')->middleware('permission:students.edit');
    Route::delete('students/{student}', [StudentController::class, 'destroy'])->name('students.destroy')->middleware('permission:students.delete');
    
    // Programas - con permisos (rutas específicas ANTES de rutas con parámetros)
    Route::get('programs/create', [ProgramController::class, 'create'])->name('programs.create')->middleware('permission:programs.create');
    Route::post('programs', [ProgramController::class, 'store'])->name('programs.store')->middleware('permission:programs.create');
    Route::get('programs', [ProgramController::class, 'index'])->name('programs.index')->middleware('permission:programs.view');
    Route::get('programs/{program}', [ProgramController::class, 'show'])->name('programs.show')->middleware('permission:programs.view');
    Route::get('programs/{program}/data', [ProgramController::class, 'getData'])->name('programs.data')->middleware('permission:programs.view');
    Route::get('programs/{program}/edit', [ProgramController::class, 'edit'])->name('programs.edit')->middleware('permission:programs.edit');
    Route::put('programs/{program}', [ProgramController::class, 'update'])->name('programs.update')->middleware('permission:programs.edit');
    Route::delete('programs/{program}', [ProgramController::class, 'destroy'])->name('programs.destroy')->middleware('permission:programs.delete');
    
    // Cursos, Modulos y Contenidos - requieren permiso de edición de programas
    Route::middleware(['permission:programs.edit'])->group(function () {
        Route::post('programs/{program}/courses', [ProgramController::class, 'storeCourse'])->name('programs.courses.store');
        Route::put('courses/{course}', [ProgramController::class, 'updateCourse'])->name('courses.update');
        Route::delete('courses/{course}', [ProgramController::class, 'destroyCourse'])->name('courses.destroy');
        Route::post('courses/{course}/modules', [ProgramController::class, 'storeModule'])->name('courses.modules.store');
        Route::put('modules/{module}', [ProgramController::class, 'updateModule'])->name('modules.update');
        Route::delete('modules/{module}', [ProgramController::class, 'destroyModule'])->name('modules.destroy');
        Route::post('modules/{module}/contents', [ProgramController::class, 'storeContent'])->name('modules.contents.store');
        Route::put('contents/{content}', [ProgramController::class, 'updateContent'])->name('contents.update');
        Route::delete('contents/{content}', [ProgramController::class, 'destroyContent'])->name('contents.destroy');
    });
    
    // Pagos - con permisos (rutas específicas ANTES de rutas con parámetros)
    Route::get('payments/create', [PaymentController::class, 'create'])->name('payments.create')->middleware('permission:payments.create');
    Route::post('payments', [PaymentController::class, 'store'])->name('payments.store')->middleware('permission:payments.create');
    Route::get('payments-pending', [PaymentController::class, 'pending'])->name('payments.pending')->middleware('permission:payments.view');
    Route::get('payments-next-installment', [PaymentController::class, 'getNextInstallment'])->name('payments.next-installment')->middleware('permission:payments.view');
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index')->middleware('permission:payments.view');
    Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show')->middleware('permission:payments.view');
    Route::get('payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt')->middleware('permission:payments.view');
    Route::get('payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit')->middleware('permission:payments.edit');
    Route::put('payments/{payment}', [PaymentController::class, 'update'])->name('payments.update')->middleware('permission:payments.edit');
    Route::get('payments/{payment}/process', [PaymentController::class, 'process'])->name('payments.process')->middleware('permission:payments.edit');
    Route::post('payments/{payment}/process', [PaymentController::class, 'processPayment'])->name('payments.process.store')->middleware('permission:payments.edit');
    Route::post('payments/{payment}/mark-paid', [PaymentController::class, 'markAsPaid'])->name('payments.mark-paid')->middleware('permission:payments.edit');
    Route::post('payments/{payment}/upload-proof', [PaymentController::class, 'uploadProof'])->name('payments.upload-proof')->middleware('permission:payments.edit');
    Route::delete('payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy')->middleware('permission:payments.delete');
    
    // Asistencia - con permisos (rutas específicas ANTES de rutas con parámetros)
    Route::get('attendance/create-session', [AttendanceController::class, 'createSession'])->name('attendance.create-session')->middleware('permission:sessions.create');
    Route::post('attendance/sessions', [AttendanceController::class, 'storeSession'])->name('attendance.store-session')->middleware('permission:sessions.create');
    Route::get('attendance/report', [AttendanceController::class, 'report'])->name('attendance.report')->middleware('permission:attendance.view');
    Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index')->middleware('permission:attendance.view');
    Route::get('attendance/session/{session}', [AttendanceController::class, 'session'])->name('attendance.session')->middleware('permission:attendance.view');
    Route::get('attendance/sessions/{session}/edit', [AttendanceController::class, 'editSession'])->name('attendance.edit-session')->middleware('permission:sessions.edit');
    Route::put('attendance/sessions/{session}', [AttendanceController::class, 'updateSession'])->name('attendance.update-session')->middleware('permission:sessions.edit');
    Route::get('attendance/scanner/{session}', [AttendanceController::class, 'scanner'])->name('attendance.scanner')->middleware('permission:attendance.manage');
    Route::post('attendance/scan/{session}', [AttendanceController::class, 'scanQr'])->name('attendance.scan')->middleware('permission:attendance.manage');
});

require __DIR__.'/auth.php';

// Rutas de verificacion de email PERSONALIZADAS (despues de auth.php para sobrescribir)
Route::middleware(['auth'])->group(function () {
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
    
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware('signed')
        ->name('verification.verify');
});
