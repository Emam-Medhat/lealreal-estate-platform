<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,' . $this->page->id,
            'content' => 'required|string',
            'featured_image' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'template' => 'required|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'show_in_menu' => 'boolean',
            'menu_title' => 'nullable|string|max:255',
        ];
    }
}
