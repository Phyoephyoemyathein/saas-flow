<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Tasks</h1>
                <p class="text-sm text-slate-500 mt-1">Manage and track all team tasks</p>
            </div>
            
            {{-- Admin သို့မဟုတ် Manager မှသာ New Task ခလုတ် မြင်ရမည် --}}
            @if(Auth::user()->canViewTeamDashboard())
                <a href="{{ route('tasks.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Task
                </a>
            @endif
        </div>
    </x-slot>

    <x-alert />

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        {{-- Filters --}}
        <div class="p-4 border-b border-slate-100">
            <form method="GET" action="{{ route('tasks.index') }}" class="flex flex-col lg:flex-row gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tasks..."
                       class="flex-1 rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <select name="status" class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Status</option>
                    <option value="todo" {{ request('status') == 'todo' ? 'selected' : '' }}>To Do</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>

                {{-- Member တွေအတွက် All Members filter ကို ဖွက်ထားမည် (Admin/Manager သာ သုံးနိုင်မည်) --}}
                @if(!Auth::user()->isMember())
                    <select name="assigned_to" class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Members</option>
                        @foreach(\App\Models\User::where('tenant_id', Auth::user()->tenant_id ?? 1)->get() as $member)
                            <option value="{{ $member->id }}" {{ request('assigned_to') == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                        @endforeach
                    </select>
                @endif

                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-lg transition-colors">Filter</button>
                    @if(request('search') || request('status') || request('assigned_to'))
                        <a href="{{ route('tasks.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-lg transition-colors">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider bg-slate-50">
                        <th class="px-6 py-3">Title</th>
                        <th class="px-6 py-3">Assignee</th>
                        <th class="px-6 py-3">Priority</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Due Date</th>
                        <th class="px-6 py-3">Attachment</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($tasks as $task)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <a href="{{ route('tasks.show', $task) }}" class="text-sm font-medium text-slate-900 hover:text-indigo-600">{{ $task->title }}</a>
                                @if($task->description)
                                    <p class="text-xs text-slate-400 mt-0.5 truncate max-w-xs">{{ $task->description }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $task->assignee->name ?? 'Unassigned' }}</td>
                            <td class="px-6 py-4">
                                @if($task->priority === 'high')
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-700">High</span>
                                @elseif($task->priority === 'medium')
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-700">Medium</span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-600">{{ ucfirst($task->priority ?? 'low') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('tasks.updateStatus', $task) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <select name="status" onchange="this.form.submit()"
                                            class="text-xs font-semibold rounded-lg px-2 py-1 border-0 cursor-pointer
                                            @if($task->status == 'completed') bg-emerald-100 text-emerald-700
                                            @elseif($task->status == 'in_progress') bg-amber-100 text-amber-700
                                            @else bg-slate-100 text-slate-700 @endif">
                                        <option value="todo" {{ $task->status == 'todo' ? 'selected' : '' }}>To Do</option>
                                        <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @php
                                    $isOverdue = false;
                                    $formattedDate = '—';
                                    if (!empty($task->due_date)) {
                                        try {
                                            $parsedDate = \Carbon\Carbon::parse($task->due_date);
                                            $formattedDate = $parsedDate->format('M d, Y');
                                            $isOverdue = $parsedDate->isPast() && $task->status != 'completed';
                                        } catch (\Exception $e) {
                                            $formattedDate = $task->due_date;
                                        }
                                    }
                                @endphp
                                <span class="{{ $isOverdue ? 'text-red-600 font-semibold' : 'text-slate-500' }}">{{ $formattedDate }}</span>
                                @if($isOverdue)
                                    <span class="ml-1 text-xs bg-red-100 text-red-700 px-1.5 py-0.5 rounded font-semibold">Overdue</span>
                                @endif
                            </td>

                            {{-- Attachment View/Download Link --}}
                            <td class="px-6 py-4 text-sm">
                                @if($task->attachment)
                                    <a href="{{ asset('storage/' . $task->attachment) }}" target="_blank" 
                                       class="inline-flex items-center gap-1 text-xs font-semibold bg-indigo-50 text-indigo-700 px-2.5 py-1.5 rounded-lg hover:bg-indigo-100 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                        </svg>
                                        View File
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400">No File</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    @if(Auth::user()->canViewTeamDashboard() || $task->assigned_to === Auth::id())
                                        <a href="{{ route('tasks.edit', $task) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Edit</a>
                                    @endif

                                    @if(Auth::user()->canViewTeamDashboard())
                                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Delete this task?');" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-800">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-12 text-center text-sm text-slate-400">No tasks found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tasks->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">{{ $tasks->links() }}</div>
        @endif
    </div>

    {{-- 🔥 Recent System Activities Section --}}
    <div class="mt-8 bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h3 class="text-base font-semibold text-slate-900 border-b border-slate-100 pb-3 mb-4">Recent System Activities</h3>
        
        <ul class="divide-y divide-slate-100">
            @forelse($activities ?? [] as $log)
                <li class="py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                    <p class="text-sm text-slate-700">{{ $log->description }}</p>
                    <span class="text-xs text-slate-400 whitespace-nowrap">{{ $log->created_at->diffForHumans() }}</span>
                </li>
            @empty
                <li class="py-3 text-center text-sm text-slate-400">No activities recorded yet.</li>
            @endforelse
        </ul>
    </div>
</x-app-layout>