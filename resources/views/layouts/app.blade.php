<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Memory') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.14.1/cdn.min.js"></script>
</head>
<body class="h-full bg-white text-gray-900 dark:bg-gray-950 dark:text-gray-100 antialiased">
    <div class="min-h-full flex flex-col">
        <nav class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-800">
            <div class="flex items-center gap-4">
                <a href="{{ route('cards.index') }}" class="font-semibold tracking-tight text-lg">Memory</a>
                @auth
                    @foreach ([['cards.index', 'Cards', 'cards.*'], ['tags.index', 'Decks', 'tags.*'], ['review', 'Review', 'review*']] as [$route, $label, $pattern])
                        <a href="{{ route($route) }}"
                            class="text-sm {{ request()->routeIs($pattern) ? 'text-gray-900 dark:text-gray-100 font-medium' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                @endauth
            </div>
            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                        Log out
                    </button>
                </form>
            @endauth
        </nav>

        @php
            $flash = [
                'card-saved' => 'Card saved.',
                'card-updated' => 'Card updated.',
                'card-deleted' => 'Card deleted.',
            ][session('status')] ?? null;
        @endphp
        @if ($flash)
            <div class="max-w-5xl w-full mx-auto px-6 pt-4 text-sm text-green-600 dark:text-green-400">{{ $flash }}</div>
        @endif

        <main class="flex-1">
            @yield('content')
        </main>
    </div>

    <script>
        // Jump between cards with Ctrl+Shift+Left/Right or Cmd+Left/Right
        // (plain Tab also works — every card has tabindex="0").
        document.addEventListener('keydown', function (e) {
            const cards = Array.from(document.querySelectorAll('[data-memory-card]'))
                .filter(card => card.offsetParent !== null); // skip cards hidden by search
            if (cards.length === 0) return;

            const isNext = (e.key === 'ArrowRight' && e.metaKey)
                || (e.key === 'ArrowRight' && e.ctrlKey && e.shiftKey);
            const isPrev = (e.key === 'ArrowLeft' && e.metaKey)
                || (e.key === 'ArrowLeft' && e.ctrlKey && e.shiftKey);

            if (!isNext && !isPrev) return;

            e.preventDefault();

            const currentIndex = cards.indexOf(document.activeElement);
            const nextIndex = currentIndex === -1
                ? 0
                : isNext
                    ? (currentIndex + 1) % cards.length
                    : (currentIndex - 1 + cards.length) % cards.length;

            cards[nextIndex].focus();
        });

        // Alpine component for the deck (tag) chip input. Registered as a
        // plain script so it exists before Alpine's deferred script boots.
        window.tagInput = function (initialTags, existingTagNames) {
            return {
                tags: Array.isArray(initialTags) ? initialTags.slice() : [],
                availableTags: Array.isArray(existingTagNames) ? existingTagNames.slice() : [],
                draft: '',
                highlightedIndex: -1,
                get suggestions() {
                    const q = this.draft.trim().toLowerCase();
                    if (q === '') return [];
                    const already = this.tags.map(t => t.toLowerCase());
                    return this.availableTags
                        .filter(name => name.toLowerCase().includes(q))
                        .filter(name => !already.includes(name.toLowerCase()))
                        .slice(0, 6);
                },
                addTag(name) {
                    const value = (name ?? this.draft).trim().replace(/,+$/, '').trim();
                    this.draft = '';
                    this.highlightedIndex = -1;
                    if (value === '') return;
                    const lower = value.toLowerCase();
                    if (!this.tags.some(t => t.toLowerCase() === lower)) {
                        this.tags.push(value);
                    }
                },
                selectHighlighted() {
                    const options = this.suggestions;
                    if (this.highlightedIndex >= 0 && options[this.highlightedIndex]) {
                        this.addTag(options[this.highlightedIndex]);
                    } else {
                        this.addTag();
                    }
                },
                moveHighlight(delta) {
                    const count = this.suggestions.length;
                    if (count === 0) {
                        this.highlightedIndex = -1;
                        return;
                    }
                    this.highlightedIndex = (this.highlightedIndex + delta + count) % count;
                },
                removeTag(index) {
                    this.tags.splice(index, 1);
                },
                removeLastTag() {
                    if (this.draft === '' && this.tags.length > 0) {
                        this.tags.pop();
                    }
                },
            };
        };
    </script>
</body>
</html>
