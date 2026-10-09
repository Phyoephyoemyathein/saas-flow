@props(['role'])

@php
    $styles = [
        'admin'   => 'bg-purple-100 text-purple-700 ring-purple-600/20',
        'manager' => 'bg-blue-100 text-blue-700 ring-blue-600/20',
        'member'  => 'bg-slate-100 text-slate-600 ring-slate-500/20',
        'user'    => 'bg-slate-100 text-slate-600 ring-slate-500/20',
    ];
    $labels = \App\Models\User::ROLES;
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ring-1 ring-inset '.($styles[$role] ?? $styles['member'])]) }}>
    {{ $labels[$role] ?? ucfirst($role) }}
</span>
