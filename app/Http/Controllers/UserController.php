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

class UserController extends Controller
{
    /**
     * Mostrar lista de usuarios con filtros
     */
    public function index(Request $request)
    {
        $query = User::with(['enrollments.program']);

        // Filtro por rol
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Busqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('dni', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por programa (solo aplica a estudiantes)
        if ($request->filled('program_id')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('program_id', $request->program_id);
            });
        }

        // Estadisticas
        $stats = [
            'total' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'secretarios' => User::where('role', 'secretario')->count(),
            'administrativos' => User::where('role', 'administrativo')->count(),
            'profesores' => User::where('role', 'profesor')->count(),
            'estudiantes' => User::where('role', 'estudiante')->count(),
            'activos' => User::where('status', 'activo')->count(),
        ];

        $users = $query->latest()->paginate(15)->withQueryString();
        $programs = Program::active()->get();

        return view('users.index', compact('users', 'programs', 'stats'));
    }

    /**
     * Mostrar formulario de creacion
     */
    public function create(Request $request)
    {
        $programs = Program::active()->get();
        $defaultRole = $request->get('role', 'estudiante');
        
        return view('users.create', compact('programs', 'defaultRole'));
    }

    /**
     * Guardar nuevo usuario
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:' . implode(',', array_keys(User::ROLES)),
            'phone' => 'nullable|string|max:20',
            'dni' => 'nullable|string|max:20|unique:users,dni',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|max:2048',
            'specialty' => 'nullable|string|max:255', // Para profesores
            // Campos de inscripcion (solo para estudiantes)
            'program_id' => 'nullable|exists:programs,id',
            'start_date' => 'required_with:program_id|nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'payment_type' => 'nullable|in:contado,cuotas',
            'num_installments' => 'nullable|integer|min:2|max:12',
        ]);

        // Crear usuario
        $user = User::create([
            'name' => $validated['name'],
            'last_name' => $validated['last_name'] ?? null,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password'] ?? Str::random(10)),
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'dni' => $validated['dni'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'address' => $validated['address'] ?? null,
            'emergency_contact' => $validated['emergency_contact'] ?? null,
            'emergency_phone' => $validated['emergency_phone'] ?? null,
            'specialty' => $validated['specialty'] ?? null,
            'qr_code' => Str::uuid()->toString(),
            'status' => 'activo',
        ]);

        // Subir foto
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('users', 'public');
            $user->update(['photo' => $path]);
        }

        // Crear inscripcion si se selecciono un programa (solo para estudiantes)
        if ($validated['role'] === 'estudiante' && $request->filled('program_id')) {
            $paymentType = $request->input('payment_type', 'contado');
            $numInstallments = $paymentType === 'cuotas' ? $request->input('num_installments', 2) : 1;
            
            $enrollment = Enrollment::create([
                'user_id' => $user->id,
                'program_id' => $validated['program_id'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'] ?? null,
                'status' => 'activo',
                'payment_type' => $paymentType,
                'num_installments' => $numInstallments,
            ]);

            $program = Program::find($validated['program_id']);
            
            // Crear pagos
            $this->createPaymentRecords($enrollment, $program, $paymentType, $numInstallments);
            
            // Notificar inscripcion
            Notification::notifyAdmins(
                Notification::TYPE_ENROLLMENT,
                'Nueva inscripcion',
                "El estudiante {$user->name} se ha inscrito al programa {$program->name}.",
                route('users.show', $user),
                ['user_id' => $user->id, 'program_id' => $program->id]
            );

            Notification::notifyUser(
                $user->id,
                Notification::TYPE_ENROLLMENT,
                'Bienvenido al programa',
                "Has sido inscrito exitosamente al programa {$program->name}.",
                route('estudiante.my-program'),
                ['program_id' => $program->id]
            );
        }

        // Notificar nuevo usuario
        $roleNames = ['admin' => 'Administrador', 'profesor' => 'Profesor', 'estudiante' => 'Estudiante'];
        Notification::notifyAdmins(
            Notification::TYPE_USER,
            "Nuevo {$roleNames[$user->role]} registrado",
            "Se ha registrado: {$user->name} {$user->last_name}.",
            route('users.show', $user),
            ['user_id' => $user->id, 'role' => $user->role]
        );

        return redirect()
            ->route('users.show', $user)
            ->with('success', "{$roleNames[$user->role]} registrado exitosamente.");
    }

    /**
     * Mostrar detalle de usuario
     */
    public function show(User $user)
    {
        $user->load([
            'enrollments.program',
            'enrollments.payments',
            'attendances.classSession.course',
            'contentProgress.content.module.course',
        ]);

        return view('users.show', compact('user'));
    }

    /**
     * Mostrar formulario de edicion
     */
    public function edit(User $user)
    {
        $programs = Program::active()->get();
        return view('users.edit', compact('user', 'programs'));
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:' . implode(',', array_keys(User::ROLES)),
            'phone' => 'nullable|string|max:20',
            'dni' => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|max:2048',
            'status' => 'required|in:activo,inactivo,suspendido',
            'specialty' => 'nullable|string|max:255',
            // Campos de inscripcion
            'program_id' => 'nullable|exists:programs,id',
            'enrollment_start_date' => 'nullable|date',
            'enrollment_end_date' => 'nullable|date|after_or_equal:enrollment_start_date',
            'payment_type' => 'nullable|in:contado,cuotas',
            'num_installments' => 'nullable|integer|min:2|max:12',
        ]);

        // Actualizar datos
        $updateData = [
            'name' => $validated['name'],
            'last_name' => $validated['last_name'] ?? null,
            'email' => $validated['email'],
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'dni' => $validated['dni'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'address' => $validated['address'] ?? null,
            'emergency_contact' => $validated['emergency_contact'] ?? null,
            'emergency_phone' => $validated['emergency_phone'] ?? null,
            'status' => $validated['status'],
            'specialty' => $validated['specialty'] ?? null,
        ];

        // Actualizar password si se proporciona
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        // Subir foto
        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $path = $request->file('photo')->store('users', 'public');
            $user->update(['photo' => $path]);
        }

        // Crear nueva inscripcion si se selecciono programa (solo estudiantes)
        if ($user->role === 'estudiante' && $request->filled('program_id')) {
            $existingEnrollment = Enrollment::where('user_id', $user->id)
                ->where('program_id', $validated['program_id'])
                ->first();
            
            if (!$existingEnrollment) {
                $paymentType = $request->input('payment_type', 'contado');
                $numInstallments = $paymentType === 'cuotas' ? $request->input('num_installments', 2) : 1;
                
                $enrollment = Enrollment::create([
                    'user_id' => $user->id,
                    'program_id' => $validated['program_id'],
                    'start_date' => $validated['enrollment_start_date'],
                    'end_date' => $validated['enrollment_end_date'] ?? null,
                    'status' => 'activo',
                    'payment_type' => $paymentType,
                    'num_installments' => $numInstallments,
                ]);
                
                $program = Program::find($validated['program_id']);
                $this->createPaymentRecords($enrollment, $program, $paymentType, $numInstallments);
                
                Notification::notifyAdmins(
                    Notification::TYPE_ENROLLMENT,
                    'Nueva inscripcion',
                    "{$user->name} ha sido inscrito al programa {$program->name}.",
                    route('users.show', $user),
                    ['user_id' => $user->id, 'program_id' => $program->id]
                );
            }
        }

        $roleNames = ['admin' => 'Administrador', 'profesor' => 'Profesor', 'estudiante' => 'Estudiante'];
        
        return redirect()
            ->route('users.show', $user)
            ->with('success', "{$roleNames[$user->role]} actualizado exitosamente.");
    }

    /**
     * Eliminar usuario
     */
    public function destroy(User $user)
    {
        $userName = "{$user->name} {$user->last_name}";
        $userRole = $user->role;
        
        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
        }

        $user->delete();

        $roleNames = ['admin' => 'Administrador', 'profesor' => 'Profesor', 'estudiante' => 'Estudiante'];
        
        Notification::notifyAdmins(
            Notification::TYPE_USER,
            "{$roleNames[$userRole]} eliminado",
            "'{$userName}' ha sido eliminado del sistema.",
            route('users.index'),
            ['user_name' => $userName]
        );

        return redirect()
            ->route('users.index')
            ->with('success', "{$roleNames[$userRole]} eliminado exitosamente.");
    }

    /**
     * Mostrar QR del usuario
     */
    public function qrCode(User $user)
    {
        return view('users.qr-code', compact('user'));
    }

    /**
     * Regenerar QR
     */
    public function regenerateQr(User $user)
    {
        $user->update(['qr_code' => Str::uuid()->toString()]);
        return back()->with('success', 'Codigo QR regenerado exitosamente.');
    }

    /**
     * Crear registros de pago
     */
    private function createPaymentRecords(Enrollment $enrollment, Program $program, string $paymentType, int $numInstallments)
    {
        $totalAmount = $program->price;
        
        if ($paymentType === 'contado') {
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
            $installmentAmount = round($totalAmount / $numInstallments, 2);
            $startDate = $enrollment->start_date ?? now();
            
            for ($i = 1; $i <= $numInstallments; $i++) {
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
}
