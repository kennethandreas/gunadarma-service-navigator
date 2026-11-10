<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * One placeholder service per category, using a placeholder URL.
     * Admin will replace the URL with the official Gunadarma link later.
     */
    public function run(): void
    {
        $services = [
            'jadwal-perkuliahan' => [
                'name' => 'Jadwal Perkuliahan',
                'slug' => 'jadwal-perkuliahan',
                'description' => 'Gunakan layanan ini untuk melihat informasi jadwal perkuliahan.',
                'capabilities' => ['Melihat jadwal kuliah', 'Melihat jadwal kelas', 'Melihat jadwal semester'],
            ],
            'krs' => [
                'name' => 'Kartu Rencana Studi (KRS)',
                'slug' => 'krs',
                'description' => 'Gunakan layanan ini untuk mengisi dan mengelola KRS.',
                'capabilities' => ['Mengisi KRS', 'Melihat KRS yang sudah diambil', 'Mengubah rencana studi sesuai periode yang ditentukan'],
            ],
            'nilai-akademik' => [
                'name' => 'Nilai Akademik',
                'slug' => 'nilai-akademik',
                'description' => 'Gunakan layanan ini untuk melihat nilai semester dan transkrip.',
                'capabilities' => ['Melihat nilai per mata kuliah', 'Melihat nilai per semester', 'Melihat transkrip akademik'],
            ],
            'pembayaran-kuliah' => [
                'name' => 'Pembayaran Kuliah',
                'slug' => 'pembayaran-kuliah',
                'description' => 'Gunakan layanan ini untuk informasi dan pembayaran biaya kuliah.',
                'capabilities' => ['Melihat tagihan kuliah', 'Melihat riwayat pembayaran', 'Mengetahui metode pembayaran yang tersedia'],
            ],
            'pendaftaran-sidang' => [
                'name' => 'Pendaftaran Sidang',
                'slug' => 'pendaftaran-sidang',
                'description' => 'Gunakan layanan ini untuk mendaftar sidang skripsi/tugas akhir.',
                'capabilities' => ['Mendaftar sidang skripsi/tugas akhir', 'Melihat syarat pendaftaran sidang', 'Melihat jadwal sidang'],
            ],
            'wisuda' => [
                'name' => 'Wisuda',
                'slug' => 'wisuda',
                'description' => 'Gunakan layanan ini untuk informasi dan pendaftaran wisuda.',
                'capabilities' => ['Mendaftar wisuda', 'Melihat jadwal wisuda', 'Melihat persyaratan wisuda'],
            ],
            'administrasi-akademik' => [
                'name' => 'Administrasi Akademik',
                'slug' => 'administrasi-akademik',
                'description' => 'Gunakan layanan ini untuk keperluan administrasi akademik.',
                'capabilities' => ['Mengurus administrasi akademik mahasiswa', 'Melihat status administrasi', 'Menghubungi bagian akademik'],
            ],
            'surat-akademik' => [
                'name' => 'Surat Akademik',
                'slug' => 'surat-akademik',
                'description' => 'Gunakan layanan ini untuk mengajukan surat keterangan akademik.',
                'capabilities' => ['Mengajukan surat keterangan aktif kuliah', 'Mengajukan surat keterangan lainnya', 'Melihat status pengajuan surat'],
            ],
            'kemahasiswaan' => [
                'name' => 'Layanan Kemahasiswaan',
                'slug' => 'layanan-kemahasiswaan',
                'description' => 'Gunakan layanan ini untuk informasi organisasi dan kegiatan kemahasiswaan.',
                'capabilities' => ['Melihat informasi organisasi mahasiswa', 'Melihat informasi kegiatan kemahasiswaan', 'Mengikuti kegiatan kemahasiswaan'],
            ],
            'informasi-perkuliahan' => [
                'name' => 'Informasi Perkuliahan',
                'slug' => 'informasi-perkuliahan',
                'description' => 'Gunakan layanan ini untuk informasi umum seputar perkuliahan.',
                'capabilities' => ['Melihat informasi umum perkuliahan', 'Melihat pengumuman akademik', 'Melihat kalender akademik'],
            ],
        ];

        foreach ($services as $categorySlug => $service) {
            $category = Category::where('slug', $categorySlug)->first();

            if (! $category) {
                continue;
            }

            Service::updateOrCreate(
                ['slug' => $service['slug']],
                $service + [
                    'category_id' => $category->id,
                    'url' => 'https://example.com',
                    'status' => true,
                ]
            );
        }
    }
}
