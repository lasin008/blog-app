<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Post;
use App\Interfaces\PostRepositoryInterface;
use Illuminate\Contracts\Pagination\Paginator;


/**
 * Class PostRepository
 */
class PostRepository implements PostRepositoryInterface
{
    /**
     * Get a paginated list of posts with optional filters.
     *
     * @param int $perPage
     * @param array $filters
     * @return CursorPaginator
     */
    public function all(int $perPage = 15, array $filters = []): Paginator
    {
        $query = Post::query();
        $this->applyFilters($query, $filters);
        return $query->simplePaginate($perPage);
    }

    /**
     * Find a post by its ID.
     *
     * @param int $id
     * @return Post
     */
    public function find(int $id): Post
    {
        return Post::findorfail($id);
    }

    /**
     * Create a new post.
     *
     * @param array $data
     * @return Post
     */
    public function create(array $data): Post
    {
        return Post::create($data);
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
        $post = $this->find($id);
        $post->update($data);
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
        $post = $this->find($id);
        return $post->delete();
    }

    /**
     * Apply dynamic filters to the query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The query builder
     * @param array $filters The filters to apply to the query
     * @return void
     */
    protected function applyFilters($query, array $filters): void
    {
        foreach ($filters as $filterKey => $filterValue) {
            if ($filterValue === '' || $filterValue === null) {
                continue;
            }
            if ($filterKey === 'tags') {
                $query->whereHas('tags', function ($q) use ($filterValue) {
                    $q->whereIn('tags.id', (array) $filterValue);
                });
            } elseif ($filterKey === 'title') {
                $query->where('title', 'like', '%' . $filterValue . '%');
            } elseif ($filterKey === 'published_on') {
                $query->whereDate('created_at', $filterValue);
            } elseif ($filterKey === 'comment_count') {
                if ((int) $filterValue === 0) {
                    $query->whereDoesntHave('comments');
                } else {
                    $query->whereHas('comments', function ($q) use ($filterValue) {
                        $q->selectRaw('count(comments.id)')
                            ->groupBy('comments.post_id')
                            ->havingRaw('count(comments.id) = ?', [(int) $filterValue]);
                    });
                }
            }
        }
    }
}
