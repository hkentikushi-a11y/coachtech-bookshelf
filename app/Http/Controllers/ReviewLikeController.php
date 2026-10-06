<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\RedirectResponse;

class ReviewLikeController extends Controller
{
    public function toggle(Review $review): RedirectResponse
    {
        $user = auth()->user();

        if ($user->reviewLikes()->where('review_id', $review->id)->exists()) {
            $user->reviewLikes()->detach($review->id);
        } else {
            $user->reviewLikes()->attach($review->id);
        }

        return back();
    }
}
