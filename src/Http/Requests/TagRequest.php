<?php

namespace EdgarMendozaTech\Blog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|min:1|max:255',
            'slug' => 'required|string|min:1|max:255|unique:blog_tags,slug',
            'description' => 'nullable|string|max:400',
            'status' => 'required|string|max:255',
            'published_at' => 'nullable|date',
            'template' => 'required|string|max:255',
            'media_resource_id' => 'nullable|exists:media_resources,id',
            'meta_title' => 'string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'meta_media_resource_id' => 'nullable|exists:media_resources,id',
        ];

        $tag = $this->route('tag');
        if($tag !== null) {
            $rules['slug'] = 'required|string|min:1|max:255|unique:blog_tags,slug,'.$tag->id;
        }

        return $rules;
    }
}
