<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    protected $fillable = ['name', 'domain', 'subscription_status'];

    // Tenant တစ်ခုမှာ Task တွေ အများကြီး ရှိနိုင်ပါတယ်
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
