<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'restaurant_id',
        'order_id',
        'product_id',
        'rating',
        'food_rating',
        'delivery_rating',
        'service_rating',
        'comment',
        'images',
        'is_approved',
        'is_featured',
        'admin_response',
        'responded_at',
        'helpful_count',
        'unhelpful_count',
    ];

    protected $casts = [
        'rating' => 'integer',
        'food_rating' => 'integer',
        'delivery_rating' => 'integer',
        'service_rating' => 'integer',
        'images' => 'array',
        'is_approved' => 'boolean',
        'is_featured' => 'boolean',
        'responded_at' => 'datetime',
        'helpful_count' => 'integer',
        'unhelpful_count' => 'integer',
    ];

    protected $appends = [
        'rating_stars',
        'helpful_percentage',
        'image_urls',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(ReviewVote::class);
    }

    // Accesseurs
    public function getRatingStarsAttribute(): string
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            $stars .= $i <= $this->rating ? '★' : '☆';
        }
        return $stars;
    }

    public function getHelpfulPercentageAttribute(): float
    {
        $total = $this->helpful_count + $this->unhelpful_count;
        return $total > 0 ? round(($this->helpful_count / $total) * 100, 1) : 0;
    }

    public function getImageUrlsAttribute(): array
    {
        if (!$this->images) {
            return [];
        }

        return array_map(function($image) {
            return asset('storage/' . $image);
        }, $this->images);
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeWithRating($query, int $rating)
    {
        return $query->where('rating', $rating);
    }

    public function scopeWithImages($query)
    {
        return $query->whereNotNull('images');
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    // Méthodes
    public function approve(): void
    {
        $this->update(['is_approved' => true]);
        
        // Mettre à jour la note du produit
        if ($this->product) {
            $this->product->updateRating();
        }
        
        // Mettre à jour la note du restaurant
        $this->restaurant->update(['rating_updated_at' => now()]);
    }

    public function disapprove(): void
    {
        $this->update(['is_approved' => false]);
        
        if ($this->product) {
            $this->product->updateRating();
        }
    }

    public function addAdminResponse(string $response): void
    {
        $this->update([
            'admin_response' => $response,
            'responded_at' => now(),
        ]);
    }

    public function vote(User $user, string $type): void
    {
        $existingVote = $this->votes()->where('user_id', $user->id)->first();

        if ($existingVote) {
            if ($existingVote->type === $type) {
                // Retirer le vote
                $existingVote->delete();
                $this->decrement($type . '_count');
            } else {
                // Changer le vote
                $existingVote->update(['type' => $type]);
                $this->decrement($type === 'helpful' ? 'unhelpful_count' : 'helpful_count');
                $this->increment($type . '_count');
            }
        } else {
            // Nouveau vote
            $this->votes()->create([
                'user_id' => $user->id,
                'type' => $type,
            ]);
            $this->increment($type . '_count');
        }
    }

    public function hasUserVoted(User $user): ?string
    {
        $vote = $this->votes()->where('user_id', $user->id)->first();
        return $vote ? $vote->type : null;
    }
}