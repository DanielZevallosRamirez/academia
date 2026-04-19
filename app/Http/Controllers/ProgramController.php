<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Module;
use App\Models\Content;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $query = Program::withCount(['courses', 'enrollments']);

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
        return view('programs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'price' => 'required|numeric|min:0',
            'duration_months' => 'required|integer|min:1',
        ]);

        $program = Program::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'price' => $validated['price'],
            'duration_months' => $validated['duration_months'],
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('programs', 'public');
            $program->update(['image' => $path]);
        }

        return redirect()
            ->route('programs.show', $program)
            ->with('success', 'Programa creado exitosamente.');
    }

    public function show(Program $program)
    {
        $program->load(['courses.modules.contents', 'enrollments.student']);
        
        return view('programs.show', compact('program'));
    }

    public function edit(Program $program)
    {
        return view('programs.edit', compact('program'));
    }

    public function update(Request $request, Program $program)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'price' => 'required|numeric|min:0',
            'duration_months' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

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

    // ==================== CURSOS ====================

    public function storeCourse(Request $request, Program $program)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $maxOrder = $program->courses()->max('order') ?? 0;

        $program->courses()->create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'order' => $maxOrder + 1,
        ]);

        return back()->with('success', 'Curso agregado exitosamente.');
    }

    public function updateCourse(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $course->update($validated);

        return back()->with('success', 'Curso actualizado exitosamente.');
    }

    public function destroyCourse(Course $course)
    {
        $course->delete();
        return back()->with('success', 'Curso eliminado exitosamente.');
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
            'is_active' => 'boolean',
        ]);

        $module->update($validated);

        return back()->with('success', 'Módulo actualizado exitosamente.');
    }

    public function destroyModule(Module $module)
    {
        $module->delete();
        return back()->with('success', 'Módulo eliminado exitosamente.');
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
            'external_url' => 'nullable|url',
            'content_text' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:1',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $content->update($validated);

        if ($request->hasFile('file')) {
            if ($content->file_path) {
                Storage::disk('public')->delete($content->file_path);
            }
            $path = $request->file('file')->store('contents', 'public');
            $content->update(['file_path' => $path]);
        }

        return back()->with('success', 'Contenido actualizado exitosamente.');
    }

    public function destroyContent(Content $content)
    {
        if ($content->file_path) {
            Storage::disk('public')->delete($content->file_path);
        }

        $content->delete();
        return back()->with('success', 'Contenido eliminado exitosamente.');
    }
}
