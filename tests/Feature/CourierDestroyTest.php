<?php

namespace Tests\Feature;

use App\Models\Courier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourierDestroyTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deletes_a_courier_from_the_database(): void
    {
        $courier = Courier::factory()->create();

        $response = $this->deleteJson("/api/couriers/{$courier->id}");

        $response->assertOk()
            ->assertJsonPath('code', 200)
            ->assertJsonPath('message', 'Courier deleted successfully.')
            ->assertJsonPath('data', null);

        $this->assertDatabaseMissing('couriers', ['id' => $courier->id]);
        $this->assertDatabaseCount('couriers', 0);
    }

    public function test_it_returns_404_when_deleting_a_missing_courier(): void
    {
        $this->deleteJson('/api/couriers/999')->assertNotFound();
    }
}
