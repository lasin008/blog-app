@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Posts</h1>

    <!-- Filters Section -->
    <div class="row mb-4">
        <form method="GET" action="{{ route('posts.index') }}" class="w-100">
            <div class="row g-3">
                <!-- Title Filter -->
                <div class="col-md-2">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control form-control-sm" name="title" id="title" placeholder="Search by title" value="{{ request()->get('title') }}">
                </div>

                <!-- Author Dropdown -->
                <div class="col-md-2">
                    <label for="author_id" class="form-label">Author</label>
                    <select class="form-select form-select-sm" name="author_id" id="author_id">
                        <option value="">Select Author</option>
                        @foreach ($authors as $author)
                        <option value="{{ $author->id }}" {{ request()->get('author_id') == $author->id ? 'selected' : '' }}>
                            {{ $author->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Published Date Filter -->
                <div class="col-md-2">
                    <label for="published_on" class="form-label">Published Date</label>
                    <input type="date" class="form-control form-control-sm" name="published_on" id="published_on" value="{{ request()->get('published_on') }}"
                        max="{{ date('Y-m-d') }}">
                </div>

                <!-- Comment Count Filter -->
                <div class="col-md-2">
                    <label for="comment_count" class="form-label">Comment Count</label>
                    <input type="number" class="form-control form-control-sm" name="comment_count" id="comment_count" placeholder="Enter count" value="{{ request()->get('comment_count') }}">
                </div>

                <!-- Tags Multi-Select Dropdown (Last) -->
                <div class="col-md-4">
                    <label for="tags" class="form-label">Tags</label>
                    <select class="form-select form-select-sm" name="tags[]" id="tags" multiple>
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
            </div>

            <!-- Submit Button and Clear Filters Button -->
            <div class="d-flex justify-content-end mt-3">
                <div class="d-flex w-25"> <!-- Create a flex container -->
                    <button type="submit" class="btn btn-primary btn-sm w-100 mr-2">Filter</button> <!-- w-100 for full width -->
                    <a href="{{ route('posts.index') }}" class="btn btn-secondary btn-sm w-100">Clear Filters</a> <!-- w-100 for full width -->
                </div>
            </div>
        </form>
    </div>

    <!-- Create New Post Button -->
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('posts.create') }}" class="btn btn-primary">Create New Post</a>
    </div>

    <!-- Posts Table -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">Title</th>
                <th scope="col">Author</th>
                <th scope="col">Comment Count</th>
                <th scope="col">Active</th>
                <th scope="col">Updated At</th>
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
                        data-post-id="{{ $post->id }}">
                        {{ $post->comments->count() }} Comments
                    </button>
                </td>
                <td>
                    <span class="badge {{ $post->active ? 'bg-success' : 'bg-danger' }}">
                        {{ $post->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td>{{ $post->updated_at->toFormattedDateString() }}</td>
                <td>
                    <!-- View button is always visible -->
                    <a href="{{ route('posts.show', $post->id) }}" class="btn btn-success btn-sm">View</a>

                    <!-- Edit and Delete buttons only visible to the post owner -->
                    @if ($post->author_id == auth()->id())
                    <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('posts.destroy', $post->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $posts->appends(request()->query())->links('pagination::bootstrap-4') }}
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
        const commentButtons = document.querySelectorAll('.comment-btn');
        commentButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const postId = button.getAttribute('data-post-id');
                const commentsList = document.getElementById('comments-list');
                commentsList.innerHTML = '<div class="text-center">Loading comments...</div>';
                fetch(`comment/${postId}`)
                    .then(response => response.json())
                    .then(comments => {
                        let commentsHtml = '<ul>';
                        comments.forEach(function(comment) {
                            commentsHtml += `
                                <li>
                                    <strong>${comment.author.name}</strong>: ${comment.content} 
                                    <br>
                                    <small>Posted on: ${new Date(comment.created_at).toLocaleDateString()}</small>
                                </li>
                            `;
                        });
                        commentsHtml += '</ul>';
                        commentsList.innerHTML = commentsHtml;
                    })
                    .catch(error => {
                        console.log(error);
                        commentsList.innerHTML = '<div class="text-danger">Failed to load comments. Please try again later.</div>';
                    });
            });
        });
    });
</script>