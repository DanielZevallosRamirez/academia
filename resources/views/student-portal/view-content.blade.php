@extends('layouts.app')

@section('title', $content->title)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('estudiante.my-program') }}" class="hover:text-indigo-600">Mi Programa</a>
        <i data-lucide="chevron-right" class="w-4 h-4"></i>
        <span>{{ $module->course->name ?? 'Curso' }}</span>
        <i data-lucide="chevron-right" class="w-4 h-4"></i>
        <span>{{ $module->name }}</span>
        <i data-lucide="chevron-right" class="w-4 h-4"></i>
        <span class="text-gray-900 font-medium">{{ $content->title }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Main Content Area -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Content Card -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <!-- Content Header -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center
                            {{ $content->type === 'pdf' ? 'bg-red-100 text-red-600' : '' }}
                            {{ $content->type === 'video' ? 'bg-blue-100 text-blue-600' : '' }}
                            {{ $content->type === 'audio' ? 'bg-purple-100 text-purple-600' : '' }}
                            {{ $content->type === 'link' ? 'bg-green-100 text-green-600' : '' }}
                            {{ $content->type === 'text' ? 'bg-gray-100 text-gray-600' : '' }}">
                            <i data-lucide="{{ $content->icon }}" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">{{ $content->title }}</h1>
                            <p class="text-sm text-gray-500">{{ ucfirst($content->type) }} @if($content->duration_minutes)- {{ $content->duration_minutes }} min @endif</p>
                        </div>
                    </div>
                </div>

                <!-- Content Body -->
                <div class="p-6">
                    @if($content->type === 'video')
                        @php
                            $videoUrl = $content->external_url;
                            $embedUrl = '';
                            
                            // YouTube
                            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $videoUrl, $matches)) {
                                $embedUrl = 'https://www.youtube.com/embed/' . $matches[1];
                            }
                            // Vimeo
                            elseif (preg_match('/vimeo\.com\/(\d+)/', $videoUrl, $matches)) {
                                $embedUrl = 'https://player.vimeo.com/video/' . $matches[1];
                            }
                        @endphp
                        
                        @if($embedUrl)
                            <div class="aspect-video bg-black rounded-lg overflow-hidden">
                                <iframe 
                                    src="{{ $embedUrl }}" 
                                    class="w-full h-full"
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                    allowfullscreen>
                                </iframe>
                            </div>
                        @else
                            <div class="aspect-video bg-gray-100 rounded-lg flex items-center justify-center">
                                <div class="text-center">
                                    <i data-lucide="video" class="w-12 h-12 text-gray-400 mx-auto mb-2"></i>
                                    <p class="text-gray-500 mb-4">Video externo</p>
                                    <a href="{{ $videoUrl }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                        <i data-lucide="external-link" class="w-4 h-4"></i>
                                        Abrir en nueva ventana
                                    </a>
                                </div>
                            </div>
                        @endif
                        
                    @elseif($content->type === 'pdf')
                        <div class="space-y-4">
                            @if($content->file_path)
                                <div class="aspect-[4/3] bg-gray-100 rounded-lg overflow-hidden">
                                    <iframe 
                                        src="{{ Storage::url($content->file_path) }}" 
                                        class="w-full h-full"
                                        frameborder="0">
                                    </iframe>
                                </div>
                                <div class="flex justify-center">
                                    <a href="{{ Storage::url($content->file_path) }}" target="_blank" download class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                        <i data-lucide="download" class="w-4 h-4"></i>
                                        Descargar PDF
                                    </a>
                                </div>
                            @else
                                <div class="p-8 bg-gray-100 rounded-lg text-center">
                                    <i data-lucide="file-text" class="w-12 h-12 text-gray-400 mx-auto mb-2"></i>
                                    <p class="text-gray-500">No hay archivo PDF disponible</p>
                                </div>
                            @endif
                        </div>
                        
                    @elseif($content->type === 'audio')
                        <div class="p-8 bg-gradient-to-r from-purple-50 to-indigo-50 rounded-lg">
                            <div class="text-center mb-6">
                                <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i data-lucide="headphones" class="w-10 h-10 text-purple-600"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $content->title }}</h3>
                            </div>
                            @if($content->file_path)
                                <audio controls class="w-full">
                                    <source src="{{ Storage::url($content->file_path) }}" type="audio/mpeg">
                                    Tu navegador no soporta el elemento de audio.
                                </audio>
                            @else
                                <p class="text-center text-gray-500">No hay archivo de audio disponible</p>
                            @endif
                        </div>
                        
                    @elseif($content->type === 'link')
                        <div class="p-8 bg-green-50 rounded-lg text-center">
                            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="link" class="w-10 h-10 text-green-600"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Enlace externo</h3>
                            <p class="text-gray-500 mb-4">{{ $content->external_url }}</p>
                            <a href="{{ $content->external_url }}" target="_blank" class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                <i data-lucide="external-link" class="w-5 h-5"></i>
                                Abrir enlace
                            </a>
                        </div>
                        
                    @elseif($content->type === 'text')
                        <div class="prose prose-indigo max-w-none">
                            {!! nl2br(e($content->content_text)) !!}
                        </div>
                    @endif
                </div>

                <!-- Description -->
                @if($content->description)
                <div class="p-6 border-t border-gray-200 bg-gray-50">
                    <h3 class="font-semibold text-gray-900 mb-2">Descripcion</h3>
                    <p class="text-gray-600">{{ $content->description }}</p>
                </div>
                @endif
            </div>

            <!-- Navigation -->
            <div class="flex items-center justify-between">
                @if($prevContent)
                    <a href="{{ route('estudiante.content.view', $prevContent) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg text-gray-700 hover:bg-gray-50">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                        <span class="hidden sm:inline">{{ Str::limit($prevContent->title, 20) }}</span>
                        <span class="sm:hidden">Anterior</span>
                    </a>
                @else
                    <div></div>
                @endif

                @if($nextContent)
                    <a href="{{ route('estudiante.content.view', $nextContent) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        <span class="hidden sm:inline">{{ Str::limit($nextContent->title, 20) }}</span>
                        <span class="sm:hidden">Siguiente</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                @else
                    <a href="{{ route('estudiante.my-program') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        Finalizar modulo
                    </a>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Progress Card -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Tu progreso</h3>
                
                <div class="space-y-4">
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1">
                            <span class="text-gray-500">Contenido</span>
                            <span class="font-medium">{{ $progress->progress_percent ?? 0 }}%</span>
                        </div>
                        <div class="bg-gray-200 rounded-full h-2">
                            <div class="bg-indigo-600 rounded-full h-2 transition-all" style="width: {{ $progress->progress_percent ?? 0 }}%"></div>
                        </div>
                    </div>

                    @if($progress->completed)
                        <div class="flex items-center gap-2 p-3 bg-green-50 rounded-lg text-green-700">
                            <i data-lucide="check-circle" class="w-5 h-5"></i>
                            <span class="text-sm font-medium">Completado</span>
                        </div>
                    @else
                        <form action="{{ route('estudiante.content.progress', $content) }}" method="POST">
                            @csrf
                            <input type="hidden" name="progress_percent" value="100">
                            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                <i data-lucide="check" class="w-4 h-4"></i>
                                Marcar como completado
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Module Contents -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="font-semibold text-gray-900">{{ $module->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $module->contents->count() }} contenidos</p>
                </div>
                <div class="divide-y divide-gray-100 max-h-80 overflow-y-auto">
                    @foreach($module->contents as $item)
                        <a href="{{ route('estudiante.content.view', $item) }}" 
                           class="flex items-center gap-3 p-3 hover:bg-gray-50 {{ $item->id === $content->id ? 'bg-indigo-50 border-l-2 border-indigo-600' : '' }}">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0
                                {{ $item->type === 'pdf' ? 'bg-red-100 text-red-600' : '' }}
                                {{ $item->type === 'video' ? 'bg-blue-100 text-blue-600' : '' }}
                                {{ $item->type === 'audio' ? 'bg-purple-100 text-purple-600' : '' }}
                                {{ $item->type === 'link' ? 'bg-green-100 text-green-600' : '' }}
                                {{ $item->type === 'text' ? 'bg-gray-100 text-gray-600' : '' }}">
                                <i data-lucide="{{ $item->icon }}" class="w-4 h-4"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate {{ $item->id === $content->id ? 'text-indigo-600' : '' }}">
                                    {{ $item->title }}
                                </p>
                            </div>
                            @if($item->isCompletedBy(auth()->user()))
                                <i data-lucide="check-circle" class="w-4 h-4 text-green-500 flex-shrink-0"></i>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});
</script>
@endpush
@endsection
