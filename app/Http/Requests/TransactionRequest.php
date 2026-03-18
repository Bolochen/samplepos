<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
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
            'notransaction' => 'required|string',
            'shift_id' => 'required|exists:shifts,id',
            'table_id' => 'nullable|exists:tables,id',
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'subtotal' => 'nullable|numeric',
            'tax' => 'nullable|numeric',
            'service_charge' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'status' => 'nullable|in:paid,unpaid,cancelled',
        ]; 
    }
}
