<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTaskRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|required|in:pending,completed',
            'due_date' => 'sometimes|required|date_format:d-m-Y|after_or_equal:today',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The title field is required.',
            'title.max' => 'The title must not exceed 255 characters.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The status must be either pending or completed.',
            'due_date.required' => 'The due date field is required.',
            'due_date.date_format' => 'The due date format must be DD-MM-YYYY.',
            'due_date.after_or_equal' => 'The due date must not be in the past.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed',
            ], 422)
        );
    }
}
