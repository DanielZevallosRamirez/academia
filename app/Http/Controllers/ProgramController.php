<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Module;
use App\Models\Content;
use App\Models\Notification;
use App\Models\Program;
use App\Models\ClassSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $query = Program::with('teacher')->withCount(['courses', 'enrollments']);

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $programs = $query->latest()->paginate(12);

        return view('programs.index', compact('programs'));
    }

    public function create()
    {
        $teachers = User::where('role', 'profesor')->orderBy('name')->get();
        return view('programs.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'price' => 'required|numeric|min:0',
            'duration_months' => 'required|integer|min:1',
            'total_hours' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'schedules' => 'nullable|array',
            'schedules.*' => 'nullable|string',
            'status' => 'required|in:activo,inactivo',
            'teacher_id' => 'nullable|exists:users,id',
        ]);

        // Process schedules
        $schedules = array_filter($validated['schedules'] ?? []);
        
        $program = Program::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'duration_months' => $validated['duration_months'],
            'total_hours' => $validated['total_hours'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'schedule' => !empty($schedules) ? json_encode($schedules) : null,
            'status' => $validated['status'],
            'teacher_id' => $validated['teacher_id'] ?? null,
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('programs', 'public');
            $program->update(['image' => $path]);
        }

        // Notificar a administradores sobre nuevo programa
        Notification::notifyAdmins(
            Notification::TYPE_PROGRAM,
            'Nuevo programa creado',
            "Se ha creado el programa: {$program->name}.",
            route('programs.show', $program),
            ['program_id' => $program->id]
        );

        return redirect()
            ->route('programs.show', $program)
            ->with('success', 'Programa creado exitosamente.');
    }

    public function show(Program $program)
    {
        $program->load(['teacher', 'courses.modules.contents', 'enrollments.student']);
        
        return view('programs.show', compact('program'));
    }

    public function edit(Program $program)
    {
        $teachers = User::where('role', 'profesor')->orderBy('name')->get();
        return view('programs.edit', compact('program', 'teachers'));
    }

    public function update(Request $request, Program $program)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'price' => 'required|numeric|min:0',
            'duration_months' => 'required|integer|min:1',
            'total_hours' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'schedules' => 'nullable|array',
            'schedules.*' => 'nullable|string|max:255',
            'status' => 'required|in:activo,inactivo',
            'teacher_id' => 'nullable|exists:users,id',
        ]);

        // Process schedules - filter empty values and encode as JSON
        $schedules = array_values(array_filter($request->input('schedules', []), fn($s) => !empty(trim($s))));
        $validated['schedule'] = !empty($schedules) ? json_encode($schedules) : null;
        unset($validated['schedules']);

        $program->update($validated);

        if ($request->hasFile('image')) {
            if ($program->image) {
                Storage::disk('public')->delete($program->image);
            }
            $path = $request->file('image')->store('programs', 'public');
            $program->update(['image' => $path]);
        }

        return redirect()
            ->route('programs.show', $program)
            ->with('success', 'Programa actualizado exitosamente.');
    }

    public function destroy(Program $program)
    {
        if ($program->image) {
            Storage::disk('public')->delete($program->image);
        }

        $program->delete();

        return redirect()
            ->route('programs.index')
            ->with('success', 'Programa eliminado exitosamente.');
    }

    /**
     * Get program data for AJAX requests
     */
    public function getData(Program $program)
    {
        // Parse schedule - can be JSON array or simple string
        $schedules = [];
        if ($program->schedule) {
            $decoded = json_decode($program->schedule, true);
            $schedules = is_array($decoded) ? $decoded : [$program->schedule];
        }
        
        return response()->json([
            'id' => $program->id,
            'name' => $program->name,
            'start_date' => $program->start_date?->format('Y-m-d'),
            'end_date' => $program->end_date?->format('Y-m-d'),
            'schedule' => implode(' | ', $schedules),
            'schedules' => $schedules,
            'price' => $program->price,
            'duration_months' => $program->duration_months,
        ]);
    }

    // ==================== CURSOS ====================

    public function storeCourse(Request $request, Program $program)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $maxOrder = $program->courses()->max('order') ?? 0;

        $course = $program->courses()->create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'order' => $maxOrder + 1,
        ]);

        // Crear sesion de clase inicial automaticamente si el programa tiene fechas
        if ($program->start_date) {
            $this->createInitialSession($course, $program);
        }

        return back()->with('success', 'Curso agregado exitosamente.');
    }

    /**
     * Crear sesion de clase inicial para un curso nuevo
     */
    private function createInitialSession(Course $course, Program $program)
    {
        // Obtener un profesor activo (el primero disponible)
        $professor = User::profesores()->active()->first();
        
        if (!$professor) {
            return; // No hay profesores disponibles
        }

        // Parsear horario del programa
        $scheduleTime = '09:00';
        $endTime = '11:00';
        
        if ($program->schedule) {
            $schedules = json_decode($program->schedule, true);
            if (is_array($schedules) && !empty($schedules)) {
                // Intentar extraer hora del primer horario (formato: "Lunes 10:00 - 12:15")
                if (preg_match('/(\d{1,2}:\d{2})\s*-\s*(\d{1,2}:\d{2})/', $schedules[0], $matches)) {
                    $scheduleTime = $matches[1];
                    $endTime = $matches[2];
                }
            }
        }

        // Crear la sesion inicial
        ClassSession::create([
            'course_id' => $course->id,
            'professor_id' => $professor->id,
            'title' => 'Clase 1 - Introduccion',
            'description' => 'Sesion introductoria del curso ' . $course->name,
            'session_date' => $program->start_date,
            'start_time' => $scheduleTime,
            'end_time' => $endTime,
            'location' => 'Virtual',
            'status' => 'programada',
        ]);
    }

    public function updateCourse(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
            'status' => 'nullable|in:activo,inactivo',
        ]);

        $course->update($validated);

        return redirect()->route('programs.show', $course->program_id)->with('success', 'Curso actualizado exitosamente.');
    }

    public function destroyCourse(Course $course)
    {
        $programId = $course->program_id;
        $course->delete();
        return redirect()->route('programs.show', $programId)->with('success', 'Curso eliminado exitosamente.');
    }

    // ==================== MÓDULOS ====================

    public function storeModule(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $maxOrder = $course->modules()->max('order') ?? 0;

        $course->modules()->create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'order' => $maxOrder + 1,
        ]);

        return back()->with('success', 'Módulo agregado exitosamente.');
    }

    public function updateModule(Request $request, Module $module)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $module->update($validated);
        
        $programId = $module->course->program_id;
        return redirect()->route('programs.show', $programId)->with('success', 'Modulo actualizado exitosamente.');
    }

    public function destroyModule(Module $module)
    {
        $programId = $module->course->program_id;
        $module->delete();
        return redirect()->route('programs.show', $programId)->with('success', 'Modulo eliminado exitosamente.');
    }

    // ==================== CONTENIDOS ====================

    public function storeContent(Request $request, Module $module)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:pdf,video,audio,link,text',
            'file' => 'nullable|file|max:102400', // 100MB
            'external_url' => 'nullable|url',
            'content_text' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:1',
        ]);

        $maxOrder = $module->contents()->max('order') ?? 0;

        $content = $module->contents()->create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'external_url' => $validated['external_url'] ?? null,
            'content_text' => $validated['content_text'] ?? null,
            'duration_minutes' => $validated['duration_minutes'] ?? null,
            'order' => $maxOrder + 1,
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('contents', 'public');
            $content->update(['file_path' => $path]);
        }

        return back()->with('success', 'Contenido agregado exitosamente.');
    }

    public function updateContent(Request $request, Content $content)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:pdf,video,audio,link,text',
            'file' => 'nullable|file|max:102400',
            'url' => 'nullable|string',
            'content_text' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:0',
            'order' => 'nullable|integer',
        ]);

        // Map url to external_url for the model
        if (isset($validated['url'])) {
            $validated['external_url'] = $validated['url'];
            unset($validated['url']);
        }

        $content->update($validated);

        if ($request->hasFile('file')) {
            if ($content->file_path) {
                Storage::disk('public')->delete($content->file_path);
            }
            $path = $request->file('file')->store('contents', 'public');
            $content->update(['file_path' => $path]);
        }

        $programId = $content->module->course->program_id;
        return redirect()->route('programs.show', $programId)->with('success', 'Contenido actualizado exitosamente.');
    }

    public function destroyContent(Content $content)
    {
        $programId = $content->module->course->program_id;
        
        if ($content->file_path) {
            Storage::disk('public')->delete($content->file_path);
        }

        $content->delete();
        return redirect()->route('programs.show', $programId)->with('success', 'Contenido eliminado exitosamente.');
    }
}
