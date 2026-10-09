@props(['status'])

@php
    $styles = [
        'todo'        => 'bg-slate-100 text-slate-700',
        'in_progress' => 'bg-amber-100 text-amber-700',
        'completed'   => 'bg-emerald-100 text-emerald-700',
    ];
    $labels = [
        'todo'        => 'To Do',
        'in_progress' => 'In Progress',
        'completed'   => 'Completed',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold '.($styles[$status] ?? 'bg-slate-100 text-slate-700')]) }}>
    {{ $labels[$status] ?? ucfirst(str_replace('_', ' ', $status)) }}
</span>
