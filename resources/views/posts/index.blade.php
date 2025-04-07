@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Posts</h1>

    <!-- Filters -->
    <div class="row mb-3">
        <form method="GET" action="{{ route('posts.index') }}" class="form-inline">
            <div class="form-group mr-2">
                <label for="title" class="mr-2">Title</label>
                <input type="text" class="form-control" name="title" id="title" placeholder="Search by title" value="{{ request()->get('title') }}">
            </div>
            <div class="form-group mr-2">
                <label for="author_id" class="mr-2">Author</label>
                <input type="text" class="form-control" name="author_id" id="author_id" placeholder="Search by author" value="{{ request()->get('author_id') }}">
            </div>
            <div class="form-group mr-2">
                <label for="tag" class="mr-2">Tag</label>
                <input type="text" class="form-control" name="tag" id="tag" placeholder="Search by tag" value="{{ request()->get('tag') }}">
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>

    <!-- Create New Post Button -->
    <a href="{{ route('posts.create') }}" class="btn btn-primary mb-3">Create New Post</a>
    
    <!-- Posts Table -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">Title</th>
                <th scope="col">Author</th>
                <th scope="col">Comment Count</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($posts as $post)
                <tr>
                    <td>{{ $post->title }}</td>
                    <td>{{ $post->author->name }}</td>
                    <td>
                        <button class="btn btn-info comment-btn" data-post-id="{{ $post->id }}" data-toggle="modal" data-target="#commentsModal">
                            {{ $post->comments->count() }} Comments
                        </button>
                    </td>
                    <td>
                        <a href="{{ route('posts.show', $post->id) }}" class="btn btn-success">View</a>
                        <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('posts.destroy', $post->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $posts->links() }}
    </div>

    <!-- Comments Modal -->
    <div class="modal fade" id="commentsModal" tabindex="-1" role="dialog" aria-labelledby="commentsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="commentsModalLabel">Comments</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="comments-list">
                    <!-- Comments will be loaded dynamically here -->
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Handle comment button click
        $('.comment-btn').on('click', function() {
            const postId = $(this).data('post-id');
            
            // Fetch comments for the post using AJAX
            $.ajax({
                url: '/posts/' + postId + '/comments',
                method: 'GET',
                success: function(data) {
                    let commentsHtml = '<ul>';
                    data.comments.forEach(comment => {
                        commentsHtml += `<li>${comment.content} - <strong>${comment.author}</strong></li>`;
                    });
                    commentsHtml += '</ul>';
                    $('#comments-list').html(commentsHtml);
                },
                error: function() {
                    $('#comments-list').html('<p>Failed to load comments.</p>');
                }
            });
        });
    });
</script>
@endsection
