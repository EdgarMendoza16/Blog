<?php

namespace EdgarMendozaTech\Blog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'nick_name' => 'required|string|min:1|max:255',
            'first_name' => 'nullable|string|min:0|max:255',
            'last_name' => 'nullable|string|min:0|max:255',
            'slug' => 'required|string|min:1|max:255|unique:blog_authors,slug',
            'email' => 'nullable|string|email|max:255',
            'description' => 'nullable|string|max:400',
            'media_resource_id' => 'nullable|exists:media_resources,id',
            'status' => 'required|string|max:255',
            'published_at' => 'nullable|date',
            'template' => 'required|string|max:255',
            'meta_title' => 'string|max:255',
            'meta_description' => 'string|max:255',
            'meta_media_resource_id' => 'nullable|exists:media_resources,id',
        ];

        $author = $this->route('author');
        if($author !== null) {
            $rules['slug'] = 'required|string|min:1|max:255|unique:blog_authors,slug,'.$author->id;
        }

        return $rules;
    }
}
