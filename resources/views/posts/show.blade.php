@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h1 class="text-center mb-4">{{ $post->title }}</h1>

            <div class="card mb-4">
                @if ($post->image)
                    <img src="{{ asset('storage/' . $post->image) }}" class="card-img-top" alt="Post Image" style="width: 100%; height: 350px; object-fit: cover;">
                @endif
                <div class="card-body">
                    <p class="card-text">{{ $post->content }}</p>
                    <p class="text-muted">By {{ $post->author->name }} | {{ $post->created_at->diffForHumans() }}</p>
                    
                    <!-- Tags Section -->
                    <div class="mt-3">
                        @if ($post->tags->count() > 0)
                            <p><strong>Tags:</strong></p>
                            <ul class="list-inline">
                                @foreach ($post->tags as $tag)
                                    <li class="list-inline-item">
                                        <span class="badge badge-info">{{ $tag->name }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p>No tags assigned to this post.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <h3 class="mb-4">Comments</h3>
                <ul class="list-group">
                    @forelse ($post->comments as $comment)
                        <li class="list-group-item mb-3">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $comment->author->name }}</strong>
                                <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mt-2">{{ $comment->content }}</p>

                            <!-- Delete button only if the current user is the comment author -->
                            @if ($comment->author->id === auth()->id())
                                <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm mt-2">Delete</button>
                                </form>
                            @endif
                        </li>
                    @empty
                        <li class="list-group-item">No comments yet.</li>
                    @endforelse
                </ul>
            </div>

            <div class="mt-4 mb-10">
                <h4>Add a Comment</h4>
                <form action="{{ route('comments.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="post_id" value="{{ $post->id }}">
                    <div class="form-group">
                        <textarea class="form-control" name="content" rows="4" placeholder="Add a comment..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3 btn-lg btn-block">Submit Comment</button>
                </form>
            </div>

            <div class="mt-4 text-center">
                <a href="{{ route('posts.index') }}" class="btn btn-secondary">Back to Posts</a>
            </div>
        </div>
    </div>
</div>
@endsection
