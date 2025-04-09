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
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'content' => 'required|string',
            'post_id' => 'required|exists:posts,id',
        ]);
        try {
            $comment = $this->commentService->create($data);
            return response()->json([
                'status' => 'success',
                'message' => 'Comment created successfully.',
                'data' => $comment,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error creating comment: ' . $e->getMessage(),
            ], 500);
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $comment = $this->commentService->find($id);
            $this->authorize('delete', $comment);
            $this->commentService->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting comment: ' . $e->getMessage()
            ], 500);
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
