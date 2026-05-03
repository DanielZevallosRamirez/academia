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

        // Notificar a administradores
        Notification::notifyAdmins(
            Notification::TYPE_PROGRAM,
            'Programa actualizado',
            "El programa '{$program->name}' ha sido actualizado.",
            route('programs.show', $program),
            ['program_id' => $program->id]
        );

        return redirect()
            ->route('programs.show', $program)
            ->with('success', 'Programa actualizado exitosamente.');
    }

    public function destroy(Program $program)
    {
        $programName = $program->name;
        
        if ($program->image) {
            Storage::disk('public')->delete($program->image);
        }

        $program->delete();

        // Notificar a administradores
        Notification::notifyAdmins(
            Notification::TYPE_PROGRAM,
            'Programa eliminado',
            "El programa '{$programName}' ha sido eliminado del sistema.",
            route('programs.index'),
            ['program_name' => $programName]
        );

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

        // Notificar a administradores
        Notification::notifyAdmins(
            Notification::TYPE_PROGRAM,
            "Nuevo curso en {$program->name}",
            "Se ha agregado el curso '{$course->name}' al programa.",
            route('programs.show', $program),
            ['course_id' => $course->id, 'program_id' => $program->id]
        );

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
        $program = $course->program;

        // Notificar a administradores
        Notification::notifyAdmins(
            Notification::TYPE_PROGRAM,
            "Curso actualizado en {$program->name}",
            "Se ha actualizado el curso '{$course->name}'.",
            route('programs.show', $program->id),
            ['course_id' => $course->id, 'program_id' => $program->id]
        );

        return redirect()->route('programs.show', $program->id)->with('success', 'Curso actualizado exitosamente.');
    }

    public function destroyCourse(Course $course)
    {
        $program = $course->program;
        $courseName = $course->name;
        $course->delete();

        // Notificar a administradores
        Notification::notifyAdmins(
            Notification::TYPE_PROGRAM,
            "Curso eliminado de {$program->name}",
            "Se ha eliminado el curso '{$courseName}'.",
            route('programs.show', $program->id),
            ['course_name' => $courseName, 'program_id' => $program->id]
        );

        return redirect()->route('programs.show', $program->id)->with('success', 'Curso eliminado exitosamente.');
    }

    // ==================== MÓDULOS ====================

    public function storeModule(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $maxOrder = $course->modules()->max('order') ?? 0;

        $module = $course->modules()->create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'order' => $maxOrder + 1,
        ]);

        // Notificar a administradores
        $program = $course->program;
        Notification::notifyAdmins(
            Notification::TYPE_PROGRAM,
            "Nuevo modulo agregado a {$program->name}",
            "Se ha agregado el modulo '{$module->name}' al curso '{$course->name}'.",
            route('programs.show', $course->program_id),
            ['module_id' => $module->id, 'course_id' => $course->id, 'program_id' => $program->id]
        );

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
        
        $program = $module->course->program;

        // Notificar a administradores
        Notification::notifyAdmins(
            Notification::TYPE_PROGRAM,
            "Modulo actualizado en {$program->name}",
            "Se ha actualizado el modulo '{$module->name}'.",
            route('programs.show', $program->id),
            ['module_id' => $module->id, 'program_id' => $program->id]
        );

        return redirect()->route('programs.show', $program->id)->with('success', 'Modulo actualizado exitosamente.');
    }

    public function destroyModule(Module $module)
    {
        $program = $module->course->program;
        $moduleName = $module->name;
        $module->delete();

        // Notificar a administradores
        Notification::notifyAdmins(
            Notification::TYPE_PROGRAM,
            "Modulo eliminado de {$program->name}",
            "Se ha eliminado el modulo '{$moduleName}'.",
            route('programs.show', $program->id),
            ['module_name' => $moduleName, 'program_id' => $program->id]
        );

        return redirect()->route('programs.show', $program->id)->with('success', 'Modulo eliminado exitosamente.');
    }

    // ==================== CONTENIDOS ====================

    public function storeContent(Request $request, Module $module)
    {
        $type = $request->input('type');
        
        $rules = [
            'title' => 'required|string|max:255',
            'type' => 'required|in:pdf,video,audio,link,text',
        ];

        // Validación dinámica según el tipo
        switch ($type) {
            case 'video':
                $rules['external_url'] = 'required|url';
                $rules['description'] = 'nullable|string';
                break;
            case 'pdf':
                $rules['files'] = 'required|array|min:1';
                $rules['files.*'] = 'file|mimes:pdf|max:102400';
                $rules['description'] = 'nullable|string';
                break;
            case 'audio':
                $rules['files'] = 'required|array|min:1';
                $rules['files.*'] = 'file|mimes:mp3,wav,ogg|max:102400';
                $rules['description'] = 'nullable|string';
                break;
            case 'link':
                $rules['external_url'] = 'required|url';
                break;
            case 'text':
                $rules['content_text'] = 'required|string';
                break;
        }

        $validated = $request->validate($rules);

        $maxOrder = $module->contents()->max('order') ?? 0;

        // Si es PDF o Audio con múltiples archivos, crear un contenido por archivo
        if (in_array($type, ['pdf', 'audio']) && $request->hasFile('files')) {
            $files = $request->file('files');
            $firstContent = null;
            
            foreach ($files as $index => $file) {
                $path = $file->store('contents', 'public');
                $contentTitle = count($files) > 1 
                    ? $validated['title'] . ' (' . ($index + 1) . '/' . count($files) . ')'
                    : $validated['title'];
                
                $content = $module->contents()->create([
                    'title' => $contentTitle,
                    'description' => $validated['description'] ?? null,
                    'type' => $validated['type'],
                    'file_path' => $path,
                    'order' => $maxOrder + 1 + $index,
                ]);
                
                if (!$firstContent) $firstContent = $content;
            }
            $content = $firstContent;
        } else {
            $content = $module->contents()->create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'type' => $validated['type'],
                'external_url' => $validated['external_url'] ?? null,
                'content_text' => $validated['content_text'] ?? null,
                'order' => $maxOrder + 1,
            ]);
        }

        // Notificar a administradores
        $program = $module->course->program;
        Notification::notifyAdmins(
            Notification::TYPE_PROGRAM,
            "Nuevo contenido en {$program->name}",
            "Se ha agregado el contenido '{$content->title}' al modulo '{$module->name}'.",
            route('programs.show', $program->id),
            ['content_id' => $content->id, 'module_id' => $module->id, 'program_id' => $program->id]
        );

        return back()->with('success', 'Contenido agregado exitosamente.');
    }

    public function updateContent(Request $request, Content $content)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:pdf,video,audio,link,text',
            'file' => 'nullable|file|max:102400',
            'files.*' => 'nullable|file|max:102400',
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

        $program = $content->module->course->program;

        // Notificar a administradores
        Notification::notifyAdmins(
            Notification::TYPE_PROGRAM,
            "Contenido actualizado en {$program->name}",
            "Se ha actualizado el contenido '{$content->title}'.",
            route('programs.show', $program->id),
            ['content_id' => $content->id, 'program_id' => $program->id]
        );

        return redirect()->route('programs.show', $program->id)->with('success', 'Contenido actualizado exitosamente.');
    }

    public function destroyContent(Content $content)
    {
        $program = $content->module->course->program;
        $contentTitle = $content->title;
        
        if ($content->file_path) {
            Storage::disk('public')->delete($content->file_path);
        }

        $content->delete();

        // Notificar a administradores
        Notification::notifyAdmins(
            Notification::TYPE_PROGRAM,
            "Contenido eliminado de {$program->name}",
            "Se ha eliminado el contenido '{$contentTitle}'.",
            route('programs.show', $program->id),
            ['content_title' => $contentTitle, 'program_id' => $program->id]
        );

        return redirect()->route('programs.show', $program->id)->with('success', 'Contenido eliminado exitosamente.');
    }
}
