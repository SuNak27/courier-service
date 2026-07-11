<?php

namespace Tests\Feature;

use App\Models\Courier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourierStoreTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Budiono Hadi Agung',
            'phone' => '081234567890',
            'email' => 'budi@example.com',
            'vehicle_type' => 'motorcycle',
            'vehicle_plate' => 'B 1234 XYZ',
            'level' => 3,
            'is_active' => true,
        ], $overrides);
    }

    public function test_it_stores_a_valid_courier_in_the_database(): void
    {
        $response = $this->postJson('/api/couriers', $this->validPayload());

        $response->assertCreated()
            ->assertJsonPath('code', 201)
            ->assertJsonPath('message', 'Courier created successfully.')
            ->assertJsonPath('data.name', 'Budiono Hadi Agung')
            ->assertJsonPath('data.level', 3);

        $this->assertDatabaseHas('couriers', [
            'name' => 'Budiono Hadi Agung',
            'phone' => '081234567890',
            'email' => 'budi@example.com',
            'level' => 3,
        ]);
    }

    public function test_it_requires_a_name(): void
    {
        $response = $this->postJson('/api/couriers', $this->validPayload(['name' => '']));

        $response->assertUnprocessable()->assertJsonValidationErrors('name');
        $this->assertDatabaseCount('couriers', 0);
    }

    public function test_it_requires_a_unique_phone(): void
    {
        Courier::factory()->create(['phone' => '081234567890']);

        $response = $this->postJson('/api/couriers', $this->validPayload(['phone' => '081234567890']));

        $response->assertUnprocessable()->assertJsonValidationErrors('phone');
    }

    public function test_it_rejects_an_invalid_email(): void
    {
        $response = $this->postJson('/api/couriers', $this->validPayload(['email' => 'not-an-email']));

        $response->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_it_rejects_a_level_outside_the_allowed_range(): void
    {
        $response = $this->postJson('/api/couriers', $this->validPayload(['level' => 6]));

        $response->assertUnprocessable()->assertJsonValidationErrors('level');
        $this->assertDatabaseCount('couriers', 0);
    }

    public function test_it_requires_a_level(): void
    {
        $response = $this->postJson('/api/couriers', $this->validPayload(['level' => null]));

        $response->assertUnprocessable()->assertJsonValidationErrors('level');
    }

    public function test_email_is_optional(): void
    {
        $response = $this->postJson('/api/couriers', $this->validPayload(['email' => null]));

        $response->assertCreated();
        $this->assertDatabaseHas('couriers', ['phone' => '081234567890', 'email' => null]);
    }
}
