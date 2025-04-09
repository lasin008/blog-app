<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

/**
 * Class PostRepository
 */
class PostRepository extends AbstractRepository
{

    public function __construct(Post $post)
    {
        parent::__construct($post);
    }

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
        $query->with(['author', 'comments']);
        $this->applyFilters($query, $filters);
        $query->where(function ($query) {
            $query->where('is_active', true)
                ->orWhere('author_id', auth()->id());
        });
        $query->orderBy('updated_at', 'desc');
        return $query->paginate($perPage);
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
            } elseif ($filterKey === 'author_id') {
                $query->where('author_id', '=', $filterValue);
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
