<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
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
    public function store(StoreCommentRequest $request)
    {
        try {
            $comment = $this->commentService->create($request->validated());
            return response()->json([
                'status' => 'success',
                'data' => $comment,
            ], 201);
        } catch (\Exception $e) {
            return $this->handleInternalError($e, 'Unable to create comment');
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
            return $this->handleInternalError($e, 'Unable to delete comment');
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
            return $this->handleInternalError($e, 'Unable to delete comment');
        }
    }
}
