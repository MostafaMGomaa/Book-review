<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'book_id' => null,
            'review' => $this->faker->paragraph,
            'rating' => $this->faker->numberBetween(1, 5),
            'created_at' => $this->faker->dateTimeBetween('-2 years'),
            'updated_at' => $this->faker->dateTimeBetween('created_at', 'now'),
        ];
    }

    public function average()
    {
        return $this->state(function (array $attributes) {
            return [
                'rating' => $this->faker->numberBetween(2, 4),
            ];
        });
    }

    public function bad()
    {
        return $this->state(function (array $attributes) {
            return [
                'rating' => $this->faker->numberBetween(1, 2),
            ];
        });
    }

    public function good()
    {
        return $this->state(function (array $attributes) {
            return [
                'rating' => $this->faker->numberBetween(4, 5),
            ];
        });
    }
}