# Gunadarma Academic Service Navigator

An AI-powered web application that helps Universitas Gunadarma students find the right academic service by asking questions in everyday language, instead of having to guess the exact keyword.

Developed as my **Undergraduate Thesis (Skripsi)**.

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php&logoColor=white)
![Python](https://img.shields.io/badge/Python-3.10+-3776AB?logo=python&logoColor=white)
![scikit-learn](https://img.shields.io/badge/scikit--learn-Naive_Bayes-F7931E?logo=scikitlearn&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?logo=mysql&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?logo=tailwindcss&logoColor=white)

---

## The Problem

Students often don't know which office or service handles their request. A normal keyword search only works when the student types the exact term (for example "KRS"). A question like *"Saya mau cek jadwal kuliah"* ("I want to check my class schedule") would not match anything.

This application understands the **intent** behind the question and points the student to the right service.

## How It Works

```
Student question
      │
      ▼
Laravel app ──HTTP──▶ Python AI service (Flask)
      │                 1. Preprocess: lowercase, Sastrawi stemming, stopword removal
      │                 2. TF-IDF vectorization
      │                 3. Multinomial Naive Bayes → intent + confidence score
      ◀─────────────────┘
      │
      ▼
Matching services shown with the AI confidence score
(falls back to keyword search if the AI service is offline)
```

## Model Performance

| Metric | Score |
|---|---|
| Accuracy | **96.2%** |
| Precision (macro avg) | 96.9% |
| Recall (macro avg) | 96.3% |
| F1-score (macro avg) | 96.3% |

- **Dataset:** 390 example questions in Indonesian across 10 intents
- **Test set:** 79 questions (20% split)
- Adding Indonesian stemming and stopword removal with **Sastrawi** raised accuracy from about **92% to 96%**
- The confidence threshold is set at **0.25**, chosen through testing: correct matches usually score 0.4–0.7, while unrelated questions score 0.10–0.23

**Intents covered:** Class Schedule · Study Plan Card (KRS) · Academic Grades · Tuition Payment · Thesis Defense Registration · Graduation · Academic Administration · Academic Letters · Student Affairs · Course Information

## Features

**For students (public)**
- Natural-language search with AI intent detection and confidence score
- Quick-search chips for common questions
- Service directory grouped by category, with detail pages for each service
- Automatic fallback to keyword search when the AI service is unavailable

**For administrators**
- Dashboard with usage overview
- Category and service management (create, edit, delete)
- Search history with a low-confidence filter and CSV export, to see which questions the AI struggles with
- Training dataset management: add, edit and delete example questions, with search, intent filter and data distribution stats
- AI model page: view evaluation results (accuracy, per-intent scores, confusion matrix) and retrain the model with one click

## Tech Stack

| Layer | Technology |
|---|---|
| Web application | Laravel 12, PHP 8.2+, Blade, Tailwind CSS 4, Vite |
| Database | MySQL / MariaDB |
| AI service | Python, Flask, scikit-learn (TF-IDF + Multinomial Naive Bayes), PySastrawi, pandas, joblib |
| Testing | PHPUnit (59 feature and unit tests) |
| Deployment | Docker, or shared hosting + PythonAnywhere (see `deploy/`) |

## Getting Started

### Requirements
- PHP 8.2 or newer (Laragon or XAMPP is fine)
- Composer
- MySQL or MariaDB
- Node.js and npm
- Python 3.10 or newer (only needed for AI search)

### 1. Run the web application

```bash
git clone https://github.com/<your-username>/gunadarma-service-navigator.git
cd gunadarma-service-navigator

composer install
cp .env.example .env          # Windows: copy .env.example .env
php artisan key:generate
```

Create an empty database named `gunadarma_navigator`, and update the database settings in `.env` if yours are different from the defaults (`127.0.0.1:3306`, user `root`, no password).

```bash
php artisan migrate --seed
npm install
npm run dev
php artisan serve
```

Open `http://127.0.0.1:8000`.

> The seeder creates a default admin account (see `database/seeders/DatabaseSeeder.php`). **Change the password right after your first login.**

### 2. Turn on AI search (optional)

The model is already trained and saved in `ml/model/`. Run the AI service in a separate terminal and keep it running next to `php artisan serve`:

```bash
cd ml
pip install -r requirements.txt
python api.py                 # runs on http://127.0.0.1:5000
```

If the AI service is not running, the website still works and simply uses keyword search.

### Other Python commands

```bash
python train.py                                  # retrain the model from the dataset
python evaluate.py                               # accuracy, precision, recall, F1 + confusion matrix
python predict.py "Saya mau cek jadwal kuliah"   # test a single prediction
```

### AI service endpoints

| Method | Endpoint | Description |
|---|---|---|
| GET | `/health` | Health check |
| POST | `/predict` | Body `{ "question": "..." }` → returns intent and confidence |
| POST | `/retrain` | Retrains and re-evaluates the model |

## Running Tests

```bash
php artisan test
```

## Project Structure

```
app/
├── Http/Controllers/        Public pages and admin controllers
├── Models/                  Category, Service, SearchHistory, User
└── Services/                SearchService (keyword + AI), AiClient (HTTP client)
config/ai.php                Confidence threshold, AI service URL, dataset path
database/                    Migrations, seeders, factories
ml/
├── dataset/                 intent_dataset.csv (390 examples, 10 intents)
├── model/                   Trained model, vectorizer, metadata and evaluation
├── preprocessing.py         Sastrawi stemming and stopword removal
├── train.py / evaluate.py / predict.py
└── api.py                   Flask REST API used by Laravel
resources/                   Blade views, Tailwind CSS, JavaScript
routes/                      web.php (public), admin.php (admin panel)
tests/                       Feature and unit tests
deploy/                      Deployment files for shared hosting + PythonAnywhere
```

## Configuration

| Variable | Default | Description |
|---|---|---|
| `AI_SERVICE_URL` | `http://127.0.0.1:5000` | URL of the Python AI service |
| `AI_CONFIDENCE_THRESHOLD` | `0.25` | Minimum confidence to accept an AI prediction |
| `AI_DATASET_PATH` | `ml/dataset/intent_dataset.csv` | Training dataset used by the admin dataset page |

## Academic Context

This application was developed as an **Undergraduate Thesis (Skripsi)** at Universitas Gunadarma. The work was completed in 21 development phases, from database design and the admin panel to model training, evaluation, testing and UI refinement.
