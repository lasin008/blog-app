<?php

namespace App\Interfaces;

use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;

interface PostRepositoryInterface
{
    /**
     * Get a paginated list of posts with optional filters.
     *
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function all(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    /**
     * Find a post by its ID.
     *
     * @param int $id
     * @return Post
     */
    public function find(int $id): Post;

    /**
     * Create a new post.
     *
     * @param array
     * @return Post
     */
    public function create(array $data): Post;

    /**
     * Update an existing post.
     *
     * @param int $id
     * @param array $data
     * @return Post
     */
    public function update(int $id, array $data): Post;

    /**
     * Delete a post by its ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
