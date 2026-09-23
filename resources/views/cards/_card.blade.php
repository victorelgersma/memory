{{-- One card in a grid. Click (or Enter) flips it between front and back;
     the pencil button switches to an inline edit form. --}}
<div
    x-data="{ editing: false, flipped: false }"
    data-memory-card
    data-search="{{ Str::lower($card->front.' '.$card->back.' '.$card->tags->pluck('name')->implode(' ')) }}"
    x-show="search.trim() === '' || $el.dataset.search.includes(search.trim().toLowerCase())"
    tabindex="0"
    @click="if (!editing) flipped = !flipped"
    @keydown.enter.self="if (!editing) flipped = !flipped"
    :class="flipped ? 'bg-gray-50 dark:bg-gray-800/60' : 'bg-white dark:bg-gray-900'"
    class="group relative flex flex-col rounded-xl border border-gray-200 dark:border-gray-800 p-5 min-h-[9rem] cursor-pointer transition-colors focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-100"
>
    {{-- View mode --}}
    <div x-show="!editing" class="flex flex-col flex-1">
        <div class="flex items-start justify-between gap-2">
            <p x-show="!flipped" class="font-semibold text-base leading-snug break-words whitespace-pre-line">{{ $card->front }}</p>
            <p x-show="flipped" x-cloak class="text-base leading-snug break-words whitespace-pre-line text-gray-700 dark:text-gray-300">{{ $card->back }}</p>

            <button
                type="button"
                title="Edit card"
                @click.stop="editing = true"
                class="shrink-0 p-1.5 rounded-md text-gray-400 opacity-0 group-hover:opacity-100 group-focus:opacity-100 focus:opacity-100 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800 transition-opacity"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                    <path d="m17.414 2.586-1.414 1.414a1 1 0 0 0 0 1.414L18.586 8l1.414-1.414a1 1 0 0 0 0-1.414l-1.414-1.414a1 1 0 0 0-1.414 0Z"/>
                    <path d="M16 6 4 18v3h3L19 9l-3-3Z"/>
                </svg>
            </button>
        </div>

        <div class="mt-auto pt-4 flex items-end justify-between gap-2">
            <div class="flex flex-wrap gap-1.5" @click.stop>
                @foreach ($card->tags as $tag)
                    <a
                        href="{{ route('tags.show', $tag) }}"
                        class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors"
                    >{{ $tag->name }}</a>
                @endforeach
            </div>
            <span class="text-xs text-gray-400 dark:text-gray-600 shrink-0" x-text="flipped ? 'back' : 'front'"></span>
        </div>
    </div>

    {{-- Edit mode --}}
    <div x-show="editing" x-cloak @click.stop>
        <form method="POST" action="{{ route('cards.update', $card) }}" class="space-y-2">
            @csrf
            @method('PUT')
            <textarea name="front" rows="2" required placeholder="Front"
                class="w-full px-3 py-2 text-sm rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-gray-900 dark:focus:ring-gray-100">{{ $card->front }}</textarea>
            <textarea name="back" rows="3" required placeholder="Back"
                class="w-full px-3 py-2 text-sm rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-gray-900 dark:focus:ring-gray-100">{{ $card->back }}</textarea>

            @include('cards._tag-input', [
                'initialTags' => $card->tags->pluck('name')->all(),
                'availableTags' => $availableTags ?? [],
            ])

            <div class="flex items-center justify-between pt-1">
                <button type="button" @click="editing = false" class="text-xs text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                    Cancel
                </button>
                <button type="submit" class="text-xs font-medium text-gray-900 dark:text-gray-100 hover:underline">
                    Save card
                </button>
            </div>
        </form>

        <form method="POST" action="{{ route('cards.destroy', $card) }}" onsubmit="return confirm('Delete this card?')" class="mt-2">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-xs text-red-500 hover:text-red-700">Delete card</button>
        </form>
    </div>
</div>
