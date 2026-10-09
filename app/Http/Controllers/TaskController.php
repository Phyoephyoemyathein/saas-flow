<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TaskComment;
use App\Notifications\NewTaskAssigned;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id ?? 1;
        $query = Task::query();
    
        // 🚨 အရေးကြီးဆုံး: တကယ်လို့ User က Member ဖြစ်နေရင် သူနဲ့ Assigned ဖြစ်တဲ့ Task တွေကိုပဲ မြင်ရမယ်
        if ($user->isMember()) {
            $query->where('assigned_to', $user->id);
        }
    
        // Title အလိုက် ရှာဖွေရန် (Search)
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
    
        // Status အလိုက် စစ်ထုတ်ရန် (Filter)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
    
        // Member အလိုက် စစ်ထုတ်ရန် (Filter - Admin/Manager တွေအတွက်သာ)
        if ($request->filled('assigned_to') && !$user->isMember()) {
            $query->where('assigned_to', $request->assigned_to);
        }
    
        // တစ်မျက်နှာလျှင် Task ၅ ခုစီဖြင့် စာမျက်နှာခွဲပြရန် (Pagination)
        $tasks = $query->latest()->paginate(5)->withQueryString();
    
        // 🔥 Recent Activity များကိုပါ တစ်ခါတည်း တွဲယူမည် (Tenant အလိုက် သို့မဟုတ် အားလုံး)
        $activities = \App\Models\ActivityLog::where('tenant_id', $tenantId)
                        ->latest()
                        ->take(10)
                        ->get();
    
        // View ဆီသို့ $tasks အပြင် $activities ကိုပါ compact ထဲ ထည့်ပို့ပေးမည်
        return view('tasks.index', compact('tasks', 'activities'));
    }

    public function create()
    {
        // Member တွေ Task အသစ် ဆောက်ခွင့်မရှိဘူးဆိုရင် ဒီမှာ စစ်လို့ရပါတယ်
        if (Auth::user()->isMember()) {
            abort(403, 'Unauthorized action.');
        }

        return view('tasks.create');
    }

    public function store(Request $request)
{
    if (Auth::user()->isMember()) {
        abort(403, 'Unauthorized action.');
    }

    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'status' => 'required|in:todo,in_progress,completed',
        'assigned_to' => 'nullable|exists:users,id', 
        'priority' => 'required|in:low,medium,high',
        'due_date' => 'nullable|date',
        'attachment' => 'nullable|file|mimes:pdf,xlsx,xls,jpg,png,jpeg|max:10240',
    ]);

    // ဖိုင်ပါလာပါက storage/app/public/attachments ထဲသို့ သိမ်းမည်
    $path = null;
    if ($request->hasFile('attachment')) {
        $path = $request->file('attachment')->store('attachments', 'public');
    }

    $user = Auth::user();
    $tenantId = $user->tenant_id ?? 1;

    // Task ကို Create လုပ်တဲ့အခါ attachment path ပါ ထည့်ပေးရန်
    $task = Task::create([
        'tenant_id' => $tenantId,
        'title' => $request->title,
        'description' => $request->description,
        'status' => $request->status,
        'assigned_to' => $request->assigned_to, 
        'priority' => $request->priority, 
        'due_date' => $request->due_date,
        'attachment' => $path,
    ]);

    // 🔥 Activity Log ကို ဒီနေရာမှာ ထည့်ပေးပါမယ် 🔥
    \App\Models\ActivityLog::create([
        'tenant_id' => $tenantId,
        'user_id' => Auth::id(),
        'subject_type' => Task::class,
        'subject_id' => $task->id, // အပေါ်က ဆောက်ပြီးသား $task ကနေ id ယူသုံးတာပါ
        'action' => 'created',
        'description' => Auth::user()->name . ' created task "' . $task->title . '"',
    ]);

    // Task ကို Assign လုပ်ထားသူ ရှိရင် Notification ပို့မည်
    if ($task->assigned_to) {
        $assignee = \App\Models\User::find($task->assigned_to);
        if ($assignee) {
            $assignee->notify(new NewTaskAssigned($task));
        }
    }

    return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
}

    public function show(Task $task)
{
    $user = Auth::user();

    // Member ဖြစ်နေရင် သူ့ Task မှ မဟုတ်ရင် ဝင်မကြည့်ခိုင်းဘဲ 403 Error ပြမယ်
    if ($user->isMember() && $task->assigned_to !== $user->id) {
        abort(403, 'Unauthorized action.');
    }

    // Comment များနှင့် User များကို Eager Load လုပ်ပေးခြင်း
    $task->load(['comments.user', 'assignee']);

    return view('tasks.show', compact('task'));
}

    public function edit(Task $task)
    {
        $user = Auth::user();

        // Member ဖြစ်နေရင် သူနဲ့မဆိုင်တဲ့ Task ကို edit လုပ်လို့မရစေရ
        if ($user->isMember() && $task->assigned_to !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
{
    $user = Auth::user();

    // Member ဖြစ်နေရင် သူနဲ့မဆိုင်တဲ့ Task ကို update လုပ်လို့မရစေရ
    if ($user->isMember() && $task->assigned_to !== $user->id) {
        abort(403, 'Unauthorized action.');
    }

    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'status' => 'required|in:todo,in_progress,completed',
        'assigned_to' => 'nullable|exists:users,id', 
        'priority' => 'required|in:low,medium,high',
        'due_date' => 'nullable|date',
        'attachment' => 'nullable|file|mimes:pdf,xlsx,xls,jpg,png,jpeg|max:10240',
    ]);

    // ရှေ့က assigned_to နဲ့ အခု update လုပ်မယ့် assigned_to တူမတူ မှတ်ထားရန်
    $oldAssigneeId = $task->assigned_to;
    $tenantId = $user->tenant_id ?? 1; // tenant_id လိုအပ်လို့ ယူထားပါတယ်

    // Data အကုန်လုံးကို အရင် သိမ်းရန် (သို့မဟုတ် $validated သုံးရန်)
    $data = [
        'title' => $request->title,
        'description' => $request->description,
        'status' => $request->status,
        'assigned_to' => $request->assigned_to,
        'priority' => $request->priority,
        'due_date' => $request->due_date,
    ];

    // ဖိုင်အသစ် တွဲတင်လာပါက
    if ($request->hasFile('attachment')) {
        // ဖိုင်ဟောင်းရှိနေရင် Storage ထဲကနေ ဖျက်ပစ်မည်
        if ($task->attachment && \Illuminate\Support\Facades\Storage::disk('public')->exists($task->attachment)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($task->attachment);
        }

        // ဖိုင်အသစ်ကို သိမ်းမည်
        $path = $request->file('attachment')->store('attachments', 'public');
        $data['attachment'] = $path;
    }

    $task->update($data);

    // 🔥 Activity Log ကို ဒီနေရာမှာ ထည့်ပေးပါမယ် 🔥
    \App\Models\ActivityLog::create([
        'tenant_id' => $tenantId,
        'user_id' => Auth::id(),
        'subject_type' => Task::class,
        'subject_id' => $task->id,
        'action' => 'updated', // Update လုပ်တာမို့ 'updated' လို့ ပေးလိုက်ပါတယ်
        'description' => Auth::user()->name . ' updated task "' . $task->title . '"',
    ]);

    // Assignee အသစ် ထည့်လိုက်တာ (သို့မဟုတ်) ပြောင်းသွားတာ ဖြစ်ပြီး Assignee ရှိနေရင် Notification ပို့မည်
    if ($task->assigned_to && $task->assigned_to !== $oldAssigneeId) {
        $assignee = \App\Models\User::find($task->assigned_to);
        if ($assignee) {
            $assignee->notify(new NewTaskAssigned($task));
        }
    }

    return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
}

    public function updateStatus(Request $request, Task $task)
    {
        $user = Auth::user();

        // Member ဖြစ်နေရင် သူပိုင်တဲ့ Task ရဲ့ Status ကိုပဲ ပြောင်းလို့ရမယ်
        if ($user->isMember() && $task->assigned_to !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'status' => 'required|in:todo,in_progress,completed',
        ]);

        $task->update([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Task status updated successfully.');
    }


    public function destroy(Task $task)
{
    $user = Auth::user();

    // 🚨 Member ဆိုရင် (သို့မဟုတ် Admin/Manager မဟုတ်ဘဲ သူနဲ့မဆိုင်ရင်) Task လုံးဝ ဖျက်လို့မရစေရ
    if ($user->isMember() || (!$user->canViewTeamDashboard() && $task->assigned_to !== $user->id)) {
        abort(403, 'Unauthorized action.');
    }

    $tenantId = $user->tenant_id ?? 1;

    // 🔥 Task မဖျက်ခင် Activity Log အရင်မှတ်မည်
    \App\Models\ActivityLog::create([
        'tenant_id' => $tenantId,
        'user_id' => Auth::id(),
        'subject_type' => Task::class,
        'subject_id' => $task->id,
        'action' => 'deleted',
        'description' => Auth::user()->name . ' deleted task "' . $task->title . '"',
    ]);

    // ဖိုင်တွဲပါခဲ့ရင် Storage ထဲကနေပါ ဖျက်မယ် (ဖိုင်ပါတဲ့ Task ဆိုရင် ဒီ code ထပ်ထည့်နိုင်ပါတယ်)
    if ($task->attachment && \Illuminate\Support\Facades\Storage::disk('public')->exists($task->attachment)) {
        \Illuminate\Support\Facades\Storage::disk('public')->delete($task->attachment);
    }

    $task->delete();
    
    return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
}


public function addComment(Request $request, Task $task)
{
    $request->validate([
        'comment' => ['required', 'string', 'max:1000'],
    ]);

    $user = Auth::user();
    $tenantId = $user->tenant_id ?? 1;

    // Comment အသစ် သိမ်းမည်
    TaskComment::create([
        'task_id' => $task->id,
        'user_id' => $user->id,
        'comment' => $request->comment,
    ]);

    // 🔥 Comment ပေးတာကို Activity Log မှတ်မည်
    \App\Models\ActivityLog::create([
        'tenant_id' => $tenantId,
        'user_id' => $user->id,
        'subject_type' => Task::class,
        'subject_id' => $task->id,
        'action' => 'commented',
        'description' => $user->name . ' commented on task "' . $task->title . '"',
    ]);

    return back()->with('success', 'Comment added successfully.');
}


}