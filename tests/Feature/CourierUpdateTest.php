<?php

namespace Tests\Feature;

use App\Models\Courier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourierUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_a_courier_in_the_database(): void
    {
        $courier = Courier::factory()->create([
            'name' => 'Old Name',
            'level' => 1,
        ]);

        $response = $this->patchJson("/api/couriers/{$courier->id}", [
            'name' => 'New Name',
            'level' => 4,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'New Name')
            ->assertJsonPath('data.level', 4);

        $this->assertDatabaseHas('couriers', [
            'id' => $courier->id,
            'name' => 'New Name',
            'level' => 4,
        ]);
        $this->assertDatabaseMissing('couriers', ['name' => 'Old Name']);
    }

    public function test_it_allows_a_partial_update(): void
    {
        $courier = Courier::factory()->create(['name' => 'Keep Me', 'level' => 2]);

        $response = $this->patchJson("/api/couriers/{$courier->id}", ['level' => 5]);

        $response->assertOk();
        $this->assertDatabaseHas('couriers', [
            'id' => $courier->id,
            'name' => 'Keep Me',
            'level' => 5,
        ]);
    }

    public function test_it_validates_the_level_on_update(): void
    {
        $courier = Courier::factory()->create(['level' => 2]);

        $response = $this->patchJson("/api/couriers/{$courier->id}", ['level' => 0]);

        $response->assertUnprocessable()->assertJsonValidationErrors('level');
        $this->assertDatabaseHas('couriers', ['id' => $courier->id, 'level' => 2]);
    }

    public function test_it_ignores_the_current_courier_when_checking_unique_phone(): void
    {
        $courier = Courier::factory()->create(['phone' => '081111111111']);

        $response = $this->patchJson("/api/couriers/{$courier->id}", [
            'phone' => '081111111111',
            'name' => 'Same Phone Update',
        ]);

        $response->assertOk();
    }

    public function test_it_rejects_a_phone_already_used_by_another_courier(): void
    {
        Courier::factory()->create(['phone' => '082222222222']);
        $courier = Courier::factory()->create(['phone' => '083333333333']);

        $response = $this->patchJson("/api/couriers/{$courier->id}", [
            'phone' => '082222222222',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('phone');
    }

    public function test_it_returns_404_when_updating_a_missing_courier(): void
    {
        $this->patchJson('/api/couriers/999', ['name' => 'Nobody'])->assertNotFound();
    }
}
