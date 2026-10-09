<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id ?? 1;

        if ($user->canViewTeamDashboard()) {
            $totalTasks = Task::where('tenant_id', $tenantId)->count();
            $completedTasks = Task::where('tenant_id', $tenantId)->where('status', 'completed')->count();
            $inProgressTasks = Task::where('tenant_id', $tenantId)->where('status', 'in_progress')->count();
            $todoTasks = Task::where('tenant_id', $tenantId)->where('status', 'todo')->count();
            $totalUsers = User::where('tenant_id', $tenantId)->count();

            $recentTasks = Task::where('tenant_id', $tenantId)
                ->with('assignee')
                ->latest()
                ->take(5)
                ->get();

            $teamMembers = User::where('tenant_id', $tenantId)->withCount([
                'tasks as total_tasks',
                'tasks as completed_tasks' => fn ($query) => $query->where('status', 'completed'),
                'tasks as pending_tasks' => fn ($query) => $query->where('status', '!=', 'completed'),
            ])->get();

            return view('dashboard.index', compact(
                'totalTasks',
                'completedTasks',
                'inProgressTasks',
                'todoTasks',
                'totalUsers',
                'recentTasks',
                'teamMembers'
            ));
        }

        $myTasks = Task::where('assigned_to', $user->id)->latest()->get();
        $myTotal = $myTasks->count();
        $myCompleted = $myTasks->where('status', 'completed')->count();
        $myInProgress = $myTasks->where('status', 'in_progress')->count();
        $myTodo = $myTasks->where('status', 'todo')->count();

        return view('dashboard.index', compact(
            'myTasks',
            'myTotal',
            'myCompleted',
            'myInProgress',
            'myTodo'
        ));
    }
}
