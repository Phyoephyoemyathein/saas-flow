<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SaaS Flow') }} — Welcome</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800">
    <div class="min-h-screen flex flex-col justify-between">
        
        {{-- Top Navigation Bar --}}
        <header class="w-full bg-white border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <span class="text-xl font-bold text-slate-900 tracking-tight">SaaS Flow</span>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">
                            Log in
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition">
                                Register
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </header>

        {{-- Hero Content Section --}}
        <main class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
            <div class="max-w-3xl w-full text-center space-y-8">
                
                <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-600 border border-indigo-100 mb-2">
                    ✨ Modern Workspace Management
                </div>

                <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    Manage Your Tasks & Projects with <span class="text-indigo-600">SaaS Flow</span>
                </h1>

                <p class="text-lg text-slate-600 max-w-xl mx-auto">
                    A clean, powerful, and responsive platform designed to streamline your workflow, user management, and team collaboration seamlessly.
                </p>

                <div class="flex flex-wrap justify-center gap-4 pt-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-6 py-3 text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition">
                            Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-6 py-3 text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition">
                            Get Started
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-6 py-3 text-base font-medium text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 rounded-xl transition">
                                Create Account
                            </a>
                        @endif
                    @endauth
                </div>

            </div>
        </main>

        {{-- Footer --}}
        <footer class="w-full bg-white border-t border-slate-200 py-6 text-center text-sm text-slate-500">
            &copy; {{ date('Y') }} SaaS Flow. All rights reserved.
        </footer>

    </div>
</body>
</html>