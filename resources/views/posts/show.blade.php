@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <!-- Back to Home Button (top-left) -->
            <div class="mt-4">
                <a href="/home" class="btn btn-secondary btn-sm">Back to Home</a>
            </div>

            <div id="post-container">
                <!-- Post content will be loaded here via AJAX -->
            </div>

            <div class="mt-5" id="comments-container">
                <!-- Comments will be loaded here -->
            </div>

            <div class="mt-4 mb-10">
                <h4>Add a Comment</h4>
                <form id="comment-form" method="POST">
                    @csrf
                    <input type="hidden" name="post_id" id="post_id">
                    <div class="form-group">
                        <textarea class="form-control" name="content" id="comment-content" rows="4" placeholder="Add a comment..." required></textarea>
                    </div>

                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary btn-sm">Submit Comment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Extract the post ID from the URL
        const pathSegments = window.location.pathname.split('/');
        const postId = pathSegments[pathSegments.length - 1]; // Assuming the ID is the last part of the URL

        // Function to load post and comments via fetch
        function loadPost(postId) {
            fetch('/post/' + postId + '/get')
                .then(response => response.json())
                .then(data => {
                    // Load the post content dynamically
                    const postContainer = document.getElementById('post-container');
                    postContainer.innerHTML = `
                    <h1 class="text-center mb-4">${data.data.title}</h1>
                    <div class="card mb-4">
                        ${data.data.image ? `<img src="${data.data.image}" class="card-img-top" alt="Post Image" style="width: 100%; height: 350px; object-fit: cover;">` : ''}
                        <div class="card-body">
                            <p class="card-text">${data.data.content}</p>
                            <p class="text-muted">By ${data.data.author} | ${data.data.created_at}</p>
                            <div class="mt-3">
                                <strong>Tags:</strong>
                                <ul class="list-inline">
                                    ${data.data.tags.map(tag => `<li class="list-inline-item"><span class="badge badge-info">${tag.name}</span></li>`).join('')}
                                </ul>
                            </div>
                        </div>
                    </div>
                `;

                    const commentsContainer = document.getElementById('comments-container');
                    let commentsHtml = '';
                    if (data.data.comments.length > 0) {
                        commentsHtml = '<h3 class="mb-4">Comments</h3><ul class="list-group">';
                        data.data.comments.forEach(comment => {
                            commentsHtml += `
                            <li class="list-group-item mb-3" id="comment-${comment.id}">
                                <div class="d-flex justify-content-between">
                                    <strong>${comment.author}</strong>
                                    <small class="text-muted">${comment.created_at}</small>
                                </div>
                                <p class="mt-2">${comment.content}</p>
                                <!-- Delete button -->
                                <button class="btn btn-danger btn-sm delete-comment" data-id="${comment.id}">Delete</button>
                            </li>
                        `;
                        });
                        commentsHtml += '</ul>';
                    } else {
                        commentsHtml = '<p>No comments yet.</p>';
                    }
                    commentsContainer.innerHTML = commentsHtml;

                    // Store post ID for the comment form
                    document.getElementById('post_id').value = postId;

                    // Add event listeners to delete buttons
                    const deleteButtons = document.querySelectorAll('.delete-comment');
                    deleteButtons.forEach(button => {
                        button.addEventListener('click', function(event) {
                            const commentId = button.getAttribute('data-id');
                            deleteComment(commentId);
                        });
                    });
                })
                .catch(error => console.error('Error loading post:', error));
        }

        // Handle form submission (Add a comment)
        const commentForm = document.getElementById('comment-form');
        commentForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const postId = document.getElementById('post_id').value;
            const content = document.getElementById('comment-content').value;

            fetch('/comments', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        post_id: postId,
                        content: content
                    })
                })
                .then(response => response.json())
                .then(data => {
                    loadPost(postId); // Reload post and comments after adding new comment
                })
                .catch(error => {
                    console.error('Error submitting comment:', error);
                });
        });

        // Delete comment function
        function deleteComment(commentId) {
            fetch(`/comments/${commentId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    loadPost(postId);
                })
                .catch(error => {
                    console.error('Error deleting comment:', error);
                });
        }

        // Load the post when the page loads
        loadPost(postId);
    });
</script>
@endsection