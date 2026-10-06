<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * Show the public service directory (/layanan), grouped by category.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $categories = Category::active()
            ->with(['services' => function ($query) use ($search) {
                $query->active()->orderBy('name');

                if ($search !== '') {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");
                    });
                }
            }])
            ->orderBy('name')
            ->get()
            ->filter(fn (Category $category) => $category->services->isNotEmpty())
            ->values();

        return view('services.index', [
            'categories' => $categories,
            'search' => $search,
        ]);
    }

    /**
     * Show a single service's detail page.
     *
     * Inactive services 404 on the public site. We check this explicitly
     * here (rather than in Service::resolveRouteBinding) so the same
     * {service:slug} binding can still be reused by admin routes later,
     * where inactive services must remain reachable.
     */
    public function show(Service $service): View
    {
        abort_unless($service->status, 404);

        $service->load('category');

        return view('services.show', [
            'service' => $service,
        ]);
    }
}
