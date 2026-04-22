<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
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
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto'])->name('profile.photo.delete');

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
        Route::get('attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');
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
    Route::resource('students', StudentController::class);
    Route::get('students/{student}/qr', [StudentController::class, 'qrCode'])->name('students.qr');
    Route::post('students/{student}/regenerate-qr', [StudentController::class, 'regenerateQr'])->name('students.regenerate-qr');
    
    Route::resource('programs', ProgramController::class);
    Route::resource('payments', PaymentController::class);
    
    Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('attendance/session/{session}', [AttendanceController::class, 'session'])->name('attendance.session');
    Route::get('attendance/scanner/{session}', [AttendanceController::class, 'scanner'])->name('attendance.scanner');
    Route::post('attendance/scan/{session}', [AttendanceController::class, 'scanQr'])->name('attendance.scan');
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
