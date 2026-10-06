<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->isbn) {
            $this->merge(['isbn' => preg_replace('/[^0-9]/', '', $this->isbn)]);
        }
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'isbn' => [
                'required', 'string', 'size:13',
                Rule::unique('books', 'isbn')->ignore($this->route('book')->id),
            ],
            'published_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
            'image_url' => ['nullable', 'url'],
            'genre_ids' => ['required', 'array', 'min:1'],
            'genre_ids.*' => ['integer', 'exists:genres,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'タイトルは必須です',
            'author.required' => '著者名は必須です',
            'isbn.required' => 'ISBNは必須です',
            'isbn.size' => 'ISBNは13桁で入力してください（ハイフンは自動除去されます）',
            'isbn.unique' => 'このISBNはすでに登録されています',
            'genre_ids.required' => 'ジャンルを1つ以上選択してください',
            'genre_ids.min' => 'ジャンルを1つ以上選択してください',
        ];
    }
}
