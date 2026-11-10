<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DatasetController extends Controller
{
    private const PER_PAGE = 15;

    /**
     * The training dataset (ml/dataset/intent_dataset.csv) is read directly
     * from disk rather than mirrored into MySQL — it's what train.py and
     * evaluate.py already read via pandas, so editing it here and clicking
     * "Retrain" on the Model AI page is the same as editing the CSV by hand
     * and running `python train.py`, just without leaving the browser.
     */
    private function csvPath(): string
    {
        return config('ai.dataset_path');
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $intent = trim((string) $request->query('intent', ''));

        $rows = $this->readRows();

        $filtered = $rows
            ->when($search !== '', fn (Collection $c) => $c->filter(
                fn (array $row) => str_contains(mb_strtolower($row['text']), mb_strtolower($search))
            ))
            ->when($intent !== '', fn (Collection $c) => $c->where('intent', $intent))
            ->values();

        $page = LengthAwarePaginator::resolveCurrentPage();
        $items = $filtered->forPage($page, self::PER_PAGE)->values();

        $paginator = new LengthAwarePaginator(
            $items,
            $filtered->count(),
            self::PER_PAGE,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.dataset.index', [
            'rows' => $paginator,
            'search' => $search,
            'intentFilter' => $intent,
            'total' => $rows->count(),
            'perIntent' => $rows->countBy('intent')->sortDesc(),
            'categories' => Category::orderBy('name')->get(['name', 'slug']),
        ]);
    }

    public function create(): View
    {
        return view('admin.dataset.form', [
            'row' => null,
            'categories' => Category::orderBy('name')->get(['name', 'slug']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $rows = $this->readRows();
        $nextId = $rows->isEmpty() ? 0 : $rows->max('id') + 1;
        $rows->push(['id' => $nextId, 'text' => $data['text'], 'intent' => $data['intent']]);

        $this->writeRows($rows);

        return redirect()
            ->route('admin.dataset.index')
            ->with('success', 'Contoh pertanyaan berhasil ditambahkan ke dataset. Jangan lupa retrain model di halaman Model AI agar perubahan diterapkan.');
    }

    public function edit(int $dataset): View
    {
        $row = $this->findRow($dataset);

        return view('admin.dataset.form', [
            'row' => $row,
            'categories' => Category::orderBy('name')->get(['name', 'slug']),
        ]);
    }

    public function update(Request $request, int $dataset): RedirectResponse
    {
        $this->findRow($dataset);
        $data = $this->validated($request);

        $rows = $this->readRows()->map(function (array $row) use ($dataset, $data) {
            if ($row['id'] === $dataset) {
                $row['text'] = $data['text'];
                $row['intent'] = $data['intent'];
            }

            return $row;
        });

        $this->writeRows($rows);

        return redirect()
            ->route('admin.dataset.index')
            ->with('success', 'Contoh pertanyaan berhasil diperbarui. Jangan lupa retrain model di halaman Model AI agar perubahan diterapkan.');
    }

    public function destroy(int $dataset): RedirectResponse
    {
        $this->findRow($dataset);

        $rows = $this->readRows()->reject(fn (array $row) => $row['id'] === $dataset)->values();

        $this->writeRows($rows);

        return redirect()
            ->route('admin.dataset.index')
            ->with('success', 'Contoh pertanyaan berhasil dihapus dari dataset. Jangan lupa retrain model di halaman Model AI agar perubahan diterapkan.');
    }

    /**
     * Download the full dataset as CSV — handy as a backup before making
     * bulk edits, or for pasting rows into the thesis's data/methodology
     * chapter.
     */
    public function export(): StreamedResponse
    {
        $filename = 'intent-dataset-'.now()->format('Y-m-d-His').'.csv';

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['text', 'intent']);

            foreach ($this->readRows() as $row) {
                fputcsv($handle, [$row['text'], $row['intent']]);
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function findRow(int $id): array
    {
        $row = $this->readRows()->firstWhere('id', $id);

        abort_if($row === null, 404);

        return $row;
    }

    /**
     * @return array{text: string, intent: string}
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'text' => ['required', 'string', 'max:500'],
            'intent' => ['required', 'string', Rule::in(Category::pluck('slug')->all())],
        ]);
    }

    /**
     * @return Collection<int, array{id: int, text: string, intent: string}>
     */
    private function readRows(): Collection
    {
        $path = $this->csvPath();

        if (! file_exists($path)) {
            return collect();
        }

        $handle = fopen($path, 'r');
        fgetcsv($handle); // skip header row

        $rows = collect();
        $id = 0;

        while (($data = fgetcsv($handle)) !== false) {
            if ($data === [null] || $data === false) {
                continue;
            }

            $rows->push([
                'id' => $id++,
                'text' => $data[0] ?? '',
                'intent' => $data[1] ?? '',
            ]);
        }

        fclose($handle);

        return $rows;
    }

    /**
     * Rewrites the whole CSV file from the given rows. The dataset is small
     * (a few hundred rows) so a full rewrite on every add/edit/delete is
     * simple and safe — no partial-write corruption risk.
     */
    private function writeRows(Collection $rows): void
    {
        $handle = fopen($this->csvPath(), 'w');
        fputcsv($handle, ['text', 'intent']);

        foreach ($rows as $row) {
            fputcsv($handle, [$row['text'], $row['intent']]);
        }

        fclose($handle);
    }
}
