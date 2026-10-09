<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">
                @if(Auth::user()->canViewTeamDashboard())
                    Dashboard
                @else
                    My Dashboard
                @endif
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                @if(Auth::user()->canViewTeamDashboard())
                    Overview of your team's tasks and performance
                @else
                    Your assigned tasks at a glance
                @endif
            </p>
        </div>
    </x-slot>

    <x-alert />

    @if(Auth::user()->canViewTeamDashboard())
        {{-- Admin / Manager Dashboard --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
            <x-stat-card label="Total Tasks" :value="$totalTasks" color="indigo"
                icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            <x-stat-card label="Completed Tasks" :value="$completedTasks" color="green"
                icon="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            <x-stat-card label="In Progress" :value="$inProgressTasks" color="amber"
                icon="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            <x-stat-card label="Total Users" :value="$totalUsers" color="purple"
                icon="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            {{-- Recent Tasks --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                    <h2 class="text-base font-semibold text-slate-900">Recent Tasks</h2>
                    <a href="{{ route('tasks.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">View all →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                <th class="px-6 py-3">Title</th>
                                <th class="px-6 py-3">Assignee</th>
                                <th class="px-6 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($recentTasks as $task)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-3.5 text-sm font-medium text-slate-900">
                                        <a href="{{ route('tasks.show', $task) }}" class="hover:text-indigo-600">{{ $task->title }}</a>
                                    </td>
                                    <td class="px-6 py-3.5 text-sm text-slate-500">{{ $task->assignee->name ?? 'Unassigned' }}</td>
                                    <td class="px-6 py-3.5"><x-status-badge :status="$task->status"/></td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-6 py-8 text-center text-sm text-slate-400">No tasks yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Team Summary --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h2 class="text-base font-semibold text-slate-900">Team Performance</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                <th class="px-6 py-3">Member</th>
                                <th class="px-6 py-3 text-center">Assigned</th>
                                <th class="px-6 py-3 text-center">Done</th>
                                <th class="px-6 py-3 text-center">Pending</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($teamMembers as $member)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-3.5">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold">
                                                {{ strtoupper(substr($member->name, 0, 1)) }}
                                            </div>
                                            <span class="text-sm font-medium text-slate-900">{{ $member->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-3.5 text-sm text-center text-slate-600">{{ $member->total_tasks }}</td>
                                    <td class="px-6 py-3.5 text-sm text-center font-semibold text-emerald-600">{{ $member->completed_tasks }}</td>
                                    <td class="px-6 py-3.5 text-sm text-center font-semibold text-amber-600">{{ $member->pending_tasks }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-6 py-8 text-center text-sm text-slate-400">No team members found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @else
        {{-- Member Dashboard --}}
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 rounded-xl p-6 mb-8 text-white shadow-lg shadow-indigo-500/20">
            <h2 class="text-lg font-bold">Welcome back, {{ Auth::user()->name }}!</h2>
            <p class="text-indigo-200 text-sm mt-1">Here's a summary of your assigned tasks.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
            <x-stat-card label="My Total Tasks" :value="$myTotal" color="indigo"
                icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            <x-stat-card label="To Do" :value="$myTodo" color="slate"
                icon="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            <x-stat-card label="In Progress" :value="$myInProgress" color="amber"
                icon="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            <x-stat-card label="Completed" :value="$myCompleted" color="green"
                icon="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="text-base font-semibold text-slate-900">My Tasks</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Title</th>
                            <th class="px-6 py-3">Priority</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($myTasks as $task)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-3.5 text-sm font-medium text-slate-900">{{ $task->title }}</td>
                                <td class="px-6 py-3.5">
                                    @if($task->priority === 'high')
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-700">High</span>
                                    @elseif($task->priority === 'medium')
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-700">Medium</span>
                                    @else
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-600">Low</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5"><x-status-badge :status="$task->status"/></td>
                                <td class="px-6 py-3.5 text-right">
                                    <a href="{{ route('tasks.show', $task) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-8 text-center text-sm text-slate-400">No tasks assigned to you yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-app-layout>
