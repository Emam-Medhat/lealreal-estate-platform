<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlogPostRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blog_posts,slug',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|string',
            'status' => 'required|in:draft,published,scheduled,archived',
            'published_at' => 'nullable|date|after_or_equal:now',
            'category_id' => 'nullable|exists:blog_categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:blog_tags,id',
            'is_featured' => 'boolean',
            'allow_comments' => 'boolean',
        ];
    }
}
