<?php

namespace App\Repositories;

use App\Models\Comment;
use App\Interfaces\CommentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CommentRepository implements CommentRepositoryInterface
{
    /**
     * Get a list of all comments.
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return Comment::all();
    }

    /**
     * Find a comment by its ID.
     *
     * @param int $id
     * @return Comment
     */
    public function find(int $id): Comment
    {
        return Comment::findOrFail($id);
    }

    /**
     * Create a new comment.
     *
     * @param array $data
     * @return Comment
     */
    public function create(array $data): Comment
    {
        return Comment::create($data);
    }

    /**
     * Update an existing comment.
     *
     * @param int $id
     * @param array $data
     * @return Comment
     */
    public function update(int $id, array $data): Comment
    {
        $comment = $this->find($id);
        $comment->update($data);
        return $comment;
    }

    /**
     * Delete a comment by its ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $comment = $this->find($id);
        return $comment->delete();
    }
}
