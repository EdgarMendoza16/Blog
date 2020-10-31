<?php

namespace EdgarMendozaTech\Blog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|min:1|max:255',
            'slug' => 'required|string|min:1|max:255|unique:blog_posts,slug',
            'description' => 'nullable|string|max:400',
            'content' => 'nullable',
            'media_resource_id' => 'nullable|exists:media_resources,id',
            'status' => 'required|string|max:255',
            'published_at' => 'nullable|date',
            'template' => 'required|string|max:255',
            'meta_title' => 'string|max:255',
            'meta_description' => 'string|max:255',
            'meta_media_resource_id' => 'nullable|exists:media_resources,id',
        ];

        $post = $this->route('post');
        if($post !== null) {
            $rules['slug'] = 'required|string|min:1|max:255|unique:blog_posts,slug,'.$post->id;
        }
        
        return $rules;
    }
}
