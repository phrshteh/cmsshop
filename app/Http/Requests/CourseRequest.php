<?php

namespace App\Http\Requests;

use App\Models\Category;
use App\Models\ExtraField;
use App\Models\Media;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CourseRequest extends FormRequest
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
                'category_id'       => 'required|exists:categories,id',
                'title'             => 'required|string|unique:contents,title',
                'slug'              => 'nullable|string|unique:contents,slug',
                'description'       => 'nullable|string',
                'intro'             => 'nullable|string',
                'cover_id'          => 'nullable|exists:media,id',
                'fields'            => 'nullable|array',
                'fields.*.field_id' => 'required_with:fields[0]|exists:extra_fields,id',
                'fields.*.value'    => 'nullable',
                'tags'              => 'nullable|array',
                'tags.*'            => 'nullable|string',
                'price'             => 'required_with:is_buyable|numeric',
                'discount_price'    => 'nullable|numeric',
                'course_type'       => 'required|in:1,2,3,4', // infinitive
            ];
        }

        return [
            'category_id'       => 'sometimes|exists:categories,id',
            'title'             => 'sometimes|string|unique:contents,title,'.$this->route('content')->id,
            'slug'              => 'nullable|string|unique:contents,slug,'.$this->route('content')->id,
            'description'       => 'nullable|string',
            'intro'             => 'nullable|string',
            'cover_id'          => 'nullable|exists:media,id',
            'fields'            => 'nullable|array',
            'fields.*.field_id' => 'required_with:fields[0]|exists:extra_fields,id',
            'fields.*.value'    => 'nullable',
            'tags'              => 'sometimes|array',
            'tags.*'            => 'nullable|string',
            'is_buyable'        => 'sometimes',
            'price'             => 'required_with:is_buyable|numeric',
            'discount_price'    => 'nullable|numeric',
            'is_available'      => 'sometimes',
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @param  Validator  $validator
     *
     * @return void
     */
    public function withValidator($validator)
    {
        //TODO:: refactor it later

        $validator->after(
            function ($validator) {
                $category               = Category::findOrFail($validator->attributes()['category_id']);
                $extraFieldRequiredKeys = $category->extraFields()->where('optional', 0)->pluck('id')->toArray();

                if (array_key_exists('fields', $validator->attributes()) and count(
                        $validator->attributes()['fields']
                    )
                ) {
                    foreach ($validator->attributes()['fields'] as $attribute) {
                        $filledKeys[] = $attribute['field_id'];
                    }
                } else {
                    $filledKeys = [];
                }

                foreach ($extraFieldRequiredKeys as $requiredKey) {
                    if ( ! in_array($requiredKey, $filledKeys ?? [])) {
                        $extraFieldTitle = ExtraField::findOrFail($requiredKey)?->title;
                        $validator->errors()->add(
                            'string',
                            trans('messages.contents.store.failed.required', ['title' => $extraFieldTitle])
                        );
                    }
                }

                if (array_key_exists('fields', $validator->attributes()) and count(
                        $validator->attributes()['fields']
                    )
                ) {
                    foreach ($validator->attributes()['fields'] as $attribute) {
                        $extraField = ExtraField::where('id', $attribute['field_id'])->first();

                        if ($extraField->type == 'file' and ! Media::where('id', $attribute['value'])->first()
                            and ! $extraField->optional
                        ) {
                            $validator->errors()->add(
                                'file',
                                trans('messages.contents.store.failed.file', ['title' => $extraField?->title])
                            );
                        }

                        if ($extraField->type == 'string' and gettype($attribute['value']) != 'string'
                            and ! $extraField->optional
                        ) {
                            $validator->errors()->add(
                                'string',
                                trans('messages.contents.store.failed.string', ['title' => $extraField?->title])
                            );
                        }
                    }
                }
            }
        );
    }
}
