<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Post;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use App\Filters\FilterManager;

/**
 * Class PostRepository
 */
class PostRepository extends AbstractRepository
{

    protected $filterManager;

    public function __construct(Post $post, FilterManager $filterManager)
    {
        $this->filterManager = $filterManager;
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
        $this->filterManager->apply($query, $filters);
        $query->where(function ($query) {
            $query->where('is_active', true)
                ->orWhere('author_id', auth()->id());
        });
        $query->orderBy('updated_at', 'desc');
        return $query->paginate($perPage);
    }
}
