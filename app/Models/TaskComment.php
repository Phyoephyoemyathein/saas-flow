<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'comment',
    ];

    // Comment တစ်ခုဟာ User တစ်ဦးနဲ့ သက်ဆိုင်ပါတယ်
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Comment တစ်ခုဟာ Task တစ်ခုနဲ့ သက်ဆိုင်ပါတယ်
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
