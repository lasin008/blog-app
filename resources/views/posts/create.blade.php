@extends('layouts.app')

@section('content')
<div class="container">
    <h1 id="form-heading">Create Post</h1>

    <form id="post-form" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="post_id" value="">
        <input type="hidden" id="remove_image" name="remove_image" value="0">

        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">Content</label>
            <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Image</label>
            <input type="file" class="form-control" id="image" name="image">

            <div id="image-container" class="mt-2" style="display: none;">
                <img id="current-image" alt="Current Image" style="width: 150px;">
                <button type="button" class="btn btn-danger btn-sm" id="remove-image-btn">
                    <i class="fas fa-times"></i> Remove Image
                </button>
            </div>
        </div>

        <div class="mb-3">
            <label for="tags" class="form-label">Tags</label>
            <select class="form-control" id="tags" name="tags[]" multiple>
                @foreach ($tags as $tag)
                    <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary" id="submit-btn">Create Post</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const pathnameParts = window.location.pathname.split('/');
    const postId = pathnameParts[pathnameParts.length - 1];
    const isEditMode = !isNaN(postId);
    const formHeading = document.getElementById('form-heading');
    const submitBtn = document.getElementById('submit-btn');
    const postForm = document.getElementById('post-form');
    const imageContainer = document.getElementById('image-container');
    const removeImageBtn = document.getElementById('remove-image-btn');
    const currentImage = document.getElementById('current-image');
    const tagSelect = document.getElementById('tags');

    if (isEditMode) {
        formHeading.textContent = 'Edit Post';
        submitBtn.textContent = 'Update Post';
        document.getElementById('post_id').value = postId;

        fetch(`/post/${postId}/get`)
            .then(response => response.json())
            .then(data => {
                const postData = data.data;
                document.getElementById('title').value = postData.title;
                document.getElementById('content').value = postData.content;

                const selectedTagIds = postData.tags.map(tag => tag.id);
                [...tagSelect.options].forEach(option => {
                    if (selectedTagIds.includes(parseInt(option.value))) {
                        option.selected = true;
                    }
                });

                if (postData.image) {
                    imageContainer.style.display = 'block';
                    currentImage.src = postData.image;
                }

                removeImageBtn.addEventListener('click', () => {
                    document.getElementById('remove_image').value = '1';
                    imageContainer.style.display = 'none';
                });
            });
    }

    postForm.addEventListener('submit', function (e) {
        e.preventDefault();
        submitBtn.disabled = true;

        const formData = new FormData(postForm);
        const url = isEditMode ? `/posts/${postId}` : '/posts';
        if (isEditMode) {
            formData.append('_method', 'PUT');
        }
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: formData,
            credentials: 'same-origin' // ensures cookies (auth) are sent
        })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) throw data;
            window.location.href = '/home';
        })
        .catch(error => {
            console.error(error);
            alert(error.message || 'Something went wrong.');
        })
        .finally(() => {
            submitBtn.disabled = false;
        });
    });
});
</script>
@endsection
