@props(['label', 'value', 'icon' => null, 'color' => 'indigo'])

@php
    $colors = [
        'indigo' => ['bg' => 'bg-indigo-50', 'icon' => 'bg-indigo-500', 'text' => 'text-indigo-600'],
        'green'  => ['bg' => 'bg-emerald-50', 'icon' => 'bg-emerald-500', 'text' => 'text-emerald-600'],
        'blue'   => ['bg' => 'bg-blue-50', 'icon' => 'bg-blue-500', 'text' => 'text-blue-600'],
        'amber'  => ['bg' => 'bg-amber-50', 'icon' => 'bg-amber-500', 'text' => 'text-amber-600'],
        'slate'  => ['bg' => 'bg-slate-50', 'icon' => 'bg-slate-500', 'text' => 'text-slate-600'],
        'purple' => ['bg' => 'bg-purple-50', 'icon' => 'bg-purple-500', 'text' => 'text-purple-600'],
    ];
    $c = $colors[$color] ?? $colors['indigo'];
@endphp

<div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
            <p class="text-3xl font-bold text-slate-900 mt-1">{{ $value }}</p>
        </div>
        @if($icon)
            <div class="w-12 h-12 rounded-xl {{ $c['icon'] }} flex items-center justify-center shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                </svg>
            </div>
        @endif
    </div>
</div>
