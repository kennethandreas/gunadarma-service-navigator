<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Throwable;

class ModelController extends Controller
{
    /**
     * Show model info: type, vectorizer, dataset size, metrics, last
     * trained, confusion matrix. Reads the JSON files train.py/evaluate.py
     * already produce in ml/model/ — no HTTP call to the AI service needed
     * just to display stats.
     */
    public function index(): View
    {
        $metadata = $this->readJson(base_path('ml/model/metadata.json'));
        $evaluation = $this->readJson(base_path('ml/model/evaluation.json'));

        return view('admin.model.index', [
            'metadata' => $metadata,
            'evaluation' => $evaluation,
        ]);
    }

    /**
     * Retrain the model.
     *
     * This calls POST /retrain on the already-running Python API (ml/api.py)
     * instead of spawning a brand new `python train.py` process from PHP.
     *
     * Why: on some Windows machines, a Python process spawned by PHP as a
     * child process crashes on `import asyncio` (WinError 10106) even
     * though the exact same command runs fine from a terminal — a quirk of
     * how that machine's Winsock/process spawning interacts with PHP's
     * proc_open. Reusing the API's HTTP connection sidesteps that failure
     * mode entirely, since predictions already prove that connection works.
     *
     * Trade-off: this means `python api.py` must be running for the
     * "Train / Retrain Model" button to work — the same requirement AI
     * search already has, so it isn't a new constraint in practice.
     */
    public function retrain(): RedirectResponse
    {
        try {
            $response = Http::timeout(120)
                ->post(rtrim(config('ai.service_url'), '/').'/retrain');
        } catch (Throwable $e) {
            return redirect()
                ->route('admin.model.index')
                ->with('error', 'Tidak bisa menghubungi layanan AI. Pastikan `python api.py` sedang berjalan, lalu coba lagi. ('.$e->getMessage().')');
        }

        if (! $response->successful()) {
            $error = $response->json('error') ?? $response->body();

            return redirect()
                ->route('admin.model.index')
                ->with('error', 'Training gagal: '.$error);
        }

        return redirect()
            ->route('admin.model.index')
            ->with('success', 'Model berhasil dilatih ulang.');
    }

    /**
     * @return array<string, mixed>|null
     */
    private function readJson(string $path): ?array
    {
        if (! file_exists($path)) {
            return null;
        }

        $decoded = json_decode(file_get_contents($path), true);

        return is_array($decoded) ? $decoded : null;
    }
}
