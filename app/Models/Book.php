<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'author', ''];


    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function scopeTitle(Builder $query, string $title): Builder
    {
        return $query->where('title', 'LIKE', '%' . $title . '%');
    }
    private function dateRangeFilter(Builder $q, $from, $to)
    {
        if ($from && !$to) {
            $q->where('created_at', '>=', $from);
        } elseif (!$from && $to) {
            $q->where('created_at', '<=', $to);
        } elseif ($from && $to) {
            $q->whereBetween('created_at', [$from, $to]);
        }
    }
    public function scopeWithReviewsCount(Builder $query, $from = null, $to = null): Builder|QueryBuilder
    {
        return $query->withCount([
            'reviews' => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)]);
    }

    public function scopeWithAvgRating(Builder $query, $from = null, $to = null): Builder|QueryBuilder
    {
        return $query->withAvg([
            'reviews' => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)
        ], 'rating');
    }

    public function scopePopular(Builder $query, $from = null, $to = null): Builder|QueryBuilder
    {
        return $query->WithReviewsCount()
            ->orderBy("reviews_count", 'desc');
    }

    public function scopeHighestRated(Builder $query, $from = null, $to = null): Builder|QueryBuilder
    {
        return $query->withAvgRating()
            ->orderBy('reviews_avg_rating', 'desc');
    }

    public function scopeMinReviews(Builder $query, int $minReviews): Builder|QueryBuilder
    {
        return $query->having('reviews_count', '>=', $minReviews);
    }

    public function scopePopularLastMonth(Builder $query): Builder|QueryBuilder
    {
        return $query->popular(now()->subMonth(), now())
            ->highestRated(now()->subMonth(), now())
            ->minReviewS(2);
    }

    public function scopePopularLast6Months(Builder $query): Builder|QueryBuilder
    {
        return $query->popular(now()->subMonth(6), now())
            ->highestRated(now()->subMonth(6), now())
            ->minReviewS(2);
    }

    public function scopeHighestRatedLastMonth(Builder $query): Builder|QueryBuilder
    {
        return $query->highestRated(now()->subMonth(), now())
            ->popular(now()->subMonth(), now())
            ->minReviewS(2);
    }

    public function scopeHighestRatedLas6tMonths(Builder $query): Builder|QueryBuilder
    {
        return $query->highestRated(now()->subMonth(6), now())
            ->popular(now()->subMonth(6), now())
            ->minReviewS(2);
    }

    protected static function booted()
    {
        static::updated(fn(Book $book) => cache()->forget('book:' . $book->id));
        static::deleted(fn(Book $book) => cache()->forget('book:' . $book->id));
    }
}
