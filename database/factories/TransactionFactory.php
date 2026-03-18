<?php

namespace Database\Factories;

use App\Models\Shift;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'notransaction' => fake()->unique()->bothify('INV####????'),
            'shift_id' => Shift::factory(),
            'table_id' => Table::factory(),
            'user_id' => User::factory(),
            'date' => now(),
        ];
    }
}
