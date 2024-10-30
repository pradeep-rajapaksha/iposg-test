<?php

namespace Tests\Unit\OrderController;

use PHPUnit\Framework\TestCase;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Order::factory()->count(50)->create();
    }

    public function test_get_all_orders_with_default_pagination()
    {
        $response = $this->json('GET', '/api/orders');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'user_id', 'status', 'created_at', 'updated_at']
                     ],
                     'current_page',
                     'last_page',
                     'total',
                     'per_page',
                 ]);
    }

    public function test_get_orders_with_custom_pagination()
    {
        $response = $this->json('GET', '/api/orders', ['per_page' => 15]);

        $response->assertStatus(200)
                 ->assertJsonPath('per_page', 15);
    }

    public function test_filter_orders_by_status()
    {
        Order::factory()->create(['status' => 'completed']);

        $response = $this->json('GET', '/api/orders', ['status' => 'completed']);

        $response->assertStatus(200)
                 ->assertJsonFragment(['status' => 'completed']);
    }

    public function test_filter_orders_by_date_range()
    {
        Order::factory()->create(['created_at' => '2024-01-01']);
        Order::factory()->create(['created_at' => '2024-12-31']);

        $response = $this->json('GET', '/api/orders', [
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31'
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'user_id', 'status', 'created_at', 'updated_at']
                     ],
                 ]);
    }

    public function test_filter_orders_by_user_id()
    {
        $order = Order::factory()->create(['user_id' => 1]);

        $response = $this->json('GET', '/api/orders', ['user_id' => 1]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['user_id' => 1]);
    }
}
