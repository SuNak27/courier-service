<?php

namespace Tests\Feature;

use App\Models\Courier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourierIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_a_paginated_list_using_the_response_formatter(): void
    {
        Courier::factory()->count(20)->create();

        $response = $this->getJson('/api/couriers');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'code',
                'message',
                'page',
                'per_page',
                'query',
                'limit',
                'data' => [['id', 'name', 'phone', 'level', 'is_active', 'created_at']],
            ])
            ->assertJsonCount(15, 'data')
            ->assertJsonPath('success', true)
            ->assertJsonPath('code', 200)
            ->assertJsonPath('page', 1)
            ->assertJsonPath('per_page', 15)
            ->assertJsonPath('limit', 15);
    }

    public function test_the_query_field_echoes_the_search_term(): void
    {
        Courier::factory()->create(['name' => 'Budi Santoso']);

        $this->getJson('/api/couriers?search=budi')
            ->assertOk()
            ->assertJsonPath('query', 'budi');
    }

    public function test_it_sorts_by_name_ascending_by_default(): void
    {
        Courier::factory()->create(['name' => 'Charlie']);
        Courier::factory()->create(['name' => 'Alice']);
        Courier::factory()->create(['name' => 'Bob']);

        $response = $this->getJson('/api/couriers');

        $names = array_column($response->json('data'), 'name');

        $this->assertSame(['Alice', 'Bob', 'Charlie'], $names);
    }

    public function test_it_can_sort_by_registration_date(): void
    {
        $first = Courier::factory()->create(['name' => 'Zulu', 'created_at' => now()->subDays(3)]);
        $second = Courier::factory()->create(['name' => 'Alpha', 'created_at' => now()->subDay()]);
        $third = Courier::factory()->create(['name' => 'Mike', 'created_at' => now()]);

        $response = $this->getJson('/api/couriers?sort=registered_at');

        $ids = array_column($response->json('data'), 'id');

        $this->assertSame([$first->id, $second->id, $third->id], $ids);
    }

    public function test_it_can_sort_by_registration_date_descending(): void
    {
        $old = Courier::factory()->create(['created_at' => now()->subDays(3)]);
        $new = Courier::factory()->create(['created_at' => now()]);

        $response = $this->getJson('/api/couriers?sort=registered_at&direction=desc');

        $ids = array_column($response->json('data'), 'id');

        $this->assertSame([$new->id, $old->id], $ids);
    }

    public function test_it_searches_couriers_by_multiple_name_words_in_any_order(): void
    {
        $target = Courier::factory()->create(['name' => 'Budiono Hadi Agung']);
        Courier::factory()->create(['name' => 'Siti Rahmawati']);
        Courier::factory()->create(['name' => 'Agung Setiawan']);

        $response = $this->getJson('/api/couriers?search=budi+agung');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $target->id);
    }

    public function test_it_filters_couriers_by_a_set_of_levels(): void
    {
        Courier::factory()->level(1)->create();
        $level2 = Courier::factory()->level(2)->create();
        $level3 = Courier::factory()->level(3)->create();
        Courier::factory()->level(4)->create();

        $response = $this->getJson('/api/couriers?level=2,3');

        $ids = array_column($response->json('data'), 'id');

        $response->assertOk()->assertJsonCount(2, 'data');
        $this->assertEqualsCanonicalizing([$level2->id, $level3->id], $ids);
    }

    public function test_filters_can_be_combined(): void
    {
        $match = Courier::factory()->level(2)->create(['name' => 'Budi Santoso']);
        Courier::factory()->level(2)->create(['name' => 'Andi Wijaya']);
        Courier::factory()->level(5)->create(['name' => 'Budi Hartono']);

        $response = $this->getJson('/api/couriers?search=budi&level=2,3');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $match->id);
    }
}
