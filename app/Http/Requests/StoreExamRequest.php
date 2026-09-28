<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExamRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'negative_marks_per_wrong' => 'nullable|numeric|min:0',
            'total_marks' => 'required|integer|min:0',
            'passing_marks' => 'required|integer|min:0|lte:total_marks',
            'max_attempts' => 'required|integer|min:1',
            'academic_year_id' => 'required|exists:school_academic_years,id',
            'school_class_id' => 'required|exists:school_classes,id',
            'school_section_id' => 'nullable|exists:school_sections,id',
        ];
    }
}
