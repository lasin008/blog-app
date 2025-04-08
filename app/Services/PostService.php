<?php

namespace App\Services;

use App\Interfaces\PostRepositoryInterface;
use App\Models\Post;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PostService
{
    protected $postRepository;

    /**
     * Inject the PostRepositoryInterface into the service.
     *
     * @param PostRepositoryInterface $postRepository
     */
    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * Get a paginated list of posts with optional filters.
     *
     * @param int $perPage
     * @param array $filters
     * @return Paginator
     */
    public function all(int $perPage = 5, array $filters = []): Paginator
    {
        return $this->postRepository->all($perPage, $filters);
    }

    /**
     * Create a new post.
     *
     * @param array $data
     * @return Post
     */
    public function create(array $data): Post
    {
        $imagePath = isset($data['image']) ? $data['image']->store('images', 'public') : null;
        Log::info($imagePath);
        $post = $this->postRepository->create([
            'title' => $data['title'],
            'content' => $data['content'],
            'image' => $imagePath,
            'author_id' => auth()->id()
        ]);
        if (isset($data['tags']) && !empty($data['tags'])) {
            $post->tags()->sync($data['tags']);
        }
        return $post;
    }

    /**
     * Find a post.
     *
     * @param int $id
     * @return Post
     */
    public function find(int $id): Post
    {
        return $this->postRepository->find($id);
    }

    /**
     * Update an existing post.
     *
     * @param int $id
     * @param array $data
     * @return Post
     */
    public function update(int $id, array $data): Post
    {
        $post = $this->postRepository->find($id);
        if (isset($data['image']) && $data['image']) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $data['image'] = $data['image']->store('images', 'public');
        }
        $this->postRepository->update($id, $data);
        $post->tags()->sync($data['tags'] ?? []);
        return $post;
    }

    /**
     * Delete a post by its ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $post = $this->postRepository->find($id);
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }
        return $this->postRepository->delete($id);
    }
}
