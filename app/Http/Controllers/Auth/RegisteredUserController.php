<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tenant; // Tenant Model ကို သုံးဖို့ ချိတ်ပေးပါ
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'company_name' => ['required', 'string', 'max:255'], // ကုမ္ပဏီအမည်အသစ်
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // ၁။ ပထမဆုံး Tenant (Company) အသစ် တစ်ခု ဖန်တီးမယ်
        $tenant = Tenant::create([
            'name' => $request->company_name,
            'domain' => strtolower(str_replace(' ', '', $request->company_name)) . '.localhost',
            'subscription_status' => 'active',
        ]);

        // ၂။ အဲ့ဒီ tenant_id ကို သုံးပြီး User အသစ် ဆောက်မယ် (Role ကို company_admin လို့ သတ်မှတ်မယ်)
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'company_admin',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}