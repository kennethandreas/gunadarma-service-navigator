<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class AiClient
{
    /**
     * Ask the Python intent-classification API (ml/api.py) what intent a
     * free-text question maps to.
     *
     * Returns null — rather than throwing — whenever the AI service can't
     * be reached or replies with something unexpected, so callers can fall
     * back to keyword search instead of breaking the page. This is a
     * separate concern from the confidence *threshold* fallback (Phase 16):
     * this is "is the AI even reachable", that one is "do we trust what it said".
     *
     * @return array{intent: string, confidence: float}|null
     */
    public function classify(string $question): ?array
    {
        try {
            $response = Http::timeout(3)
                ->post(rtrim(config('ai.service_url'), '/').'/predict', [
                    'question' => $question,
                ]);

            if (! $response->successful()) {
                Log::warning('AI service returned an error response.', [
                    'status' => $response->status(),
                ]);

                return null;
            }

            $data = $response->json();

            if (! isset($data['intent'], $data['confidence'])) {
                Log::warning('AI service returned an unexpected payload.', ['data' => $data]);

                return null;
            }

            return [
                'intent' => (string) $data['intent'],
                'confidence' => (float) $data['confidence'],
            ];
        } catch (Throwable $e) {
            Log::warning('AI service unreachable: '.$e->getMessage());

            return null;
        }
    }
}
