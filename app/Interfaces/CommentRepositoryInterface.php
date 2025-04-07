<?php

namespace App\Interfaces;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;

interface CommentRepositoryInterface
{
    /**
     * Get a list of all comments.
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Find a comment by its ID.
     *
     * @param int $id
     * @return Comment
     */
    public function find(int $id): Comment;

    /**
     * Create a new comment.
     *
     * @param array $data
     * @return Comment
     */
    public function create(array $data): Comment;

    /**
     * Update an existing comment.
     *
     * @param int $id
     * @param array $data
     * @return Comment
     */
    public function update(int $id, array $data): Comment;

    /**
     * Delete a comment by its ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
