<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::all();

        return response()->json([
            'status' => 'success',
            'length' => count($reviews),
            'data' => $reviews
        ]);
    }

    public function store(Request $request)
    {
        $request->expectsJson();

        try {
            $validatedReview = $request->validate([
                'review' => 'string|required',
                'rating' => 'integer|required',
                'book_id' => 'integer|required'
            ]);

            $book = Book::find($validatedReview['book_id']);
            if (!$book)
                return response()->json(['error' => 'Cannot find any Books'], 404);

            $newReview = $book->reviews()->create($validatedReview);

            return response()->json(['data' => $newReview], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e], 500);
        }
    }

    public function show(int $id)
    {
        $review = Review::find($id);

        if (!$review)
            return response()->json(['error' => 'Cannot find any reviews'], 404);

        return response()->json([
            'status' => 'success',
            "data" => $review
        ]);
    }

    public function update(Request $request, int $id)
    {
        $request->expectsJson();
        try {
            $vailadedReview = $request->validate([
                'review' => 'string',
                'rating' => 'integer',
            ]);
            $review = Review::find($id);
            if (!$review)
                return response()->json(['error' => 'Cannot find any reviews'], 404);

            $review->update($vailadedReview);
            return response()->json(['data' => $review]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e], 500);
        }
    }

    public function destroy(string $id)
    {
       $review = Review::destroy($id);

        if (!$review)
            return response()->json(['error' => 'Cannot find any reviews'], 404);
        return response()->json(['message' => 'Review deleted successfully'], 204);
    }
}
