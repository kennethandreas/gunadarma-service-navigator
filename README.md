# Gunadarma Academic Service Navigator

Proyek skripsi — aplikasi navigasi layanan akademik untuk Universitas Gunadarma yang memungkinkan mahasiswa mencari layanan menggunakan pertanyaan bebas, bukan hanya kata kunci yang persis. Backend menggunakan Laravel + MySQL, sedangkan pengenalan maksud (intent) pertanyaan ditangani oleh model TF-IDF + Naive Bayes yang dibangun terpisah dengan Python.

Seluruh 21 fase pengembangan sudah selesai, tersisa penyempurnaan kecil. Rincian progres dan penambahan setelah fase 21 ada di bagian bawah.

Login admin (dari seeder): `admin@gunadarma.ac.id` / `password`. Segera ganti setelah login pertama kali.

## Requirement

- PHP >= 8.2 (Laragon/XAMPP sudah mencukupi)
- Composer
- MySQL / MariaDB
- Node.js + npm
- Python 3.10+ — hanya diperlukan untuk menjalankan API AI (`ml/api.py`). Model sudah terlatih, sehingga Python tidak wajib jika hanya ingin menjalankan aplikasi utamanya (ada mekanisme fallback, lihat penjelasan di bawah)

## Cara menjalankan

```
composer install
cp .env.example .env        # Windows: copy .env.example .env
```

Buat database kosong terlebih dahulu dengan nama `gunadarma_navigator` (melalui HeidiSQL/phpMyAdmin bawaan Laragon/XAMPP, atau `mysql -u root -e "CREATE DATABASE gunadarma_navigator"`). Sesuaikan kredensial di `.env` apabila konfigurasi MySQL berbeda dari default (host `127.0.0.1`, port `3306`, user `root`, password kosong).

```
php artisan key:generate
php artisan migrate
npm install
npm run dev
php artisan serve
```

Buka `http://127.0.0.1:8000`.

## Mengaktifkan pencarian berbasis AI

Model sudah terlatih dan tersimpan di `ml/model/`, sehingga tidak perlu retrain kecuali memang ingin mengubah dataset. Agar homepage menggunakan pencarian AI (bukan hanya keyword), jalankan API-nya pada terminal terpisah dan biarkan tetap berjalan berdampingan dengan `php artisan serve`:

```
cd ml
pip install -r requirements.txt
python api.py
```

Secara default berjalan di `http://127.0.0.1:5000`. Jika API ini tidak dijalankan atau berhenti, homepage akan otomatis kembali ke pencarian kata kunci (Phase 11) — tidak menyebabkan error, hanya saja confidence score dari AI tidak ditampilkan.

Skrip Python lain yang tersedia:

```
python train.py                                 # retrain model dari dataset
python evaluate.py                               # accuracy/precision/recall/F1 + confusion matrix
python predict.py "Saya mau cek jadwal kuliah"   # tes prediksi langsung dari CLI
```

Preprocessing (`ml/preprocessing.py`) menerapkan stemming dan stopword removal Bahasa Indonesia (Sastrawi) sebelum tahap TF-IDF. Perubahan ini yang menaikkan akurasi dari sekitar 92% (versi awal, hanya lowercase dan strip karakter) menjadi sekitar 96%. Detail dampaknya terhadap threshold confidence dijelaskan pada komentar di `config/ai.php`.

## Testing

```
php artisan test
```

Terdapat 59 test (Feature + Unit) pada direktori `tests/`.

## Struktur project

```
app/            Controller, Model
database/       Migration, seeder
resources/      Blade views, CSS (Tailwind), JS
routes/         web.php
ml/dataset/     intent_dataset.csv (390 contoh, 10 intent)
ml/model/       hasil training (.joblib, metadata.json, evaluation.json)
```

## Progres

Fase 1–21 sudah selesai seluruhnya: setup Laravel & database, autentikasi admin, homepage publik, direktori dan detail layanan, dashboard serta CRUD admin, pencarian (keyword & AI), training dan evaluasi model, riwayat pencarian, dashboard model AI, testing, hingga penyempurnaan UI (halaman 404/500, favicon, dan lainnya).

Penambahan setelah fase 21:
- Preprocessing Sastrawi pada model, menaikkan akurasi menjadi sekitar 96% (lihat penjelasan di atas)
- Perbaikan quick-search chip yang sebelumnya kadang tidak terdeteksi AI — dataset ditambah dari 350 menjadi 390 contoh, threshold confidence diturunkan dari 0.6 menjadi 0.25
- Export CSV riwayat pertanyaan pada halaman admin, mengikuti filter confidence rendah yang sedang aktif
- Halaman admin untuk mengelola dataset training — tambah/edit/hapus contoh pertanyaan langsung dari admin (membaca dan menulis `ml/dataset/intent_dataset.csv`), dilengkapi pencarian, filter per intent, serta statistik distribusi data. Setelah dataset diubah, model perlu di-retrain secara manual melalui halaman Model AI agar perubahan diterapkan.
