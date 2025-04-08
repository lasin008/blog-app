@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Posts</h1>

    <!-- Filters Section -->
    <div class="row mb-4">
        <form method="GET" action="{{ route('posts.index') }}" class="w-100">
            <div class="row">

                <!-- Title Filter -->
                <div class="col-md-3 mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" name="title" id="title" placeholder="Search by title" value="{{ request()->get('title') }}">
                </div>

                <!-- Author Dropdown -->
                <div class="col-md-3 mb-3">
                    <label for="author_id" class="form-label">Author</label>
                    <select class="form-control" name="author_id" id="author_id">
                        <option value="">Select Author</option>
                        @foreach ($authors as $author)
                        <option value="{{ $author->id }}" {{ request()->get('author_id') == $author->id ? 'selected' : '' }}>
                            {{ $author->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tags Multi-Select Dropdown -->
                <div class="col-md-3 mb-3">
                    <label for="tags" class="form-label">Tags</label>
                    <select class="form-control" name="tags[]" id="tags" multiple>
                        @foreach ($tags as $tag)
                        <option value="{{ $tag->id }}"
                            @if(in_array($tag->id, request()->get('tags', [])))
                            selected
                            @endif>
                            {{ $tag->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Published Date Filter -->
                <div class="col-md-2 mb-3">
                    <label for="published_on" class="form-label">Published Date</label>
                    <input type="date" class="form-control" name="published_on" id="published_on" value="{{ request()->get('published_on') }}">
                </div>

                <!-- Comment Count Filter -->
                <div class="col-md-2 mb-3">
                    <label for="comment_count" class="form-label">Comment Count</label>
                    <input type="number" class="form-control" name="comment_count" id="comment_count" placeholder="Enter comment count" value="{{ request()->get('comment_count') }}">
                </div>

            </div>

            <!-- Submit Button and Clear Filters Button -->
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary mt-3">Filter</button>
                <a href="{{ route('posts.index') }}" class="btn btn-secondary mt-3">Clear Filters</a>
            </div>
        </form>
    </div>

    <!-- Create New Post Button -->
    <div class="d-flex justify-content-end">
        <a href="{{ route('posts.create') }}" class="btn btn-primary mb-3">Create New Post</a>
    </div>

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
                    <button class="btn btn-info comment-btn"
                        data-toggle="modal"
                        data-target="#commentsModal"
                        data-post-id="{{ $post->id }}"
                        data-comments="{{ json_encode($post->comments->map(function($comment) { 
            return [
                'content' => $comment->content, 
                'author_name' => $comment->author->name,
                'created_at' => $comment->created_at->toFormattedDateString()  // Format the date as needed
            ]; 
        })) }}">
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
        {{ $posts->appends(request()->query())->links() }}
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


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select all the comment buttons
        const commentButtons = document.querySelectorAll('.comment-btn');

        // Iterate over each button
        commentButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                // Get the comments data from the button's data-comments attribute
                const commentsData = button.getAttribute('data-comments');

                // Parse the comments data into an array of objects
                const comments = JSON.parse(commentsData);

                // Build the HTML for displaying the comments
                let commentsHtml = '<ul>';
                comments.forEach(function(comment) {
                    commentsHtml += `
                        <li>
                            <strong>${comment.author_name}</strong>: ${comment.content} 
                            <br>
                            <small>Posted on: ${comment.created_at}</small>
                        </li>
                    `;
                });
                commentsHtml += '</ul>';

                // Insert the generated HTML into the modal's comment list container
                const commentsList = document.getElementById('comments-list');
                commentsList.innerHTML = commentsHtml;
            });
        });
    });
</script>