<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = ['tenant_id', 'title', 'description', 'status','assigned_to','due_date','priority','attachment'];

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope());
    }

    // Task တစ်ခုချင်းစီက Tenant တစ်ခုခုနဲ့ သက်ဆိုင်ပါတယ်
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments()
{
    return $this->hasMany(TaskComment::class)->latest(); // အသစ်ရေးထားတာတွေကို အပေါ်ဆုံးက ပြဖို့ latest() ထည့်ထားပါတယ်
}

public function activities()
{
    return $this->morphMany(ActivityLog::class, 'subject');
}
}


