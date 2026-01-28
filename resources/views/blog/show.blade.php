<x-app-layout>
<div class="py-6">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
    <h1 class="text-3xl font-bold mb-2">{{ $post->title }}</h1>
    <p class="text-gray-500 mb-4">By {{ $post->author->name ?? 'Unknown' }} @if($post->published_at) • {{ $post->published_at->format('M d, Y') }} @endif</p>
    @if($post->featured_image)
        <img src="{{ asset('storage/'.$post->featured_image) }}" alt="{{ $post->title }}" class="mb-4 max-h-96 object-cover">
    @endif
    <div class="content">{!! $post->content !!}</div>
        </div>
    </div>
</div>
</x-app-layout>
