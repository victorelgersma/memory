@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-6 py-10" x-data="{ search: '' }">
    <div class="flex items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('tags.index') }}" class="text-sm text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                ← Decks
            </a>
            <h1 class="text-2xl font-semibold">{{ $tag->name }}</h1>
        </div>

        @if ($cards->isNotEmpty())
            <a href="{{ route('review.deck', $tag) }}" class="text-sm font-medium px-4 py-2 rounded-full bg-gray-900 text-white hover:bg-gray-700 dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-white transition-colors">
                Review this deck
            </a>
        @endif
    </div>

    @if ($cards->isEmpty())
        <div class="text-center text-gray-400 dark:text-gray-600 py-20">
            No cards in "{{ $tag->name }}" yet.
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($cards as $card)
                @include('cards._card', ['card' => $card, 'availableTags' => $availableTags])
            @endforeach
        </div>
    @endif
</div>
@endsection
