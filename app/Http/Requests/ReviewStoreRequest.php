<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $book = $this->route('book');
            $exists = $book->reviews()->where('user_id', auth()->id())->exists();
            if ($exists) {
                $validator->errors()->add(
                    'rating',
                    'この書籍にはすでにレビューを投稿済みです。編集する場合は既存のレビューを編集してください。'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'rating.required' => '評価は必須です',
            'rating.integer' => '評価は整数で入力してください',
            'rating.between' => '評価は1〜5の間で入力してください',
            'comment.required' => 'コメントは必須です',
            'comment.max' => 'コメントは1000文字以内で入力してください',
        ];
    }
}
