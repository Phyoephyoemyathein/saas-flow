<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Add User</h1>
            <p class="text-sm text-slate-500 mt-1">Create a new team member and assign a role</p>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <form method="POST" action="{{ route('users.store') }}" class="space-y-5">
                @csrf

                <div>
                    <x-input-label for="name" :value="__('Full Name')" class="text-slate-700 font-medium"/>
                    <x-text-input id="name" class="block mt-1.5 w-full rounded-lg" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('Email Address')" class="text-slate-700 font-medium"/>
                    <x-text-input id="email" class="block mt-1.5 w-full rounded-lg" type="email" name="email" :value="old('email')" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="role" :value="__('Role')" class="text-slate-700 font-medium"/>
                    <select id="role" name="role" required class="block mt-1.5 w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach($roles as $value => $label)
                            <option value="{{ $value }}" {{ old('role', 'member') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    <p class="text-xs text-slate-400 mt-1.5">
                        <strong>Admin</strong> — full access including user management.
                        <strong>Manager</strong> — team dashboard and task management.
                        <strong>Member</strong> — personal tasks only.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <x-input-label for="password" :value="__('Password')" class="text-slate-700 font-medium"/>
                        <x-text-input id="password" class="block mt-1.5 w-full rounded-lg" type="password" name="password" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-slate-700 font-medium"/>
                        <x-text-input id="password_confirmation" class="block mt-1.5 w-full rounded-lg" type="password" name="password_confirmation" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('users.index') }}" class="px-4 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">Cancel</a>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors">Create User</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
