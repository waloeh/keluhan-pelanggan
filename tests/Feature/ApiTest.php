<?php

namespace Tests\Feature;

use App\Models\KeluhanPelanggan;
use App\Models\KeluhanStatusHistori;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use DatabaseTransactions;

    protected $token;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Tester',
            'email' => 'tester@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->token = $this->user->createToken('api-token')->plainTextToken;
    }

    /** @test */
    public function user_bisa_login_untuk_dapat_token()
    {
        $user = User::create([
            'name' => 'hadi',
            'email' => 'hadi@gmail.com',
            'password' => Hash::make('password')
        ]);

        $payload = [
            'email' => 'hadi@gmail.com',
            'password' => 'password'
        ];

        $response = $this->post('/api/login', $payload);
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'token',
                    'user' => [
                        'id',
                        'name',
                        'email'
                    ]
                ]);
    }

    /** @test */
    public function user_gagal_login_dengan_kredensial_salah()
    {
        $user = User::create([
            'name' => 'hadi',
            'email' => 'ibrohimmm@gmail.com',
            'password' => Hash::make('password')
        ]);

        $payload = [
            'email' => 'ibrohimmm@gmail.com',
            'password' => 'passwordsalah'
        ];

        $response = $this->post('/api/login', $payload);
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials'
            ]);
    }

    /** @test */
    public function update_status_keluhan_berhasil()
    {
        $keluhan = KeluhanPelanggan::create([
            'nama' => 'mulyana',
            'email' => 'mulyana@example.com',
            'nomor_hp' => '08123456789',
            'status_keluhan' => 0,
            'keluhan' => 'AC bocor',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->put("/api/status-keluhan/{$keluhan->id}", [
            'status_keluhan' => 1
        ]);

        $response->assertStatus(200)
         ->assertJson([
             'message' => 'Status keluhan berhasil diperbarui.'
         ]);
    }

    /** @test */
    public function update_status_keluhan_gagal_tanpa_token()
    {
        $keluhan = KeluhanPelanggan::create([
            'nama' => 'mulyana',
            'email' => 'mulyana@example.com',
            'nomor_hp' => '08123456789',
            'status_keluhan' => 0,
            'keluhan' => 'AC bocor',
        ]);

        $token_salah = '000'; 
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token_salah,
            'Accept' => 'application/json',
        ])->put("/api/status-keluhan/{$keluhan->id}", [
            'status_keluhan' => 1
        ]);

        $response->assertStatus(401)
         ->assertJson([
             'message' => 'Unauthenticated.'
         ]);
    }

    /** @test */
    public function hapus_status_keluhan_berhasil()
    {
      $keluhan = KeluhanPelanggan::create([
            'nama' => 'Ali',
            'email' => 'ali@example.com',
            'nomor_hp' => '08123456789',
            'status_keluhan' => 0,
            'keluhan' => 'Contoh keluhan'
        ]);

        $histori = KeluhanStatusHistori::create([
            'keluhan_id' => $keluhan->id,
            'status_keluhan' => 1,
            'updated_at' => now(),
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->delete("/api/status-keluhan/{$keluhan->id}", [
                'status_keluhan' => 1,
            ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Data berhasil dihapus',
                ]);

        $this->assertDatabaseMissing('keluhan_status_his', [ // pastikan histori terhapus
            'keluhan_id' => $keluhan->id,
            'status_keluhan' => 1,
        ]);
    }

       /** @test */
    public function hapus_status_keluhan_gagal_id_tidak_ditemukan()
    {
        $id_salah = 1234;
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->delete("/api/status-keluhan/{$id_salah}", [
                'status_keluhan' => 1,
            ]);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Data tidak ditemukan'
            ]);
    }

    /** @test */
    public function logout_berhasil_dengan_hapus_token()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->post('/api/logout'); 

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Logout berhasil',
                 ]);

        // Pastikan token sudah tidak ada
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_type' => 'App\Models\User',
            'tokenable_id' => $this->user->id, // sesuaikan jika ID user tidak 1
        ]);
    }

}
