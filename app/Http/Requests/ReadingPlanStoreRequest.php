<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReadingPlanStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'book_id' => ['required', 'integer', 'exists:books,id'],
            'status' => ['required', 'in:want,reading,done'],
            'target_date' => ['nullable', 'date', 'after_or_equal:today'],
            'started_at' => ['nullable', 'date'],
            'finished_at' => ['nullable', 'date', 'after_or_equal:started_at'],
        ];
    }

    public function messages(): array
    {
        return [
            'book_id.required' => '書籍を選択してください。',
            'book_id.exists' => '選択した書籍が存在しません。',
            'status.required' => '状態を選択してください。',
            'status.in' => '無効な状態です。',
            'target_date.after_or_equal' => '目標日は今日以降の日付を入力してください。',
            'finished_at.after_or_equal' => '読了日は開始日以降の日付を入力してください。',
        ];
    }
}
