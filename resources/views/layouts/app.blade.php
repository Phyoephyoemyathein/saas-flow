<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SaaS Flow') }} — @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800">
    <div class="min-h-screen flex bg-slate-50">

        {{-- Sidebar (ဘယ်ဘက် Fixed Sidebar) --}}
        @include('layouts.partials.sidebar')

        {{-- Main Content Area (ml-64 နဲ့ flex-1 သုံးပြီး ဘယ်ဘက် Space ချန်မယ်) --}}
        <div class="ml-64 flex-1 flex flex-col min-w-0">
            @include('layouts.partials.topbar')

            <main class="flex-1 p-6">
                @isset($header)
                    <div class="mb-6">
                        {{ $header }}
                    </div>
                @endisset

                {{ $slot }}
            </main>
        </div>

    </div>
</body>
</html>