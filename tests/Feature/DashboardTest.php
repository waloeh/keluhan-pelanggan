<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use DatabaseTransactions;
    
    /** @test */
    public function response_summary()
    {
        $user = User::factory()->create();
        $this->actingAs($user); //harus login dulu

        $response = $this->get('/summary');
        $response->assertStatus(200)
             ->assertJsonStructure([
                'pie',
                'total_pie',
                'table' => [
                    '*' => ['nama', 'email', 'created_at', 'selisih_hari']
                ],
                'bar' => [
                    'label',
                    'data' => [
                        '*' => ['label', 'data', 'backgroundColor']
                    ]
                ]
            ]);
    }
}
