<?php

namespace Database\Seeders; // Corrected namespace for Seeder

use Illuminate\Database\Seeder;
use App\Models\User; // Import the User model
use App\Models\Book; // Import the Book model
use App\Models\Review; // Import the Review model

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        User::factory(10)->create();

        Book::factory(5)->create()->each(function ($book) {
            $numOfReviews = random_int(5, 30);

            Review::factory()->count($numOfReviews)
                ->good()->for($book)->create();
        });
        Book::factory(5)->create()->each(function ($book) {
            $numOfReviews = random_int(5, 30);

            Review::factory()->count($numOfReviews)
                ->average()->for($book)->create();
        });
        Book::factory(5)->create()->each(function ($book) {
            $numOfReviews = random_int(5, 30);

            Review::factory()->count($numOfReviews)
                ->bad()->for($book)->create();
        });
    }
}