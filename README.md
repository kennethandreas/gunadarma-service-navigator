<div align="center">

# Gunadarma Academic Service Navigator

**Ask in plain Indonesian, get pointed to the right campus service.**

An AI-powered web application that helps Universitas Gunadarma students find the academic service they need by understanding the *intent* behind their question, rather than relying on exact keywords.

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php&logoColor=white)
![Python](https://img.shields.io/badge/Python-3.10+-3776AB?logo=python&logoColor=white)
![scikit-learn](https://img.shields.io/badge/scikit--learn-Naive_Bayes-F7931E?logo=scikitlearn&logoColor=white)
![Flask](https://img.shields.io/badge/Flask-3-000000?logo=flask&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?logo=mysql&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?logo=tailwindcss&logoColor=white)

*Undergraduate Thesis (Skripsi) — Universitas Gunadarma*

</div>

---

## Table of Contents

- [Overview](#overview)
- [How It Works](#how-it-works)
- [Model Performance](#model-performance)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Getting Started](#getting-started)
- [Configuration](#configuration)
- [AI Service API](#ai-service-api)
- [Testing](#testing)
- [Deployment](#deployment)
- [Project Structure](#project-structure)
- [Academic Context](#academic-context)
- [Author](#author)

## Overview

Students often don't know which office or service handles their request. A conventional keyword search only works when the student already knows the right term (for example *"KRS"*). A natural question such as *"Saya mau cek jadwal kuliah"* ("I want to check my class schedule") would return nothing.

This application classifies the **intent** of a free-text question with a machine-learning model and directs the student to the matching service, together with a confidence score. When the AI service is unavailable, the app automatically falls back to keyword search, so it keeps working.

## How It Works

```
 Student question
        │
        ▼
 ┌──────────────┐   HTTP    ┌──────────────────────────────────────────┐
 │  Laravel app │ ────────▶ │  Python AI service (Flask)               │
 │              │           │   1. Preprocess: lowercase, Sastrawi     │
 │              │           │      stemming, stopword removal          │
 │              │           │   2. TF-IDF vectorization                │
 │              │ ◀──────── │   3. Multinomial Naive Bayes             │
 └──────────────┘           │      → intent + confidence score         │
        │                   └──────────────────────────────────────────┘
        ▼
 Matching service shown with the AI confidence score
 (falls back to keyword search if the AI service is offline)
```

## Model Performance

Evaluated on a held-out test set of 79 questions (20% split):

| Metric                | Score     |
|-----------------------|-----------|
| Accuracy              | **96.2%** |
| Precision (macro avg) | 96.9%     |
| Recall (macro avg)    | 96.3%     |
| F1-score (macro avg)  | 96.3%     |

- **Dataset:** 390 example questions in Indonesian across 10 intents (`ml/dataset/intent_dataset.csv`)
- **Preprocessing impact:** adding Indonesian stemming and stopword removal with **Sastrawi** raised accuracy from ~92% to ~96%
- **Confidence threshold:** `0.25`, chosen empirically — correct matches typically score 0.4–0.7, while unrelated questions score 0.10–0.23 (see `config/ai.php`)

**Intents covered:** Class Schedule · Study Plan Card (KRS) · Academic Grades · Tuition Payment · Thesis Defense Registration · Graduation · Academic Administration · Academic Letters · Student Affairs · Course Information

## Features

### For students (public)
- Natural-language search with AI intent detection and confidence score
- Quick-search chips for frequently asked questions
- Service directory grouped by category, with a detail page for every service
- Graceful fallback to keyword search when the AI service is offline

### For administrators
- Dashboard with a usage overview
- Category and service management (create, edit, delete)
- Search history with a low-confidence filter and CSV export, to spot questions the model struggles with
- Training dataset management — add, edit and delete example questions, with search, intent filter and distribution statistics
- AI model page — view evaluation results (accuracy, per-intent scores, confusion matrix) and retrain the model with one click

## Tech Stack

| Layer           | Technology                                                                                  |
|-----------------|---------------------------------------------------------------------------------------------|
| Web application | Laravel 12, PHP 8.2+, Blade, Tailwind CSS 4, Vite 7                                         |
| Database        | MySQL / MariaDB                                                                             |
| AI service      | Python, Flask, scikit-learn (TF-IDF + Multinomial Naive Bayes), PySastrawi, pandas, joblib |
| Testing         | PHPUnit — 59 feature and unit tests                                                         |
| Deployment      | Docker, or shared hosting + PythonAnywhere                                                  |

## Getting Started

### Prerequisites

- PHP 8.2+ and Composer
- MySQL or MariaDB (Laragon or XAMPP works fine)
- Node.js and npm
- Python 3.10+ *(only required for AI search)*

### 1. Run the web application

```bash
git clone https://github.com/kennethandreas/gunadarma-service-navigator.git
cd gunadarma-service-navigator

composer install
cp .env.example .env          # Windows: copy .env.example .env
php artisan key:generate
```

Create an empty database named `gunadarma_navigator`, then adjust the `DB_*` values in `.env` if your setup differs from the defaults (`127.0.0.1:3306`, user `root`, empty password).

```bash
php artisan migrate --seed
npm install
npm run dev
php artisan serve
```

Open **http://127.0.0.1:8000**. The admin panel is available at **`/admin/login`**.

> [!IMPORTANT]
> The seeder creates a default administrator account (see `database/seeders/DatabaseSeeder.php`). Change its password right after your first login.

### 2. Enable AI search (optional)

The model is already trained and stored in `ml/model/`. Start the AI service in a separate terminal and keep it running next to `php artisan serve`:

```bash
cd ml
pip install -r requirements.txt
python api.py                 # http://127.0.0.1:5000
```

If the service isn't running, the website keeps working and uses keyword search instead.

### Useful ML commands

```bash
python train.py                                  # retrain the model from the dataset
python evaluate.py                               # accuracy, precision, recall, F1 + confusion matrix
python predict.py "Saya mau cek jadwal kuliah"   # run a single prediction from the CLI
```

## Configuration

| Variable                  | Default                         | Description                                        |
|---------------------------|---------------------------------|----------------------------------------------------|
| `AI_SERVICE_URL`          | `http://127.0.0.1:5000`         | Base URL of the Python AI service                  |
| `AI_CONFIDENCE_THRESHOLD` | `0.25`                          | Minimum confidence required to accept a prediction |
| `AI_DATASET_PATH`         | `ml/dataset/intent_dataset.csv` | Dataset file used by the admin dataset page        |

## AI Service API

| Method | Endpoint   | Description                                                      |
|--------|------------|------------------------------------------------------------------|
| `GET`  | `/health`  | Health check — returns `{ "status": "ok" }`                      |
| `POST` | `/predict` | Body `{ "question": "..." }` → returns `intent` and `confidence` |
| `POST` | `/retrain` | Retrains and re-evaluates the model, returns the new metrics     |

## Testing

```bash
php artisan test
```

The suite contains 59 feature and unit tests covering the public pages, search (keyword and AI) and every admin module.

## Deployment

- **Docker** — a multi-stage `Dockerfile` builds the frontend assets and serves the Laravel app on port `10000` (overridable with the `PORT` variable).
- **Shared hosting** — the `deploy/` folder contains ready-to-use files for hosting the Laravel app on shared hosting and the AI service on PythonAnywhere. See [`deploy/README.md`](deploy/README.md).

## Project Structure

```
├── app/
│   ├── Http/Controllers/      Public and admin controllers
│   ├── Models/                Category, Service, SearchHistory, User
│   └── Services/              SearchService (keyword + AI), AiClient (HTTP client)
├── config/ai.php              Confidence threshold, AI service URL, dataset path
├── database/                  Migrations, seeders, factories
├── deploy/                    Shared hosting + PythonAnywhere deployment files
├── ml/
│   ├── dataset/               intent_dataset.csv (390 examples, 10 intents)
│   ├── model/                 Trained model, vectorizer, metadata, evaluation
│   ├── preprocessing.py       Sastrawi stemming and stopword removal
│   ├── train.py · evaluate.py · predict.py
│   └── api.py                 Flask REST API consumed by Laravel
├── resources/                 Blade views, Tailwind CSS, JavaScript
├── routes/                    web.php (public), admin.php (admin panel)
├── tests/                     Feature and unit tests
├── Dockerfile
└── skema.sql                  Database schema
```

## Academic Context

This application was developed as an **Undergraduate Thesis (Skripsi)** at Universitas Gunadarma. It was built across 21 development phases — from database design and the admin panel to model training, evaluation, testing and UI refinement.

## Author

**Kenneth Andreas** — [@kennethandreas](https://github.com/kennethandreas)

---

<div align="center">
<sub>Released under the MIT License.</sub>
</div>
