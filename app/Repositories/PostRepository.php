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
        $query->with(['author', 'comments']);
        $query->where(function ($query) {
            $query->where('is_active', true)
                ->orWhere('author_id', auth()->id());
        });
        $query->orderBy('updated_at', 'desc');
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
        return Post::with(['comments', 'tags', 'author'])->findOrFail($id);
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
        $query->when($filters['tags'] ?? null, function ($query, $tags) {
            $query->whereHas('tags', function ($q) use ($tags) {
                $q->whereIn('tags.id', (array) $tags);
            });
        })
            ->when($filters['title'] ?? null, function ($query, $title) {
                $query->where('title', 'like', '%' . $title . '%');
            })
            ->when($filters['published_on'] ?? null, function ($query, $published_on) {
                $query->whereDate('created_at', $published_on);
            })
            ->when($filters['author_id'] ?? null, function ($query, $author_id) {
                $query->where('author_id', '=', $author_id);
            })
            ->when($filters['comment_count'] ?? null, function ($query, $filterValue) {
                if ((int) $filterValue === 0) {
                    $query->whereDoesntHave('comments');
                } else {
                    $query->whereHas('comments', function ($q) use ($filterValue) {
                        $q->selectRaw('count(comments.id)')
                            ->groupBy('comments.post_id')
                            ->havingRaw('count(comments.id) = ?', [(int) $filterValue]);
                    });
                }
            });
    }
}
