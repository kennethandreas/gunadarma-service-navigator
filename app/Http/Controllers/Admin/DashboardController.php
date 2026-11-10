<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SearchHistory;
use App\Models\Service;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard: totals, intent stats, recent activity.
     */
    public function index(): View
    {
        $threshold = config('ai.confidence_threshold');

        $stats = [
            'total_services' => Service::count(),
            'total_categories' => Category::count(),
            'total_questions' => SearchHistory::count(),
            'low_confidence' => SearchHistory::whereNotNull('confidence')
                ->where('confidence', '<', $threshold)
                ->count(),
        ];

        $intentStats = SearchHistory::query()
            ->whereNotNull('predicted_category_id')
            ->selectRaw('predicted_category_id, count(*) as total')
            ->groupBy('predicted_category_id')
            ->orderByDesc('total')
            ->with('category')
            ->take(8)
            ->get();

        $recentActivity = SearchHistory::with(['category', 'service'])
            ->latest('created_at')
            ->take(8)
            ->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'intentStats' => $intentStats,
            'recentActivity' => $recentActivity,
            'threshold' => $threshold,
        ]);
    }
}
