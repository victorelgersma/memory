@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-10">
    <h1 class="text-2xl font-semibold mb-6">Decks</h1>

    @if ($tags->isEmpty())
        <div class="text-center text-gray-400 dark:text-gray-600 py-20">
            No decks yet. Add a deck name to a card and it will show up here.
        </div>
    @else
        <ul class="divide-y divide-gray-200 dark:divide-gray-800 border-y border-gray-200 dark:border-gray-800">
            @foreach ($tags as $tag)
                <li class="flex items-center justify-between gap-4 py-3">
                    <a href="{{ route('tags.show', $tag) }}" class="flex items-baseline gap-2 hover:underline">
                        <span class="font-medium">{{ $tag->name }}</span>
                        <span class="text-sm text-gray-400 dark:text-gray-600">
                            {{ $tag->cards_count }} {{ Str::plural('card', $tag->cards_count) }}
                        </span>
                    </a>
                    @if ($tag->cards_count > 0)
                        <a href="{{ route('review.deck', $tag) }}" class="text-sm font-medium px-3 py-1.5 rounded-full border border-gray-300 dark:border-gray-700 hover:border-gray-900 dark:hover:border-gray-100 transition-colors">
                            Review
                        </a>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
