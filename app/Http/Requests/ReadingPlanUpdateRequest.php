<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReadingPlanUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:want,reading,done'],
            'target_date' => ['nullable', 'date'],
            'started_at' => ['nullable', 'date'],
            'finished_at' => ['nullable', 'date', 'after_or_equal:started_at'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => '状態を選択してください。',
            'status.in' => '無効な状態です。',
            'finished_at.after_or_equal' => '読了日は開始日以降の日付を入力してください。',
        ];
    }
}
