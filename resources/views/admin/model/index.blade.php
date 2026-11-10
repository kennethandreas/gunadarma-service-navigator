@extends('layouts.admin')

@section('title', 'Model AI')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="heading-2">Model AI</h2>
            <p class="mt-1 text-body-muted">TF-IDF + Multinomial Naive Bayes untuk klasifikasi intent.</p>
        </div>
        <form method="POST" action="{{ route('admin.model.retrain') }}" data-loading-form>
            @csrf
            <button type="submit" class="btn-primary">
                {{ $metadata ? 'Train / Retrain Model' : 'Train Model' }}
            </button>
        </form>
    </div>

    @if (! $metadata)
        <x-alert variant="warning" class="mt-6">
            <p class="font-medium">Model belum pernah dilatih</p>
            <p class="mt-1 text-warning/90">Klik "Train Model" di atas, atau jalankan <code>python train.py</code> di folder <code>ml</code>.</p>
        </x-alert>
    @else
        <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="card p-5">
                <p class="text-sm text-text-muted">Model</p>
                <p class="mt-1 text-lg font-semibold text-text">{{ $metadata['model'] }}</p>
            </div>
            <div class="card p-5">
                <p class="text-sm text-text-muted">Vectorizer</p>
                <p class="mt-1 text-lg font-semibold text-text">{{ $metadata['vectorizer'] }}</p>
            </div>
            <div class="card p-5">
                <p class="text-sm text-text-muted">Dataset</p>
                <p class="mt-1 text-lg font-semibold text-text">{{ $metadata['dataset_size'] }} baris</p>
                <p class="text-xs text-text-muted">{{ $metadata['num_intents'] }} intent</p>
            </div>
            <div class="card p-5">
                <p class="text-sm text-text-muted">Terakhir Dilatih</p>
                <p class="mt-1 text-lg font-semibold text-text">{{ date('d M Y, H:i', strtotime($metadata['trained_at'])) }}</p>
            </div>
        </div>

        <div class="mt-4 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="card p-5">
                <p class="text-sm text-text-muted">Accuracy</p>
                <p class="mt-1 text-2xl font-semibold text-primary">{{ number_format(($evaluation['accuracy'] ?? $metadata['test_accuracy']) * 100, 1) }}%</p>
            </div>
            <div class="card p-5">
                <p class="text-sm text-text-muted">Precision</p>
                <p class="mt-1 text-2xl font-semibold text-text">{{ isset($evaluation['macro_avg']) ? number_format($evaluation['macro_avg']['precision'] * 100, 1).'%' : '—' }}</p>
            </div>
            <div class="card p-5">
                <p class="text-sm text-text-muted">Recall</p>
                <p class="mt-1 text-2xl font-semibold text-text">{{ isset($evaluation['macro_avg']) ? number_format($evaluation['macro_avg']['recall'] * 100, 1).'%' : '—' }}</p>
            </div>
            <div class="card p-5">
                <p class="text-sm text-text-muted">F1-score</p>
                <p class="mt-1 text-2xl font-semibold text-text">{{ isset($evaluation['macro_avg']) ? number_format($evaluation['macro_avg']['f1_score'] * 100, 1).'%' : '—' }}</p>
            </div>
        </div>

        @if ($evaluation)
            <div class="card mt-6 overflow-hidden">
                <div class="border-b border-border px-5 py-4">
                    <h3 class="heading-3">Precision / Recall / F1 per Intent</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-border bg-background text-xs uppercase tracking-wide text-text-muted">
                            <tr>
                                <th class="px-5 py-3 font-medium">Intent</th>
                                <th class="px-5 py-3 font-medium">Precision</th>
                                <th class="px-5 py-3 font-medium">Recall</th>
                                <th class="px-5 py-3 font-medium">F1-score</th>
                                <th class="px-5 py-3 font-medium">Data Uji</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach ($evaluation['per_intent'] as $intent => $row)
                                <tr>
                                    <td class="px-5 py-3 font-medium text-text">{{ $intent }}</td>
                                    <td class="px-5 py-3 text-text-muted">{{ number_format($row['precision'] * 100, 0) }}%</td>
                                    <td class="px-5 py-3 text-text-muted">{{ number_format($row['recall'] * 100, 0) }}%</td>
                                    <td class="px-5 py-3 text-text-muted">{{ number_format($row['f1_score'] * 100, 0) }}%</td>
                                    <td class="px-5 py-3 text-text-muted">{{ $row['support'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card mt-6 overflow-hidden">
                <div class="border-b border-border px-5 py-4">
                    <h3 class="heading-3">Confusion Matrix</h3>
                    <p class="mt-1 text-sm text-text-muted">Baris = aktual, kolom = prediksi.</p>
                </div>
                <div class="overflow-x-auto p-5">
                    <table class="text-center text-xs">
                        <thead>
                            <tr>
                                <th class="px-2 py-1"></th>
                                @foreach ($evaluation['labels'] as $label)
                                    <th class="px-2 py-1 font-medium text-text-muted">{{ Str::limit($label, 10, '') }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($evaluation['confusion_matrix'] as $i => $row)
                                <tr>
                                    <th class="whitespace-nowrap px-2 py-1 text-right font-medium text-text-muted">{{ $evaluation['labels'][$i] }}</th>
                                    @foreach ($row as $j => $value)
                                        <td class="px-2 py-1 {{ $i === $j ? 'bg-success-light font-semibold text-success' : ($value > 0 ? 'bg-error-light text-error' : 'text-text-muted') }}">
                                            {{ $value }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endif
@endsection
