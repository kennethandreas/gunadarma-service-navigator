<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Show current system configuration.
     *
     * Read-only by design: these are .env-backed values (config/ai.php),
     * not a database-backed settings table. For a project this size, that's
     * simpler and safer than building CRUD + cache-invalidation for a
     * single threshold value — changes just need a server restart.
     */
    public function index(): View
    {
        return view('admin.settings.index', [
            'confidenceThreshold' => config('ai.confidence_threshold'),
            'aiServiceUrl' => config('ai.service_url'),
        ]);
    }
}
