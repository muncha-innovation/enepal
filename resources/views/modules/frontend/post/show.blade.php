@extends('layouts.frontend.app')

@section('title', $post->title)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Main Post Content -->
        <article class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Post Header -->
            <div class="px-6 py-8">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            @if($post->business->logo)
                                <img src="{{ getImage($post->business->logo, 'business/logo/') }}" 
                                     alt="{{ $post->business->name }}" 
                                     class="w-12 h-12 rounded-full object-cover">
                            @else
                                <div class="w-12 h-12 rounded-full bg-indigo-500 flex items-center justify-center">
                                    <span class="text-white font-semibold text-lg">
                                        {{ substr($post->business->name, 0, 1) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900">{{ $post->business->name }}</h4>
                            <p class="text-sm text-gray-600">{{ $post->user->name }} • {{ $post->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ $post->created_at->format('M j, Y') }}
                    </div>
                </div>

                <!-- Post Title -->
                <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $post->title }}</h1>
                
                <!-- Post Short Description -->
                @if($post->short_description)
                    <p class="text-xl text-gray-600 mb-6 leading-relaxed">{{ $post->short_description }}</p>
                @endif
            </div>

            <!-- Post Image -->
            @if($post->image)
                <div class="px-6 mb-8">
                    <img src="{{ getImage($post->image, 'posts/') }}" 
                         alt="{{ $post->title }}" 
                         class="w-full h-auto rounded-lg shadow-md">
                </div>
            @endif

            <!-- Post Content -->
            <div class="px-6 pb-8">
                <div class="prose max-w-none">
                    {!! $post->content !!}
                </div>
            </div>

            <!-- Post Meta & Actions -->
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-6">
                        <span class="text-sm text-gray-600">
                            <i class="far fa-eye mr-1"></i>
                            Views: {{ $post->views_count ?? 0 }}
                        </span>
                        <span class="text-sm text-gray-600">
                            <i class="far fa-heart mr-1"></i>
                            Likes: {{ $post->likes->count() }}
                        </span>
                        <span class="text-sm text-gray-600">
                            <i class="far fa-comment mr-1"></i>
                            Comments: {{ $post->comments->count() }}
                        </span>
                    </div>
                    
                    <!-- Share Buttons -->
                    <div class="flex items-center space-x-2">
                        <button onclick="sharePost()" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-share-alt mr-1"></i>
                            Share
                        </button>
                    </div>
                </div>
            </div>
        </article>

        <!-- Business Info Section -->
        <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">About {{ $post->business->name }}</h3>
            <div class="flex items-start space-x-4">
                @if($post->business->logo)
                    <img src="{{ getImage($post->business->logo, 'business/logo/') }}" 
                         alt="{{ $post->business->name }}" 
                         class="w-16 h-16 rounded-lg object-cover">
                @endif
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-900">{{ $post->business->name }}</h4>
                    @if($post->business->description)
                        <p class="text-gray-600 mt-2">{{ Str::limit($post->business->description, 150) }}</p>
                    @endif
                    @if($post->business->address)
                        <p class="text-sm text-gray-500 mt-2">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            {{ $post->business->address->city }}, {{ $post->business->address->country->name ?? '' }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Related Posts -->
        @if($relatedPosts->count() > 0)
            <div class="mt-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">More from {{ $post->business->name }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedPosts as $relatedPost)
                        <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                            @if($relatedPost->image)
                                <img src="{{ getImage($relatedPost->image, 'posts/') }}" 
                                     alt="{{ $relatedPost->title }}" 
                                     class="w-full h-48 object-cover">
                            @endif
                            <div class="p-4">
                                <h4 class="font-semibold text-gray-900 mb-2 line-clamp-2">
                                    <a href="{{ route('frontend.post.show', $relatedPost) }}" 
                                       class="hover:text-indigo-600 transition-colors">
                                        {{ $relatedPost->title }}
                                    </a>
                                </h4>
                                <p class="text-sm text-gray-600 line-clamp-3">{{ $relatedPost->short_description }}</p>
                                <div class="mt-3 text-xs text-gray-500">
                                    {{ $relatedPost->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function sharePost() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $post->title }}',
            text: '{{ $post->short_description }}',
            url: window.location.href
        });
    } else {
        // Fallback - copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Link copied to clipboard!');
        });
    }
}
</script>
@endsection 