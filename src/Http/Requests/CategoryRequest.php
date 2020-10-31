<?php

namespace EdgarMendozaTech\Blog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|min:1|max:255',
            'slug' => 'required|string|min:1|max:255|unique:blog_categories,slug',
            'description' => 'nullable|string|max:400',
            'media_resource_id' => 'nullable|exists:media_resources,id',
            'status' => 'required|string|max:255',
            'published_at' => 'nullable|date',
            'template' => 'required|string|max:255',
            'order' => 'required|numeric',
            'category_id' => 'nullable|exists:blog_categories,id',
            'meta_title' => 'string|max:255',
            'meta_description' => 'string|max:255',
            'meta_media_resource_id' => 'nullable|exists:media_resources,id',
        ];

        $category = $this->route('category');
        if($category !== null) {
            $rules['slug'] = 'required|string|min:1|max:255|unique:blog_categories,slug,'.$category->id;
        }

        return $rules;
    }
}
