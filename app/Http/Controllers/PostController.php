<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PostController extends Controller
{
    protected $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
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
            $filters = $request->only(['author_id', 'tag', 'created_at']);
            $posts = $this->postService->all(15, $filters);
            return view('posts.index', compact('posts'));
        } catch (\Exception $e) {
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
        $post = $this->postService->find($id);
        return view('posts.show', compact('post'));
    }

    /**
     * Store a newly created post in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $this->postService->create($this->validatePostData($request));
            return redirect()->route('posts.index');
        } catch (\Exception $e) {
            Log::error('Error creating post: ' . $e->getMessage());
            return redirect()->route('posts.index')->with('error', 'Failed to create the post. Please try again later.');
        }
    }

    /**
     * Show the form for editing a post.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(int $id)
    {
        $post = $this->postService->find($id);
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
    public function update(Request $request, int $id)
    {
        try {
            $this->postService->update($id, $this->validatePostData($request));
            return redirect()->route('posts.index');
        } catch (\Exception $e) {
            Log::error('Error updating the post: ' . $e->getMessage());
            return redirect()->route('posts.index')->with('error', 'Failed to update the post. Please try again later.');
        }
    }

    /**
     * Remove the specified post from the database.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id)
    {
        $this->postService->delete($id);
        return redirect()->route('posts.index');
    }

    /**
     * Validate the post data.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    protected function validatePostData(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id'
        ]);
    }
}
