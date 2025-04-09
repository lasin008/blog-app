<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $post = $this->resource;
        return [
            'id' => $post->id,
            'title' => $post->title,
            'author' => $post->author->name,
            'status' => $post->is_active,
            'comment_count' => $post->comments->count(),
            'created_at' => $post->created_at,
            'updated_at' => $post->updated_at,
        ];
    }
}
