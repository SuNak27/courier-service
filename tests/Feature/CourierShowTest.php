<?php

namespace Tests\Feature;

use App\Models\Courier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourierShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_all_data_for_a_single_courier(): void
    {
        $courier = Courier::factory()->create();

        $response = $this->getJson("/api/couriers/{$courier->id}");

        $response->assertOk()
            ->assertJsonPath('code', 200)
            ->assertJsonPath('data.id', $courier->id)
            ->assertJsonPath('data.name', $courier->name)
            ->assertJsonPath('data.phone', $courier->phone)
            ->assertJsonPath('data.email', $courier->email)
            ->assertJsonPath('data.vehicle_type', $courier->vehicle_type)
            ->assertJsonPath('data.vehicle_plate', $courier->vehicle_plate)
            ->assertJsonPath('data.level', $courier->level)
            ->assertJsonPath('data.is_active', $courier->is_active);
    }

    public function test_it_returns_404_for_a_missing_courier(): void
    {
        $this->getJson('/api/couriers/999')->assertNotFound();
    }
}
