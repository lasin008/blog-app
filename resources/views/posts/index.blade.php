@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Posts</h1>

    <!-- Filters Section -->
    <div class="row mb-4">
        <form method="GET" action="{{ route('posts.index') }}" class="w-100" id="filter-form">
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
                    <input type="date" class="form-control form-control-sm" name="published_on" id="published_on" value="{{ request()->get('published_on') }}">
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
            <div class="d-flex justify-content-between mt-3">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                <a href="{{ route('home') }}" class="btn btn-secondary btn-sm">Clear Filters</a>
            </div>
        </form>
    </div>

    <!-- Create New Post Button -->
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('posts.create') }}" class="btn btn-primary">Create New Post</a>
    </div>

    <!-- Posts Table -->
    <table class="table table-striped" id="posts-table">
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
            <!-- Posts will be dynamically loaded here -->
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="d-flex justify-content-center" id="pagination-links">
        <!-- Pagination links will be loaded here -->
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
        // Function to load posts with filters using Vanilla JS
        function loadPosts(page = 1, filters = {}) {
            const url = new URL("{{ route('posts.index') }}");
            const params = new URLSearchParams(filters);
            params.set('page', page);
            url.search = params.toString();
            console.log(url);
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    let postsHtml = '';
                    data.data.forEach(function(post) {
                        postsHtml += `
                            <tr>
                                <td>${post.title}</td>
                                <td>${post.author}</td>
                                <td>
                                    <button class="btn btn-info comment-btn"
                                        data-toggle="modal"
                                        data-target="#commentsModal"
                                        data-post-id="${post.id}">
                                        ${post.comment_count} Comments
                                    </button>
                                </td>
                                <td>
                                    <span class="badge ${post.status ? 'bg-success' : 'bg-danger'}">
                                        ${post.status ? 'Active' : 'Inactive'}
                                    </span>
                                </td>
                                <td>${new Date(post.updated_at).toLocaleDateString()}</td>
                                <td>
                                    <!-- View Button -->
                                    <a href="/posts/${post.id}" class="btn btn-success btn-sm">View</a>
                                    
                                    <!-- Edit Button -->
                                    <a href="/edit/${post.id}" class="btn btn-warning btn-sm">Edit</a>
                                    
                                    <!-- Delete Button -->
                                    <form action="/posts/${post.id}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        `;
                    });

                    document.querySelector('#posts-table tbody').innerHTML = postsHtml;

                    // Handle pagination links
                    let paginationHtml = '';
                    data.meta.links.forEach(link => {
                        paginationHtml += `<a href="javascript:void(0);" class="page-link" data-page="${link.label}">${link.label}</a>`;
                    });
                    document.getElementById('pagination-links').innerHTML = paginationHtml;
                })
                .catch(error => {
                    console.error('Error fetching posts:', error);
                });
        }

        // Load posts initially
        loadPosts(1);

        // On filter form change, reload posts with the current filters
        document.getElementById('filter-form').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent page reload
            const filters = {
                title: document.getElementById('title').value,
                author_id: document.getElementById('author_id').value,
                published_on: document.getElementById('published_on').value,
                comment_count: document.getElementById('comment_count').value,
                tags: Array.from(document.getElementById('tags').selectedOptions).map(option => option.value),
            };
            loadPosts(1, filters);
        });

        // Handle pagination click
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('page-link')) {
                const page = e.target.getAttribute('data-page');
                const filters = {
                    title: document.getElementById('title').value,
                    author_id: document.getElementById('author_id').value,
                    published_on: document.getElementById('published_on').value,
                    comment_count: document.getElementById('comment_count').value,
                    tags: Array.from(document.getElementById('tags').selectedOptions).map(option => option.value),
                };
                loadPosts(page, filters);
            }
        });

        // Comment button to show comments in modal
        document.addEventListener('click', function(e) {
            // Check if the clicked element is a comment button
            if (e.target && e.target.classList.contains('comment-btn')) {
                const postId = e.target.getAttribute('data-post-id');
                const commentsList = document.getElementById('comments-list');
                commentsList.innerHTML = '<div class="text-center">Loading comments...</div>';

                fetch(`/comment/${postId}`)
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
            }
        });

    });
</script>