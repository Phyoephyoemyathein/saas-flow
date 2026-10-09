<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Profile</h1>
            <p class="text-sm text-slate-500 mt-1">Manage your account settings</p>
        </div>
    </x-slot>

    <div class="max-w-2xl space-y-6">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            @include('profile.partials.update-password-form')
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
