<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShiftRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'start_time'=> 'required|date',
            'end_time'=> 'nullable|date',
            'opening_cash'=> 'required|numeric',
            'closing_cash'=> 'nullable|numeric',
            'expected_cash'=> 'nullable|numeric',
            'difference'=> 'nullable|numeric',
            'status' => 'nullable|string'
        ];
    }
}
