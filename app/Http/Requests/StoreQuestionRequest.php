<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'question_text' => 'required|string',
            'marks' => 'required|numeric|min:0',
            'is_previous_year' => 'nullable|boolean',
            'previous_year' => 'nullable|integer',
            'options' => 'required|array|min:2',
            'options.*.text' => 'required|string',
            'options.*.is_correct' => 'nullable|boolean',
            'correct_option_index' => 'required|integer|min:0',
        ];
    }
}
