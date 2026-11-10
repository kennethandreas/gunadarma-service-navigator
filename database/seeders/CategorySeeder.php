<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Initial intent categories for the navigator.
     * Admin can add/edit/delete/toggle these later — this is just starter data.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Jadwal Perkuliahan', 'slug' => 'jadwal-perkuliahan', 'description' => 'Informasi jadwal kuliah, jadwal kelas, dan jadwal semester.'],
            ['name' => 'Kartu Rencana Studi (KRS)', 'slug' => 'krs', 'description' => 'Pengisian dan pengelolaan Kartu Rencana Studi.'],
            ['name' => 'Nilai Akademik', 'slug' => 'nilai-akademik', 'description' => 'Melihat nilai per semester dan transkrip akademik.'],
            ['name' => 'Pembayaran Kuliah', 'slug' => 'pembayaran-kuliah', 'description' => 'Informasi dan tata cara pembayaran biaya kuliah.'],
            ['name' => 'Pendaftaran Sidang', 'slug' => 'pendaftaran-sidang', 'description' => 'Pendaftaran sidang skripsi/tugas akhir.'],
            ['name' => 'Wisuda', 'slug' => 'wisuda', 'description' => 'Informasi dan pendaftaran wisuda.'],
            ['name' => 'Administrasi Akademik', 'slug' => 'administrasi-akademik', 'description' => 'Layanan administrasi akademik mahasiswa.'],
            ['name' => 'Surat Akademik', 'slug' => 'surat-akademik', 'description' => 'Pengajuan surat keterangan dan dokumen akademik.'],
            ['name' => 'Kemahasiswaan', 'slug' => 'kemahasiswaan', 'description' => 'Layanan organisasi dan kegiatan kemahasiswaan.'],
            ['name' => 'Informasi Perkuliahan', 'slug' => 'informasi-perkuliahan', 'description' => 'Informasi umum seputar perkuliahan.'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category + ['status' => true]
            );
        }
    }
}
