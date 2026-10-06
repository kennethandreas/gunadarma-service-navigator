<?php

return [

    /*
    |--------------------------------------------------------------------------
    | AI Intent Classification
    |--------------------------------------------------------------------------
    |
    | Used from Phase 15/16 onward, when search results start coming from the
    | TF-IDF + Naive Bayes model. Defined now so the admin dashboard (Phase 9)
    | can already report a real "low confidence" threshold instead of a
    | hardcoded number.
    |
    | 0.25 (not the more obvious-looking 0.5+) is a deliberate, tested choice:
    | with this dataset size, MultinomialNB's predict_proba for a *correct*
    | match typically lands around 0.4-0.7 (short quick-search-chip-style
    | queries included), while genuinely irrelevant questions score 0.10-0.23.
    | 0.25 sits comfortably in the gap between those two groups based on real
    | testing — a higher threshold like 0.6 previously rejected every
    | quick-search chip's own label as "not confident enough", which made the
    | feature look broken. Retrain/re-test (ml/evaluate.py) if the dataset
    | grows enough to change this balance.
    |
    | Preprocessing note: ml/preprocessing.py stems words and strips Indonesian
    | stopwords (Sastrawi) before TF-IDF, which raised test accuracy from
    | ~92% to ~96% and widened the confidence gap above versus the original
    | lowercase-only preprocessing.
    |
    */

    'confidence_threshold' => (float) env('AI_CONFIDENCE_THRESHOLD', 0.25),

    'service_url' => env('AI_SERVICE_URL', 'http://127.0.0.1:5000'),

    /*
    |--------------------------------------------------------------------------
    | Training Dataset Path
    |--------------------------------------------------------------------------
    |
    | The admin "Dataset" page (Admin\DatasetController) reads/writes this CSV
    | directly — it's the exact same file train.py/evaluate.py load via
    | pandas, so editing a row here and clicking "Retrain" on the Model AI
    | page has the same effect as hand-editing the CSV and running
    | `python train.py`. Configurable so feature tests can point this at a
    | throwaway fixture file instead of mutating the real curated dataset.
    |
    */

    'dataset_path' => env('AI_DATASET_PATH', base_path('ml/dataset/intent_dataset.csv')),

];
