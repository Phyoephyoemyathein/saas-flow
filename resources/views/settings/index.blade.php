<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Settings</h1>
            <p class="text-sm text-slate-500 mt-1">Manage your account and preferences</p>
        </div>
    </x-slot>

    <div class="max-w-2xl space-y-6">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-semibold text-slate-900">Profile Settings</h3>
                    <p class="text-sm text-slate-500 mt-1">Update your name, email address, and password.</p>
                    <a href="{{ route('profile.edit') }}"
                       class="inline-flex items-center mt-4 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-semibold text-slate-900">Account Information</h3>
                    <dl class="mt-3 space-y-2 text-sm">
                        <div class="flex justify-between py-2 border-b border-slate-100">
                            <dt class="text-slate-500">Name</dt>
                            <dd class="font-medium text-slate-900">{{ Auth::user()->name }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-100">
                            <dt class="text-slate-500">Email</dt>
                            <dd class="font-medium text-slate-900">{{ Auth::user()->email }}</dd>
                        </div>
                        <div class="flex justify-between py-2">
                            <dt class="text-slate-500">Role</dt>
                            <dd><x-role-badge :role="Auth::user()->role"/></dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
