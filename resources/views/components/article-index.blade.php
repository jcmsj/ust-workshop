<x-guest-layout>

<div class="container mx-auto px-4">
    {{-- Featured Articles Carousel --}}
    @if($featuredCarousel->isNotEmpty())
    <div class="carousel w-full rounded-box my-8">
        @foreach($featuredCarousel as $index => $article)
            <div id="slide{{ $index }}" class="carousel-item relative w-full h-96">
                <div class="hero bg-base-200 w-full">
                    <div class="hero-content flex-col lg:flex-row">
                        <div class="text-center lg:text-left">
                            <h1 class="text-5xl font-bold">{{ $article->title }}</h1>
                            <p class="py-6">{{ $article->summary }}</p>
                            <a href="{{ route('articles.show', $article) }}" class="btn btn-primary">Read More</a>
                        </div>
                    </div>
                </div>
                <div class="absolute flex justify-between transform -translate-y-1/2 left-5 right-5 top-1/2">
                    <a href="#slide{{ $index - 1 < 0 ? count($featuredCarousel) - 1 : $index - 1 }}" class="btn btn-circle">❮</a> 
                    <a href="#slide{{ $index + 1 >= count($featuredCarousel) ? 0 : $index + 1 }}" class="btn btn-circle">❯</a>
                </div>
            </div>
        @endforeach
    </div>
    @endif

    {{-- Paginated Articles Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 my-8">
        @foreach($articles as $article)
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">{{ $article->title }}</h2>
                    <p class="text-sm text-gray-500">By {{ $article->penname ?? $article->author->name }}</p>
                    <p>{{ $article->summary }}</p>
                    <div class="card-actions justify-end">
                        <a href="{{ route('articles.show', $article) }}" class="btn btn-primary">Read More</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination Links --}}
    <div class="my-8">
        {{ $articles->links() }}
    </div>
</div>
</x-guest-layout>
