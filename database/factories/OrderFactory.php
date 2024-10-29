<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\Shipping;
use App\Models\Payment;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first(),
            'total_amount' => $this->faker->randomFloat(2, 50, 500),
            'status' => $this->faker->randomElement(['pending', 'completed', 'cancelled']),
        ];  
    }

    public function withItems($count = 3)
    {
        return $this->has(OrderItem::factory()->count($count), 'items');
    }

    public function withShipping()
    {
        return $this->has(Shipping::factory(), 'shipping');
    }

    public function withPayment()
    {
        return $this->has(Payment::factory(), 'payment');
    }
}
