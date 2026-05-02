<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = User::estudiantes()->with(['enrollments.program']);

        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('dni', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filtro por programa
        if ($request->filled('program_id')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('program_id', $request->program_id);
            });
        }

        $students = $query->latest()->paginate(15);
        $programs = Program::active()->get();

        return view('students.index', compact('students', 'programs'));
    }

    public function create()
    {
        $programs = Program::active()->get();
        return view('students.create', compact('programs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'dni' => 'nullable|string|max:20|unique:users,dni',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|max:2048',
            'program_id' => 'nullable|exists:programs,id',
            'start_date' => 'required_with:program_id|date',
            'end_date' => 'required_with:program_id|date|after:start_date',
        ]);

        // Crear usuario
        $student = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make(Str::random(10)), // Contraseña temporal
            'role' => 'estudiante',
            'phone' => $validated['phone'] ?? null,
            'dni' => $validated['dni'] ?? null,
            'address' => $validated['address'] ?? null,
            'emergency_contact' => $validated['emergency_contact'] ?? null,
            'emergency_phone' => $validated['emergency_phone'] ?? null,
            'qr_code' => Str::uuid()->toString(),
        ]);

        // Subir foto
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('students', 'public');
            $student->update(['photo' => $path]);
        }

        // Crear inscripción si se seleccionó un programa
        if ($request->filled('program_id')) {
            $enrollment = Enrollment::create([
                'user_id' => $student->id,
                'program_id' => $validated['program_id'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'status' => 'activo',
            ]);

            $program = Program::find($validated['program_id']);
            
            // Notificar a los administradores sobre nueva inscripcion
            Notification::notifyAdmins(
                Notification::TYPE_ENROLLMENT,
                'Nueva inscripcion',
                "El estudiante {$student->name} se ha inscrito al programa {$program->name}.",
                route('students.show', $student),
                ['student_id' => $student->id, 'program_id' => $program->id]
            );

            // Notificar al estudiante sobre su inscripcion
            Notification::notifyUser(
                $student->id,
                Notification::TYPE_ENROLLMENT,
                'Bienvenido al programa',
                "Has sido inscrito exitosamente al programa {$program->name}. Bienvenido!",
                route('estudiante.my-program'),
                ['program_id' => $program->id]
            );
        }

        // Notificar a admins sobre nuevo estudiante registrado
        Notification::notifyAdmins(
            Notification::TYPE_USER,
            'Nuevo estudiante registrado',
            "Se ha registrado un nuevo estudiante: {$student->name}.",
            route('students.show', $student),
            ['student_id' => $student->id]
        );

        return redirect()
            ->route('students.show', $student)
            ->with('success', 'Estudiante registrado exitosamente.');
    }

    public function show(User $student)
    {
        $student->load([
            'enrollments.program',
            'enrollments.payments',
            'attendances.classSession.course',
            'contentProgress.content.module.course',
        ]);

        return view('students.show', compact('student'));
    }

    public function edit(User $student)
    {
        $programs = Program::active()->get();
        return view('students.edit', compact('student', 'programs'));
    }

    public function update(Request $request, User $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($student->id)],
            'phone' => 'nullable|string|max:20',
            'dni' => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($student->id)],
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|max:2048',
            'status' => 'required|in:activo,inactivo,suspendido',
            // Enrollment fields
            'program_id' => 'nullable|exists:programs,id',
            'enrollment_start_date' => 'nullable|required_with:program_id|date',
            'enrollment_end_date' => 'nullable|date|after_or_equal:enrollment_start_date',
            'payment_type' => 'nullable|in:contado,cuotas',
            'num_installments' => 'nullable|integer|min:2|max:12',
        ]);

        // Update student data
        $student->update([
            'name' => $validated['name'],
            'last_name' => $validated['last_name'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'dni' => $validated['dni'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'address' => $validated['address'] ?? null,
            'emergency_contact' => $validated['emergency_contact'] ?? null,
            'emergency_phone' => $validated['emergency_phone'] ?? null,
            'status' => $validated['status'],
        ]);

        if ($request->hasFile('photo')) {
            // Eliminar foto anterior
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }
            $path = $request->file('photo')->store('students', 'public');
            $student->update(['photo' => $path]);
        }

        // Create new enrollment if program is selected
        if ($request->filled('program_id')) {
            // Check if enrollment already exists for this program
            $existingEnrollment = Enrollment::where('user_id', $student->id)
                ->where('program_id', $validated['program_id'])
                ->first();
            
            if (!$existingEnrollment) {
                $paymentType = $request->input('payment_type', 'contado');
                $numInstallments = $paymentType === 'cuotas' ? $request->input('num_installments', 2) : 1;
                
                $enrollment = Enrollment::create([
                    'user_id' => $student->id,
                    'program_id' => $validated['program_id'],
                    'start_date' => $validated['enrollment_start_date'],
                    'end_date' => $validated['enrollment_end_date'] ?? null,
                    'status' => 'activo',
                    'payment_type' => $paymentType,
                    'num_installments' => $numInstallments,
                ]);
                
                // Create payment records based on payment type
                $program = Program::find($validated['program_id']);
                $this->createPaymentRecords($enrollment, $program, $paymentType, $numInstallments);
                
                // Notificar a los administradores sobre la nueva inscripcion
                Notification::notifyAdmins(
                    Notification::TYPE_ENROLLMENT,
                    'Nueva inscripcion registrada',
                    "{$student->name} {$student->last_name} ha sido inscrito al programa {$program->name}.",
                    route('students.show', $student),
                    ['student_id' => $student->id, 'program_id' => $program->id]
                );
                
                // Notificar al estudiante sobre su inscripcion
                Notification::notifyUser(
                    $student->id,
                    Notification::TYPE_ENROLLMENT,
                    'Bienvenido al programa',
                    "Has sido inscrito exitosamente al programa {$program->name}. Bienvenido!",
                    route('estudiante.my-program'),
                    ['program_id' => $program->id]
                );
            }
        }

        return redirect()
            ->route('students.show', $student)
            ->with('success', 'Estudiante actualizado exitosamente.');
    }

    /**
     * Create payment records based on payment type
     */
    private function createPaymentRecords(Enrollment $enrollment, Program $program, string $paymentType, int $numInstallments)
    {
        $totalAmount = $program->price;
        
        if ($paymentType === 'contado') {
            // Single payment - full amount
            Payment::create([
                'user_id' => $enrollment->user_id,
                'enrollment_id' => $enrollment->id,
                'amount' => $totalAmount,
                'status' => 'pendiente',
                'due_date' => $enrollment->start_date ?? now(),
                'installment_number' => 1,
                'total_installments' => 1,
                'concept' => 'mensualidad',
            ]);
        } else {
            // Multiple installments (cuotas)
            $installmentAmount = round($totalAmount / $numInstallments, 2);
            $startDate = $enrollment->start_date ?? now();
            
            for ($i = 1; $i <= $numInstallments; $i++) {
                // Adjust last installment to account for rounding
                $amount = ($i === $numInstallments) 
                    ? $totalAmount - ($installmentAmount * ($numInstallments - 1))
                    : $installmentAmount;
                
                Payment::create([
                    'user_id' => $enrollment->user_id,
                    'enrollment_id' => $enrollment->id,
                    'amount' => $amount,
                    'status' => 'pendiente',
                    'due_date' => $startDate->copy()->addMonths($i - 1),
                    'installment_number' => $i,
                    'total_installments' => $numInstallments,
                    'concept' => 'mensualidad_cuotas',
                ]);
            }
        }
    }

    public function destroy(User $student)
    {
        if ($student->photo) {
            Storage::disk('public')->delete($student->photo);
        }

        $student->delete();

        return redirect()
            ->route('students.index')
            ->with('success', 'Estudiante eliminado exitosamente.');
    }

    public function qrCode(User $student)
    {
        return view('students.qr-code', compact('student'));
    }

    public function regenerateQr(User $student)
    {
        $student->update(['qr_code' => Str::uuid()->toString()]);

        return back()->with('success', 'Código QR regenerado exitosamente.');
    }
}
