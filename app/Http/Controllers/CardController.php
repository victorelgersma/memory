<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CardController extends Controller
{
    public function index(Request $request): View
    {
        $cards = $request->user()->cards()
            ->with('tags')
            ->latest()
            ->get();

        $availableTags = $request->user()->tags()
            ->orderBy('name')
            ->pluck('name');

        return view('cards.index', [
            'cards' => $cards,
            'availableTags' => $availableTags,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateCard($request);

        $card = $request->user()->cards()->create([
            'front' => $validated['front'],
            'back' => $validated['back'],
        ]);

        $this->syncTags($request, $card);

        return back()->with('status', 'card-saved');
    }

    public function update(Request $request, Card $card): RedirectResponse
    {
        abort_unless($card->user_id === $request->user()->id, 404);

        $validated = $this->validateCard($request);

        $card->update([
            'front' => $validated['front'],
            'back' => $validated['back'],
        ]);

        $this->syncTags($request, $card);

        return back()->with('status', 'card-updated');
    }

    public function destroy(Request $request, Card $card): RedirectResponse
    {
        abort_unless($card->user_id === $request->user()->id, 404);

        $card->delete();

        return back()->with('status', 'card-deleted');
    }

    private function validateCard(Request $request): array
    {
        return $request->validate([
            'front' => ['required', 'string', 'max:5000'],
            'back' => ['required', 'string', 'max:5000'],
            'tags' => ['nullable', 'string', 'max:1000'],
        ]);
    }

    /**
     * Splits the comma-separated "tags" input into individual tag names,
     * finds-or-creates each one scoped to the current user, and syncs
     * them onto the card. Blank/duplicate names are dropped.
     */
    private function syncTags(Request $request, Card $card): void
    {
        $names = collect(explode(',', (string) $request->input('tags', '')))
            ->map(fn ($name) => Str::lower(trim($name)))
            ->filter()
            ->unique();

        $tagIds = $names->map(
            fn ($name) => Tag::firstOrCreate(
                ['user_id' => $request->user()->id, 'name' => $name]
            )->id
        );

        $card->tags()->sync($tagIds);
    }
}
