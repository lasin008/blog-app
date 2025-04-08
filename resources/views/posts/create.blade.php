@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ isset($post) ? 'Edit' : 'Create' }} Post</h1>

    <form action="{{ isset($post) ? route('posts.update', $post->id) : route('posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @if(isset($post))
        @method('PUT') <!-- Add method field for update -->
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
                <!-- Close button to remove current image -->
                <button type="button" class="btn btn-danger btn-sm" id="remove-image-btn">
                    <i class="fas fa-times"></i> Remove Image
                </button>
                <input type="hidden" name="remove_image" id="remove_image" value="0"> <!-- Hidden input to track if image is removed -->
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

        <!-- Show active/inactive dropdown only when editing an existing post -->
        @if(isset($post))
        <div class="mb-3">
            <label for="is_active" class="form-label">Status</label>
            <select class="form-control" id="is_active" name="is_active">
                <option value="1" {{ (old('is_active', $post->is_active ?? 1) == 1) ? 'selected' : '' }}>Active</option>
                <option value="0" {{ (old('is_active', $post->is_active ?? 1) == 0) ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        @endif

        <button type="submit" class="btn btn-primary">{{ isset($post) ? 'Update' : 'Create' }} Post</button>
    </form>
</div>


<script>
    document.getElementById('remove-image-btn').addEventListener('click', function() {
        document.getElementById('remove_image').value = '1';
        const imageContainer = document.querySelector('.mt-2');
        imageContainer.style.display = 'none';
    });
</script>


@endsection
