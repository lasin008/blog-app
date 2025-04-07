<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Interfaces\PostRepositoryInterface;

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
     * @return LengthAwarePaginator
     */
    public function all(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Post::query();
        $this->applyFilters($query, $filters);
        return $query->paginate($perPage);
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
            if (empty($filterValue)) {
                continue;
            }

            // Apply each filter dynamically
            if ($filterKey === 'title') {
                $query->where('title', 'like', '%' . $filterValue . '%');
            } elseif ($filterKey === 'author_id') {
                $query->where('author_id', $filterValue);
            } elseif ($filterKey === 'tag') {
                $query->whereHas('tags', function ($q) use ($filterValue) {
                    $q->where('tags.name', 'like', '%' . $filterValue . '%');
                });
            } elseif ($filterKey === 'date_from') {
                $query->where('created_at', '>=', $filterValue);
            } elseif ($filterKey === 'date_to') {
                $query->where('created_at', '<=', $filterValue);
            }
        }
    }
}
