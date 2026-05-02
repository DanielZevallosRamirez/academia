@extends('layouts.app')

@section('title', 'Notificaciones')
@section('page-title', 'Notificaciones')
@section('page-description', 'Todas tus notificaciones')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header Actions -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-slate-800">Todas las notificaciones</h2>
        <div class="flex gap-2">
            <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 text-sm font-medium text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition-colors">
                    Marcar todo como leido
                </button>
            </form>
            <form action="{{ route('notifications.clear-read') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors">
                    Limpiar leidas
                </button>
            </form>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        @forelse($notifications as $notification)
            <a href="{{ $notification->link ? route('notifications.mark-read', $notification) : '#' }}" 
               class="block px-6 py-4 border-b border-slate-100 hover:bg-slate-50 transition-colors {{ !$notification->read_at ? 'bg-emerald-50/50' : '' }}">
                <div class="flex gap-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full {{ $notification->icon_class }} bg-current/10 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $notification->icon_svg !!}
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-slate-800">{{ $notification->title }}</p>
                                <p class="text-sm text-slate-600 mt-1">{{ $notification->message }}</p>
                            </div>
                            @if(!$notification->read_at)
                                <span class="flex-shrink-0 w-2.5 h-2.5 bg-emerald-500 rounded-full mt-1.5"></span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-400 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </a>
        @empty
            <div class="py-16 text-center">
                <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <p class="text-slate-500 text-lg">No tienes notificaciones</p>
                <p class="text-slate-400 text-sm mt-1">Las notificaciones apareceran aqui cuando haya actividad</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
