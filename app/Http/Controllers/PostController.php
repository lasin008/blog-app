<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
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
            $perPage = $request->get('per_page', 10);
            $posts = $this->postService->all($perPage, $request->all());
            $authors = User::all();
            $tags = Tag::all();
            return view('posts.index', compact('posts', 'tags', 'authors'));
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
        if ($post->author_id != auth()->id()) {
            return redirect()->route('posts.index')->with('error', 'You are not authorized to edit this post.');
        }
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
    public function update(Request $request, int $id)
    {
        try {
            $this->postService->update($id, $this->validatePostData($request));
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
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Post $post)
    {
        if ($post->author_id != auth()->id()) {
            return redirect()->route('posts.index')->with('error', 'You are not authorized to edit this post.');
        }
        $this->postService->delete($post->id);
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
            'tags.*' => 'exists:tags,id',
            'is_active' => 'nullable|boolean'
        ]);
    }
}
