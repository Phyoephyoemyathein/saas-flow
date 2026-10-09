<?php


namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id ?? 1;

        $query = ActivityLog::where('tenant_id', $tenantId)->latest();

        // 👤 User အလိုက် စစ်ထုတ်ရန် (Filter by User)
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // 📅 နေ့ရက်အလိုက် စစ်ထုတ်ရန် (Filter by Date)
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $activities = $query->paginate(15)->withQueryString();
        $users = User::where('tenant_id', $tenantId)->get();

        return view('activity-logs.index', compact('activities', 'users'));
    }

    // 📥 CSV/Excel ဖြင့် Export ထုတ်ရန်
    public function export(Request $request)
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id ?? 1;

        $query = ActivityLog::where('tenant_id', $tenantId)->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $activities = $query->get();

        $filename = "activity-logs-" . date('Y-m-d') . ".csv";
        
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($activities) {
            $file = fopen('php://output', 'w');
            // CSV Column Headers
            fputcsv($file, ['ID', 'User', 'Action', 'Description', 'Date/Time']);

            foreach ($activities as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user->name ?? 'Unknown',
                    $log->action,
                    $log->description,
                    $log->created_at,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}