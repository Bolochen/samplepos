<?php

namespace Database\Factories;

use App\Models\Menu;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransactionItem>
 */
class TransactionItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'transaction_id' => Transaction::factory(),
            'menu_item_id' => Menu::factory(),
            'quantity' => fake()->numberBetween(1,12),
            'price' => fake()->numberBetween(1000,35000),
        ];
    }
}
