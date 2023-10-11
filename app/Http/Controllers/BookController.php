<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $title = $request->input('title');
        $filter = $request->input('filter', '');

        $books = Book::when(
            $title,
            fn($query, $title) => $query->title($title)
        );
        $books = match ($filter) {
            'popular_last_month' => $books->popularLastMonth(),
            'popular_last_6months' => $books->popularLast6Months(),
            'highest_rated_last_month' => $books->highestRatedLastMonth(),
            'highest_rated_last_6months' => $books->highestRatedLas6tMonths(),
            default => $books->latest()->withAvgRating()->withReviewsCount()
        };

        $cacheKey = 'books' . $filter . ':' . $title;
        $books =
            cache()->remember(
                $cacheKey,
                36000,
                fn() => $books->get()
            );

        return response()->json(['data' => $books], 200);
    }

    public function show(int $id)
    {
        $cacheKey = 'book:' . $id;
        $book = cache()->remember(
            $cacheKey,
            3600,
            fn() => Book::with([
                'reviews' => fn($query) => $query->latest()
            ])->withAvgRating()->withReviewsCount()->findOrFail($id)
        );

        return response()->json(['data' => $book]);
    }

    public function store(Request $request)
    {
        $request->expectsJson();

        try {
            $validatedBook = $request->validate([
                'title' => 'required|string',
                'author' => 'required|string',
            ]);

            $newBook = Book::create($validatedBook);

            return response()->json(['data' => $newBook], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Book creation failed'], 500);
        }
    }


    public function update(Request $request, int $id)
    {
        $request->expectsJson();
        $validatedData = $request->validate([
            'title' => 'string',
            'author' => 'string',
        ]);
        try {
            $book = Book::findOrFail($id);
            $book->update($validatedData);
        } catch (\Exception $e) {
            return response()->json(data: ['error' => 'Book updating failed'], status: 500);
        }

        return response()->json(['message' => 'Book updated successfully']);

    }

    public function destroy(int $id)
    {
        Book::destroy($id);
        return response()->json(['message' => 'Book deleted successfully'], 204);
    }
}
