@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-6 py-10" x-data="{
    formOpen: @js($errors->any()),
    search: '',
    cards: @js($cards->map(fn ($c) => Str::lower($c->front.' '.$c->back.' '.$c->tags->pluck('name')->implode(' ')))),
    get visibleCount() {
        const q = this.search.trim().toLowerCase();
        return this.cards.filter(text => q === '' || text.includes(q)).length;
    }
}">

    {{-- Big, central search — filters instantly as you type --}}
    <div class="mb-10">
        <input
            type="text"
            x-model="search"
            placeholder="Search your cards…"
            class="w-full text-2xl sm:text-3xl font-medium text-center bg-transparent border-0 border-b-2 border-gray-200 dark:border-gray-800 focus:border-gray-900 dark:focus:border-gray-100 focus:ring-0 outline-none px-1 py-4 placeholder:text-gray-300 dark:placeholder:text-gray-700 transition-colors"
            autofocus
        >
    </div>

    <div class="flex items-center justify-between gap-3 mb-6">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            <span x-text="visibleCount"></span>
            <span x-text="visibleCount === 1 ? 'card' : 'cards'"></span>
        </p>

        <div class="flex items-center gap-2">
            @if ($cards->isNotEmpty())
                <a href="{{ route('review') }}" class="text-sm font-medium px-4 py-2 rounded-full border border-gray-300 dark:border-gray-700 hover:border-gray-900 dark:hover:border-gray-100 transition-colors">
                    Review all
                </a>
            @endif
            <button
                type="button"
                @click="formOpen = !formOpen"
                class="text-sm font-medium px-4 py-2 rounded-full bg-gray-900 text-white hover:bg-gray-700 dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-white transition-colors"
            >
                <span x-show="!formOpen">+ New card</span>
                <span x-show="formOpen" x-cloak>Cancel</span>
            </button>
        </div>
    </div>

    {{-- New card form — hidden until "New card" is pressed --}}
    <div x-show="formOpen" x-cloak x-transition class="mb-10 p-5 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900">
        <form method="POST" action="{{ route('cards.store') }}" class="grid gap-3 sm:grid-cols-2">
            @csrf
            <textarea name="front" rows="3" placeholder="Front — the question or prompt" required
                class="px-3 py-2 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-gray-900 dark:focus:ring-gray-100">{{ old('front') }}</textarea>
            <textarea name="back" rows="3" placeholder="Back — the answer" required
                class="px-3 py-2 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-gray-900 dark:focus:ring-gray-100">{{ old('back') }}</textarea>

            @include('cards._tag-input', [
                'initialTags' => array_values(array_filter(explode(',', (string) old('tags', '')))),
                'availableTags' => $availableTags,
                'class' => 'sm:col-span-2',
            ])

            @error('front')<p class="text-sm text-red-600 sm:col-span-2">{{ $message }}</p>@enderror
            @error('back')<p class="text-sm text-red-600 sm:col-span-2">{{ $message }}</p>@enderror
            @error('tags')<p class="text-sm text-red-600 sm:col-span-2">{{ $message }}</p>@enderror

            <button type="submit" class="sm:col-span-2 justify-self-start px-4 py-2 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-gray-700 dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-white transition-colors">
                Save card
            </button>
        </form>
    </div>

    @if ($cards->isEmpty())
        <div class="text-center text-gray-400 dark:text-gray-600 py-20">
            No cards yet. Press "New card" to write your first one.
        </div>
    @else
        <div x-show="visibleCount === 0" x-cloak class="text-center text-gray-400 dark:text-gray-600 py-20">
            No cards match that search.
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($cards as $card)
                @include('cards._card', ['card' => $card, 'availableTags' => $availableTags])
            @endforeach
        </div>
    @endif
</div>
@endsection
