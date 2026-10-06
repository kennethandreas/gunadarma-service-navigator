<?php

namespace App\Services;

use App\Models\Service;
use Illuminate\Support\Collection;

class SearchService
{
    /**
     * A small Indonesian stopword list — just enough to strip common filler
     * words so keyword matching focuses on meaningful terms. This is the
     * "search without AI" version (Phase 11); Phase 15/16 replaces the
     * matching itself with the trained TF-IDF + Naive Bayes model while
     * keeping this same entry point.
     */
    private const STOPWORDS = [
        'saya', 'aku', 'kamu', 'kami', 'kita', 'mau', 'ingin', 'mohon', 'tolong',
        'yang', 'untuk', 'dengan', 'dan', 'atau', 'adalah', 'ada', 'apa', 'apakah',
        'bagaimana', 'gimana', 'dimana', 'di', 'ke', 'dari', 'pada', 'ini', 'itu',
        'nya', 'lah', 'kah', 'dong', 'yuk', 'deh', 'sih', 'coba', 'bisa', 'boleh',
        'cara', 'melihat', 'lihat', 'cek', 'mengecek', 'tahu', 'mengetahui', 'info',
        'tentang',
    ];

    /**
     * Find the best-matching active service for a free-text question using
     * simple keyword overlap. Returns null when nothing scores above zero.
     */
    public function search(string $question): ?Service
    {
        $keywords = $this->tokenize($question);

        if ($keywords->isEmpty()) {
            return null;
        }

        $best = Service::active()
            ->with('category')
            ->get()
            ->map(fn (Service $service) => [
                'service' => $service,
                'score' => $this->score($service, $keywords),
            ])
            ->filter(fn (array $row) => $row['score'] > 0)
            ->sortByDesc('score')
            ->first();

        return $best['service'] ?? null;
    }

    /**
     * @return Collection<int, string>
     */
    private function tokenize(string $text): Collection
    {
        $words = preg_split('/[^\p{L}\p{N}]+/u', mb_strtolower($text)) ?: [];

        return collect($words)
            ->filter(fn ($word) => mb_strlen($word) >= 3)
            ->reject(fn ($word) => in_array($word, self::STOPWORDS, true))
            ->unique()
            ->values();
    }

    /**
     * Name matches count double — they're a stronger signal than a keyword
     * appearing somewhere in the description or category name.
     */
    private function score(Service $service, Collection $keywords): int
    {
        $name = mb_strtolower($service->name);
        $rest = mb_strtolower($service->description.' '.($service->category->name ?? ''));

        return $keywords->sum(function (string $keyword) use ($name, $rest) {
            if (str_contains($name, $keyword)) {
                return 2;
            }

            return str_contains($rest, $keyword) ? 1 : 0;
        });
    }
}
