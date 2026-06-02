<?php

namespace App;

trait HasRating
{
    public function updateRating(): void
    {
        if (!method_exists($this, 'reviews')) {
            throw new \Exception('Model must have reviews() relationship');
        }

        $stats = $this->reviews()
            ->where('is_approved', true)
            ->selectRaw('
                AVG(rating) as avg_rating, 
                COUNT(*) as review_count,
                SUM(CASE WHEN rating >= 4 THEN 1 ELSE 0 END) as positive_reviews,
                SUM(CASE WHEN rating <= 2 THEN 1 ELSE 0 END) as negative_reviews
            ')
            ->first();

        $this->update([
            'rating_avg' => round($stats->avg_rating ?? 0, 2),
            'rating_count' => $stats->review_count ?? 0,
        ]);
    }

    public function getRatingDistributionAttribute(): array
    {
        $distribution = [];
        
        for ($i = 5; $i >= 1; $i--) {
            $distribution[$i] = $this->reviews()
                ->where('is_approved', true)
                ->where('rating', $i)
                ->count();
        }
        
        return $distribution;
    }

    public function getRecommendationScoreAttribute(): float
    {
        $total = $this->rating_count;
        if ($total === 0) return 0;
        
        $positive = $this->reviews()
            ->where('is_approved', true)
            ->where('rating', '>=', 4)
            ->count();
        
        return round(($positive / $total) * 100, 1);
    }
}
