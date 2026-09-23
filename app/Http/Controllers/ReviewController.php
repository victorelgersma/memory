<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * Shows one random card — from all of the user's cards, or from a
     * single deck (tag). No scheduling: every draw is uniformly random.
     * The "after" query parameter carries the previous card's id so the
     * same card isn't drawn twice in a row (unless it's the only one).
     */
    public function show(Request $request, ?Tag $tag = null): View
    {
        if ($tag !== null) {
            abort_unless($tag->user_id === $request->user()->id, 404);
        }

        $query = fn () => $tag !== null
            ? $tag->cards()
            : $request->user()->cards();

        $total = $query()->count();
        $previousId = $request->integer('after');

        $card = $query()
            ->with('tags')
            ->when($previousId && $total > 1, fn ($q) => $q->whereKeyNot($previousId))
            ->inRandomOrder()
            ->first();

        return view('review.show', [
            'tag' => $tag,
            'card' => $card,
            'total' => $total,
        ]);
    }
}
