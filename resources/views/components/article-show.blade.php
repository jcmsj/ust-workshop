<x-guest-layout>
    <div class="w-full flex px-8 gap-2 justify-between">
        <aside class="w-64 relative hidden xl:block">
            <ul class="menu bg-base-200 rounded-box sticky top-20">
                <li class="menu-title">Featured Articles</li>
                @foreach($featured as $featuredArticle)
                <li>
                    <a href="{{ route('articles.show', $featuredArticle->slug) }}">
                        {{ $featuredArticle->title }}
                        <span class="text-sm text-base-content/60">{{ $featuredArticle->created_at->format('d/m/Y')
                            }}</span>
                    </a>
                </li>
                @endforeach
            </ul>
        </aside>
        <x-slot:sidebar>
            <div class="divider w-full">Articles</div>
            @foreach($featured as $featuredArticle)
            <li>
                <a href="{{ route('articles.show', $featuredArticle->slug) }}">
                    {{ $featuredArticle->title }}
                    <span class="text-sm text-base-content/60">{{ $featuredArticle->created_at->format('d/m/Y')
                        }}</span>
                </a>
            </li>
            @endforeach
        </x-slot:sidebar>
        <div class="flex-1 flex justify-center">
            <article>
                <h1 class="text-3xl font-bold mb-4">{{ $article->title }}</h1>
                <div class="flex items-center gap-4 text-sm text-base-content/70 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        {{ $article->created_at->format('M d, Y') }}
                    </div>
                    @if($article->author)
                    <div class="flex items-center">
                        {{--
                        <x-heroicon-s-user-circle /> --}}
                        by {{ $article->penname ?? $article->author->name }}
                    </div>
                    @endif
                </div>

                <div class="article-content leading-relaxed">
                    {!! $article->body !!}
                </div>
            </article>
        </div>
        <div class="relative hidden xl:block">
            <div class="sticky top-20">
                <h3 class="font-bold">Getting life insurance is easier than you think</h3>
                @livewire('submit-quote')
            </div>
        </div>
    </div>
</x-guest-layout>
