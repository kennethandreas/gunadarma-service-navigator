<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SearchHistory;
use App\Services\AiClient;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Show the public homepage and, if a question was submitted, the best
     * matching service.
     *
     * Matching order:
     *   1. Ask the AI service (ml/api.py) for an intent + confidence.
     *      - If confidence is below config('ai.confidence_threshold'), we
     *        deliberately don't show a result — see Phase 16 / spec section 9
     *        ("jangan memaksakan hasil"). The view renders a distinct
     *        "belum yakin" message for this case.
     *   2. If the AI is unreachable at all, fall back to plain keyword
     *      search (Phase 11) so the page still works without the Python
     *      API running.
     *
     * Every search attempt is logged to search_histories (Phase 17) —
     * including low-confidence and not-found ones, since those are exactly
     * what the admin dashboard's "confidence rendah" stat and intent
     * distribution chart need. No user identity is stored anywhere.
     */
    public function index(Request $request, AiClient $aiClient, SearchService $keywordSearch): View
    {
        $query = trim((string) $request->query('q', ''));
        $searched = $query !== '';
        $threshold = config('ai.confidence_threshold');

        $categories = Category::active()
            ->orderBy('name')
            ->take(6)
            ->get();

        $result = null;
        $confidence = null;
        $source = null;
        $lowConfidence = false;
        $predictedCategoryId = null;

        if ($searched) {
            $prediction = $aiClient->classify($query);

            if ($prediction) {
                $source = 'ai';
                $confidence = $prediction['confidence'];

                $category = Category::where('slug', $prediction['intent'])->first();
                $predictedCategoryId = $category?->id;

                if ($confidence >= $threshold && $category && $category->status) {
                    $result = $category->services()->active()->first();
                } else {
                    $lowConfidence = $confidence < $threshold;
                }
            } else {
                $source = 'keyword';
                $result = $keywordSearch->search($query);
                $predictedCategoryId = $result?->category_id;
            }

            SearchHistory::create([
                'question' => $query,
                'predicted_category_id' => $predictedCategoryId,
                'confidence' => $source === 'ai' ? $confidence : null,
                'service_id' => $result?->id,
            ]);
        }

        return view('welcome', [
            'categories' => $categories,
            'query' => $query,
            'searched' => $searched,
            'result' => $result,
            'confidence' => $confidence,
            'source' => $source,
            'lowConfidence' => $lowConfidence,
            'threshold' => $threshold,
        ]);
    }
}
