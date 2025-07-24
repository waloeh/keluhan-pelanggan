<?php

namespace Tests\Feature;

use App\Exports\KeluhanPelangganExport;
use App\Models\KeluhanPelanggan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class KeluhanPelangganTest extends TestCase
{
    use WithFaker, DatabaseTransactions;
    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpFaker(); //Inisialisasi faker

        $this->user = User::factory()->create();
        $this->actingAs($this->user); //harus login untuk bisa akses url
    }

    /** @test */
    public function pengguna_dapat_menyimpan_keluhan_dari_form_web()
    {
        $payload = [
            'nama' => 'Hadi',
            'email' => 'hadi@example.com',
            'nomor_hp' => '08123456789',
            'keluhan' => str_repeat('Masalah pada produk. ', 3), // > 50 karakter
        ];

        $response = $this->post('/keluhan', $payload);
        $keluhan = KeluhanPelanggan::latest()->first();

        $response->assertRedirect(); // redirect setelah sukses
        $this->assertDatabaseHas('keluhan_status_his', [ // berhasil simpan ke table histori
            'keluhan_id' => $keluhan->id,
            'status_keluhan' => '0',
        ]);
    }

    /** @test */
    public function validasi_gagal_dengan_nama_tidak_valid()
    {
        $payload = [
            'nama' => 'Ha',
            'email' => 'hadi@example.com',
            'keluhan' => str_repeat('Keluhan cukup panjang. ', 3),
        ];

        $response = $this->from('/keluhan')->post('/keluhan', $payload);

        $response->assertRedirect('/keluhan');
        $response->assertSessionHasErrors(['nama']);
    }

    /** @test */
    public function validasi_gagal_dengan_keluhan_pendek()
    {
        $payload = [
            'nama' =>  'Hadi M',
            'email' => 'hadi@example.com',
            'keluhan' => 'Pendek',
        ];

        $response = $this->from('/keluhan')->post('/keluhan', $payload);

        $response->assertRedirect('/keluhan');
        $response->assertSessionHasErrors(['keluhan']);
    }

    /** @test */
    public function dapat_melihat_daftar_keluhan()
    {
        $response = $this->get('/keluhan');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'nama',
                    'email',
                    'nomor_hp',
                    'status_keluhan',
                    'keluhan',
                    'created_at'
                ]
            ]
        ]);
    }

    
    /** @test */
    public function dapat_mencari_keluhan_berdasarkan_nama()
    {
        $payload = [
            'nama' => 'Hadi Mulyana',
            'email' => $this->faker->unique()->safeEmail,
            'nomor_hp' => $this->faker->numerify('08##########'),
            'keluhan' => $this->faker->paragraph(3),
        ];

        $this->post('/keluhan', $payload);
        $response = $this->get('/keluhan?search=Hadi');

        $response->assertStatus(200);
        $response->assertJsonFragment(['nama' => 'Hadi Mulyana']);
        $response->assertJsonMissing(['nama' => 'Ibrahim Ali Husein']);
    }

    /** @test */
    public function test_pengguna_bisa_melihat_detail_keluhan_berdasarkan_id()
    {
        $keluhan = KeluhanPelanggan::with('histori')->first(); // Ambil 1 data dari database
        $this->assertNotNull($keluhan, 'Keluhan tidak ditemukan'); // Pastikan ada datanya

        $response = $this->get("/keluhan/{$keluhan->id}");
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $keluhan->id,
            'nama' => $keluhan->nama,
            'email' => $keluhan->email,
        ]);

        // relasi histori ikut dimuat
        $response->assertJsonStructure([
            'id',
            'nama',
            'email',
            'keluhan',
            'histori' => [
                '*' => ['id', 'keluhan_id', 'status_keluhan', 'updated_at']
            ]
        ]);
    }

    /** @test */
    public function pengguna_dapat_mengupdate_data_keluhan()
    {
        $payload = [
            'nama' => 'Hadi',
            'email' => 'hadi@example.com',
            'nomor_hp' => '08123456789',
            'keluhan' => str_repeat('Masalah pada produk. ', 3), // > 50 karakter
        ];

        $response = $this->post('/keluhan', $payload);
        $keluhan = KeluhanPelanggan::latest()->first();

        $updatePayload = [
            'nama' => 'Hadi Updated',
            'email' => 'hadi.updated@example.com',
            'nomor_hp' => '0822222222',
            'keluhan' => str_repeat('Keluhan yang diperbarui. ', 3), // >50 karakter
        ];

        $response = $this->put("/keluhan/{$keluhan->id}", $updatePayload);
        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Data berhasil diubah',
                 ]);

        // pastikan data di database terupdate
        $this->assertDatabaseHas('keluhan_pelanggan', [
            'id' => $keluhan->id,
            'nama' => 'Hadi Updated',
            'email' => 'hadi.updated@example.com',
            'nomor_hp' => '0822222222',
            'keluhan' => str_repeat('Keluhan yang diperbarui. ', 3),
        ]);
    }

    /** @test */
    public function pengguna_dapat_menghapus_data_keluhan()
    {
        $payload = [
            'nama' => 'Hadi',
            'email' => 'hadi@gmail.com',
            'nomor_hp' => '082110833390',
            'status_keluhan' => '0',
            'keluhan' => str_repeat('Keluhan akan dihapus', 3)
        ];

        $this->post('/keluhan', $payload);
        $keluhan = KeluhanPelanggan::first();

        $response = $this->delete("/keluhan/{$keluhan->id}");
        $response->assertStatus(200) //cek response
            ->assertJson([
                'message' => 'Data keluhan berhasil dihapus.'
            ]);

        $this->assertDatabaseMissing('keluhan_pelanggan', [ //data tidak ada di database
            'id' => $keluhan->id,
        ]);
    }

    /** @test */
    public function pengguna_dapat_mengekspor_keluhan_dalam_format_xlsx()
    {
        Excel::fake(); // Fake export agar tidak benar-benar membuat file

        $response = $this->get('/export/xlsx');

        $response->assertStatus(200);

        Excel::assertDownloaded('keluhan_pelangan.xlsx', function ($export) {
            return $export instanceof KeluhanPelangganExport;
        });
    }

    /** @test */
    public function pengguna_dapat_mengekspor_keluhan_dalam_format_csv()
    {
        Excel::fake();

        $response = $this->get('/export/csv');

        $response->assertStatus(200);

        Excel::assertDownloaded('keluhan_pelangan.csv', function ($export) {
            return $export instanceof KeluhanPelangganExport;
        });
    }

    /** @test */
    public function pengguna_dapat_mengekspor_keluhan_dalam_format_txt()
    {
        KeluhanPelanggan::create([
            'nama' => 'Budi',
            'email' => 'budi@example.com',
            'nomor_hp' => '08123456789',
            'keluhan' => 'AC tidak dingin',
            'status_keluhan' => 1, // In Process
            'created_at' => '2025-07-10 08:00:00',
        ]);

        $response = $this->get('/export-txt');
        $response->assertStatus(200); // Status dan headers
        $this->assertStringContainsString('text/plain', $response->headers->get('Content-Type'));
        $response->assertHeader('Content-Disposition', 'attachment; filename="keluhan_pelanggan.txt"');

        // isi ile mengandung data yang sesuai
        $this->assertStringContainsString("Nama\tEmail\tNomor HP\tKeluhan\tStatus\tTanggal", $response->getContent());
        $this->assertStringContainsString("Budi\tbudi@example.com\t08123456789\tAC tidak dingin\tIn Process\t2025-07-10 08:00", $response->getContent());
    }

    /** @test */
    public function pengguna_dapat_mengekspor_keluhan_dalam_format_pdf()
    {
        $response = $this->get('/export-pdf');

        // file berhasil diunduh
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertHeader('Content-Disposition', 'attachment; filename=keluhan_pelanggan.pdf');

        // validasi isi file PDF berupa binary
        $content = $response->getContent();
        $this->assertNotEmpty($content);
        $this->assertTrue(str_starts_with($content, '%PDF')); // Header PDF file
    }

    /** @test */
    public function export_keluhan_dengan_format_tidak_valid_menghasilkan_error_400()
    {
        $response = $this->get('/export/jpg');
        $response->assertStatus(400)
                 ->assertSee('Format tidak didukung');
    }
}
