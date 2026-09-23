@extends('layouts.app')

@php
    $nextUrl = $card
        ? ($tag ? route('review.deck', [$tag, 'after' => $card->id]) : route('review', ['after' => $card->id]))
        : null;
@endphp

@section('content')
<div class="max-w-2xl mx-auto px-6 py-10">
    <div class="flex items-center justify-between gap-3 mb-8 text-sm text-gray-500 dark:text-gray-400">
        <p>
            @if ($tag)
                Reviewing <a href="{{ route('tags.show', $tag) }}" class="font-medium text-gray-900 dark:text-gray-100 hover:underline">{{ $tag->name }}</a>
            @else
                Reviewing all cards
            @endif
            ({{ $total }} {{ Str::plural('card', $total) }})
        </p>
        <a href="{{ route('tags.index') }}" class="hover:text-gray-900 dark:hover:text-gray-100">Change deck</a>
    </div>

    @if (! $card)
        <div class="text-center text-gray-400 dark:text-gray-600 py-20">
            There are no cards to review here yet.
            <a href="{{ route('cards.index') }}" class="block mt-3 text-gray-900 dark:text-gray-100 font-medium hover:underline">Write a card</a>
        </div>
    @else
        {{-- Space or Enter reveals the back; pressing again draws the next card. --}}
        <div
            x-data="{ shown: false }"
            @keydown.window.space.prevent="shown ? window.location.href = @js($nextUrl) : shown = true"
            @keydown.window.enter.prevent="shown ? window.location.href = @js($nextUrl) : shown = true"
        >
            <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 min-h-[16rem] flex flex-col">
                <div class="flex-1 flex items-center justify-center px-8 py-10">
                    <p class="text-2xl sm:text-3xl font-semibold leading-snug text-center break-words whitespace-pre-line">{{ $card->front }}</p>
                </div>

                <div
                    x-show="shown"
                    x-cloak
                    x-transition:enter="transition ease-out duration-200 motion-reduce:transition-none"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="border-t border-dashed border-gray-300 dark:border-gray-700 px-8 py-8"
                >
                    <p class="text-lg sm:text-xl leading-relaxed text-center text-gray-700 dark:text-gray-300 break-words whitespace-pre-line">{{ $card->back }}</p>
                </div>
            </div>

            @if ($card->tags->isNotEmpty())
                <div class="mt-3 flex flex-wrap justify-center gap-1.5">
                    @foreach ($card->tags as $cardTag)
                        <a href="{{ route('review.deck', $cardTag) }}" title="Review this deck"
                            class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">{{ $cardTag->name }}</a>
                    @endforeach
                </div>
            @endif

            <div class="mt-8 flex flex-col items-center gap-2">
                <button
                    type="button"
                    x-show="!shown"
                    @click="shown = true"
                    class="px-8 py-3 rounded-full bg-gray-900 text-white text-base font-medium hover:bg-gray-700 dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-white transition-colors"
                >
                    Show
                </button>
                <a
                    x-show="shown"
                    x-cloak
                    href="{{ $nextUrl }}"
                    class="px-8 py-3 rounded-full bg-gray-900 text-white text-base font-medium hover:bg-gray-700 dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-white transition-colors"
                >
                    Next card
                </a>
                <p class="text-xs text-gray-400 dark:text-gray-600">Space or Enter</p>
            </div>
        </div>
    @endif
</div>
@endsection
