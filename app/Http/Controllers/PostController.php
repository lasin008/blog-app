<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostDetailsResource;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Services\PostService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostController extends Controller
{
    use AuthorizesRequests;

    protected $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * SHow posts view.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function showPosts(Request $request)
    {
        try {
            $authors = User::all();
            $tags = Tag::all();
            return view('posts.index', compact('authors', 'tags'));
        } catch (Exception $e) {
            Log::error('Error loading page: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Failed to load posts. Please try again later.');
        }
    }

    /**
     * Display a listing of the posts.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $posts = $this->postService->all($perPage, $request->all());
            return new PostCollection($posts);
        } catch (Exception $e) {
            Log::error('Error fetching posts: ' . $e->getMessage());
            return redirect()->route('posts.index')->with('error', 'Failed to load posts. Please try again later.');
        }
    }

    /**
     * Show the form for creating a new post.
     *
     * @return View
     */
    public function create()
    {
        $tags = Tag::all();
        return view('posts.create', compact('tags'));
    }

    /**
     * Show the post.
     *
     * @return View
     */
    public function show($id)
    {
        return view('posts.show', compact('id'));
    }

    /**
     * Find the post by id.
     *
     * @return View
     */
    public function find($id)
    {
        try {
            $post = $this->postService->find($id);
            return response()->json([
                'status' => 'success',
                'data' => new PostDetailsResource($post)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while retrieving the post. Please try again later.'
            ], 500);
        }
    }

    /**
     * Store a newly created post in the database.
     *
     * @param  \Illuminate\Http\StorePostRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StorePostRequest $request)
    {
        try {
            $this->postService->create($request->validated());
            return redirect()->route('home');
        } catch (\Exception $e) {
            Log::error('Error creating post: ' . $e->getMessage());
            return redirect()->route('posts.index')->with(
                'error',
                'Failed to create the post. Please try again later.'
            );
        }
    }

    /**
     * Show the form for editing a post.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Post $post)
    {
        $this->authorize('update', $post);
        $post = $this->postService->find($post->id);
        $tags = Tag::all();
        return view('posts.create', compact('post', 'tags'));
    }

    /**
     * Update the specified post in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(StorePostRequest $request, int $id)
    {
        try {
            $this->postService->update($id, $request->validated());
            return redirect()->route('posts.index');
        } catch (\Exception $e) {
            Log::error('Error updating the post: ' . $e->getMessage());
            return redirect()->route('posts.index')->with(
                'error',
                'Failed to update the post. Please try again later.'
            );
        }
    }

    /**
     * Remove the specified post from the database.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $post = $this->postService->find($id);
            $this->authorize('delete', $post);
            $this->postService->delete($id);
            return response()->json([
                'success' => true,
                'message' => 'Post deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while trying to delete the post. Please try again.'
            ], 500);
        }
    }
}
