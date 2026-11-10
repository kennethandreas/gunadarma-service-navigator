# Gunadarma Academic Service Navigator

**Final Project (Undergraduate Thesis) — Universitas Gunadarma**

A web application that helps students find the right academic service by asking questions in natural
Indonesian instead of guessing exact keywords. A machine-learning intent classifier (TF-IDF + Multinomial
Naive Bayes) maps free-text questions such as *"Saya mau cek jadwal kuliah"* to the relevant service,
while a Laravel application provides the service directory and an admin dashboard for managing content,
training data and the model itself.

![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white)
![Python](https://img.shields.io/badge/Python-3.10+-3776AB?logo=python&logoColor=white)
![scikit-learn](https://img.shields.io/badge/scikit--learn-1.4+-F7931E?logo=scikitlearn&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?logo=tailwindcss&logoColor=white)
![Accuracy](https://img.shields.io/badge/model_accuracy-96.2%25-brightgreen)

---

## Problem & Approach

Academic information at large universities is spread across many units and pages, so students often do
not know which service handles their need. Keyword search fails when the wording differs from the
official service name. This project treats search as an **intent classification** problem:

1. **Preprocessing** — lowercasing, cleanup, Indonesian stopword removal and stemming (Sastrawi).
2. **Feature extraction** — TF-IDF vectorization.
3. **Classification** — Multinomial Naive Bayes across 10 academic intents (KRS, grades, schedules,
   tuition payment, graduation, thesis defense registration, academic letters, and more).
4. **Decision** — predictions above a tuned confidence threshold (0.25) return the matching services;
   anything below falls back to keyword search.

## Results

| Metric | Value |
|---|---|
| Dataset | 390 labeled questions, 10 intents |
| Test accuracy | **96.2%** (79 held-out samples) |
| Improvement from Sastrawi preprocessing | ~92% → ~96% |
| Automated tests (Laravel) | 59 feature & unit tests |

The confidence threshold was chosen empirically: correct matches typically score 0.4–0.7 while
unrelated questions score 0.10–0.23 (see `config/ai.php` for the full rationale). Evaluation output
including the confusion matrix is stored in `ml/model/evaluation.json`.

## Features

**Public site**
- Natural-language search with intent label and confidence score.
- Service directory by category, service detail pages and quick-search suggestions.
- Graceful degradation: if the AI service is offline, search silently switches to keyword matching.

**Admin dashboard**
- CRUD for categories and services.
- Search history with low-confidence filtering and CSV export, used to discover gaps in the dataset.
- Training dataset management (add, edit, delete, filter by intent, class distribution statistics).
- Model dashboard: accuracy, per-class metrics, confusion matrix and one-click retraining.

## Architecture

```
 Browser ──► Laravel 12 (Blade + Tailwind)
               ├── SearchService ── HTTP ──► Flask API (ml/api.py) ──► TF-IDF + Naive Bayes
               │        └── fallback: keyword search (MySQL)
               └── Admin ──► dataset CSV ◄── train.py / evaluate.py
```

```
app/Services/      SearchService (AI + keyword fallback), AiClient (HTTP client to the ML API)
app/Http/          Public and admin controllers
ml/                preprocessing.py, train.py, evaluate.py, predict.py, api.py
ml/dataset/        intent_dataset.csv
ml/model/          trained model, vectorizer, metadata and evaluation report
database/          migrations, seeders, skema.sql (MySQL dump of the schema and seed data)
deploy/            InfinityFree + PythonAnywhere deployment files
```

## Tech Stack

| Layer | Technology |
|---|---|
| Web application | PHP 8.2, Laravel 12, Blade, Tailwind CSS 4, Vite 7 |
| Database | MySQL / MariaDB |
| Machine learning | Python, scikit-learn, pandas, PySastrawi, joblib |
| ML service | Flask, Gunicorn |
| Testing | PHPUnit |
| Deployment | Docker, InfinityFree + PythonAnywhere |

## Getting Started

**Requirements:** PHP 8.2+, Composer, Node.js, MySQL/MariaDB. Python 3.10+ is only needed for the AI service.

```bash
git clone https://github.com/kennethandreas/gunadarma-service-navigator.git
cd gunadarma-service-navigator

composer install
npm install
cp .env.example .env
php artisan key:generate
```

Create a database named `gunadarma_navigator` (adjust credentials in `.env`), then:

```bash
php artisan migrate --seed
npm run dev
php artisan serve          # http://127.0.0.1:8000
```

Start the AI service in a second terminal (the model is already trained):

```bash
cd ml
pip install -r requirements.txt
python api.py              # http://127.0.0.1:5000
```

Default admin account (from the seeder, for local use only): `admin@gunadarma.ac.id` / `password`.

### Machine-learning scripts

```bash
python train.py                               # retrain from ml/dataset/intent_dataset.csv
python evaluate.py                            # accuracy, precision, recall, F1, confusion matrix
python predict.py "Saya mau cek jadwal kuliah"
```

### Tests

```bash
php artisan test
```

## Deployment

See [`deploy/README.md`](deploy/README.md) for the InfinityFree + PythonAnywhere setup. A `Dockerfile`
is also provided for container platforms.

## Author

**Kenneth Andreas** — Software Engineer  
Undergraduate thesis project: research, dataset construction, model training and evaluation, and
full-stack implementation.

GitHub: [@kennethandreas](https://github.com/kennethandreas)

## License

Released under the [MIT License](LICENSE).
