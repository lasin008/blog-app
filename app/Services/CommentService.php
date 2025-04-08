<?php

namespace App\Services;

use App\Interfaces\CommentRepositoryInterface;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class CommentService
{
    protected $commentRepository;

    /**
     * Inject the CommentRepositoryInterface into the service.
     *
     * @param CommentRepositoryInterface $commentRepository
     */
    public function __construct(CommentRepositoryInterface $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    /**
     * Create a new comment.
     *
     * @param array $data
     * @return Comment
     */
    public function create(array $data): Comment
    {
        $comment = $this->commentRepository->create([...$data, 'user_id' => auth()->id()]);
        return $comment;
    }

    /**
     * Find a comment.
     *
     * @param int $id
     * @return Comment
     */
    public function find(int $id): Comment
    {
        return $this->commentRepository->find($id);
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
        $comment = $this->commentRepository->find($id);
        $this->commentRepository->update($id, $data);
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
        return $this->commentRepository->delete($id);
    }

    /**
     * Find comments by post ID.
     *
     * @param int $postId
     * @return Collection
     */
    public function findByPost(int $postId): Collection
    {
        return $this->commentRepository->findByPost($postId);
    }
}
