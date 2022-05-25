<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if ($this->method() == 'POST') {
            return [
                'title'             => 'required|string|unique:categories,title',
                'slug'              => 'nullable|string|unique:categories,slug',
                'parent_id'         => 'nullable|exists:categories,id',
                'thumbnail_width'   => 'nullable|numeric',
                'thumbnail_height'  => 'nullable|numeric',
                'fields'            => 'nullable|array',
                'fields.*.title'    => 'required_with:fields[0]|string',
                'fields.*.key'      => 'required_with:fields[0]|string',
                'fields.*.type'     => 'required_with:fields[0]|string|in:string,file',
                'fields.*.optional' => 'required_with:fields[0]|boolean',
            ];
        }

        return [
            'title'             => 'sometimes|string|unique:categories,title,'.$this->route('category')->id,
            'slug'              => 'nullable|string|unique:categories,slug,'.$this->route('category')->id,
            'parent_id'         => 'nullable|exists:categories,id',
            'thumbnail_width'   => 'nullable|numeric',
            'thumbnail_height'  => 'nullable|numeric',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if ( ! $this->slug) {
            $this->merge([
                'slug' => str_replace(' ', '-', $this->title),
            ]);
        } else {
            $this->merge([
                'slug' => str_replace(' ', '-', $this->slug),
            ]);
        }
    }
}
