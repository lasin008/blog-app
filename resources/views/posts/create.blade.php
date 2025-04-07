@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ isset($post) ? 'Edit' : 'Create' }} Post</h1>

    <form action="{{ isset($post) ? route('posts.update', $post->id) : route('posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @if(isset($post))
            @method('PUT')  <!-- Add method field for update -->
        @endif

        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $post->title ?? '') }}" required>
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">Content</label>
            <textarea class="form-control" id="content" name="content" rows="5" required>{{ old('content', $post->content ?? '') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Image</label>
            <input type="file" class="form-control" id="image" name="image">
            @if(isset($post) && $post->image)
                <div class="mt-2">
                    <img src="{{ asset('storage/' . $post->image) }}" alt="Post Image" style="width: 150px;">
                    <p>Current image</p>
                </div>
            @endif
        </div>

        <div class="mb-3">
            <label for="tags" class="form-label">Tags</label>
            
            @if($tags->isEmpty())
                <!-- If there are no tags, show an input to manually enter tags -->
                <input type="text" class="form-control" id="tags" name="tags" placeholder="Enter tags separated by commas" value="{{ old('tags', isset($post) ? $post->tags->pluck('name')->implode(', ') : '') }}">
                <small class="form-text text-muted">Enter tags separated by commas (e.g., "Tech, Web, Laravel")</small>
            @else
                <!-- If tags exist, show a multi-select dropdown -->
                <select class="form-control" id="tags" name="tags[]" multiple>
                    @foreach ($tags as $tag)
                        <option value="{{ $tag->id }}" 
                            @if(isset($post) && $post->tags->contains('id', $tag->id)) selected @endif>
                            {{ $tag->name }}
                        </option>
                    @endforeach
                </select>
            @endif
        </div>

        <button type="submit" class="btn btn-primary">{{ isset($post) ? 'Update' : 'Create' }} Post</button>
    </form>
</div>
@endsection
