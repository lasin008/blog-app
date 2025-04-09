<?php

namespace App\Http\Controllers;

use App\Services\CommentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CommentController extends Controller
{
    use AuthorizesRequests;

    protected $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * Create new comment
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'content' => 'required|string',
            'post_id' => 'required|exists:posts,id',
        ]);
        try {
            $this->commentService->create($data);
            return back()->with('success', 'Comment created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error creating comment: ' . $e->getMessage());
        }
    }

    /**
     * Update comment
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'content' => 'required|string',
        ]);

        try {
            $this->commentService->update($id, $data);
            return redirect()->route('comments.index')->with('success', 'Comment updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating comment: ' . $e->getMessage());
        }
    }

    /**
     * Delete comment
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $comment = $this->commentService->find($id);
            $this->authorize('delete', $comment);
            $this->commentService->delete($id);
            return back()->with('success', 'Comment deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting comment: ' . $e->getMessage());
        }
    }

    /**
     * Find comments by post id.
     *
     * @param int $postId
     * @return \Illuminate\Http\JsonResponse
     */
    public function findByPost(int $postId)
    {
        try {
            $comments = $this->commentService->findByPost($postId);
            return response()->json($comments);
        } catch (\Exception $e) {
            Log::error('Error fetching comments for post ' . $postId . ': ' . $e->getMessage());
            return response()->json([
                'error' => 'An error occurred while retrieving comments. Please try again later.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
