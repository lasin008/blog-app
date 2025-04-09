<?php

namespace App\Repositories;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;

class CommentRepository extends AbstractRepository
{
    /**
     * CommentRepository constructor.
     *
     * @param Comment $comment
     */
    public function __construct(Comment $comment)
    {
        parent::__construct($comment);
    }

    /**
     * Find comments by post id.
     *
     * @param int $postId
     * @return Collection
     */
    public function findByPost(int $postId): Collection
    {
        return $this->model->with('author')
            ->where('post_id', $postId)
            ->get();
    }
}

