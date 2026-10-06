<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReadingPlanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'book_id' => Book::factory(),
            'status' => $this->faker->randomElement(['want', 'reading', 'done']),
            'target_date' => $this->faker->optional()->dateTimeBetween('now', '+60 days')?->format('Y-m-d'),
            'started_at' => null,
            'finished_at' => null,
        ];
    }
}
