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
                        </li>
                    @empty
                        <li class="list-group-item">No comments yet.</li>
                    @endforelse
                </ul>
            </div>

            <div class="mt-4">
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
