<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SearchHistory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SearchHistoryController extends Controller
{
    /**
     * List every search a public visitor has made — read-only, no
     * identifying information is stored (see search_histories migration).
     */
    public function index(Request $request): View
    {
        $threshold = config('ai.confidence_threshold');
        $lowConfidenceOnly = $request->boolean('low_confidence');

        $histories = $this->filteredQuery($lowConfidenceOnly, $threshold)
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.search-histories.index', [
            'histories' => $histories,
            'lowConfidenceOnly' => $lowConfidenceOnly,
            'threshold' => $threshold,
        ]);
    }

    /**
     * Download the same (optionally filtered) list as a CSV file — handy
     * for pulling raw data into the thesis's results/data analysis chapter
     * without needing direct database access.
     *
     * Streamed rather than built in memory first, so this doesn't choke on
     * a search history table that's grown large by the time of the sidang.
     */
    public function export(Request $request): StreamedResponse
    {
        $threshold = config('ai.confidence_threshold');
        $lowConfidenceOnly = $request->boolean('low_confidence');

        $filename = 'riwayat-pertanyaan-'.now()->format('Y-m-d-His').'.csv';

        $callback = function () use ($lowConfidenceOnly, $threshold) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM so Excel opens the file with correct encoding
            // instead of mangling accented/emoji characters.
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Pertanyaan', 'Intent', 'Confidence (%)', 'Layanan', 'Tanggal']);

            $this->filteredQuery($lowConfidenceOnly, $threshold)
                ->chunkById(200, function ($chunk) use ($handle) {
                    foreach ($chunk as $history) {
                        fputcsv($handle, [
                            $history->question,
                            $history->category->name ?? 'Tidak dikenali',
                            is_null($history->confidence) ? '' : number_format($history->confidence * 100, 0),
                            $history->service->name ?? '',
                            $history->created_at->format('Y-m-d H:i:s'),
                        ]);
                    }
                });

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Shared base query for both the paginated index view and the CSV
     * export, so the "low confidence only" filter always means the same
     * thing in both places.
     */
    private function filteredQuery(bool $lowConfidenceOnly, float $threshold)
    {
        return SearchHistory::with(['category', 'service'])
            ->when(
                $lowConfidenceOnly,
                fn ($query) => $query->whereNotNull('confidence')->where('confidence', '<', $threshold)
            );
    }
}
