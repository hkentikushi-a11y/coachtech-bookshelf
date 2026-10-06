<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'isbn' => $this->isbn,
            'published_date' => $this->published_date?->format('Y-m-d'),
            'description' => $this->description,
            'image_url' => $this->image_url,
            'genres' => $this->whenLoaded('genres', fn () => $this->genres->pluck('name')
            ),
            'average_rating' => $this->whenLoaded('reviews', fn () => $this->reviews->count() > 0
                    ? round($this->reviews->avg('rating'), 1)
                    : null
            ),
            'reviews_count' => $this->whenLoaded('reviews', fn () => $this->reviews->count()
            ),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
