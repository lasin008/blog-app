<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $post = $this->resource;
        $imageUrl = $post->image ? url('storage/' . $post->image) : null;
        return [
            'id' => $post->id,
            'title' => $post->title,
            'content' => $post->content,
            'tags' => $post->tags->isEmpty() ? [] : $post->tags->map(function ($tag) {
                return $tag->name;
            }),
            'image' => $imageUrl,
            'author' => $post->author->name,
            'comments' => $post->comments->isEmpty() ? [] : $post->comments->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'author' => $comment->author->name,
                    'created_at' => $comment->created_at->diffForHumans(), // Format date if needed
                ];
            }),
            'created_at' => $post->created_at->diffForHumans(), 
            'updated_at' => $post->updated_at->diffForHumans(),
        ];
    }
}
