<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionItemRequest extends FormRequest
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
            'transaction_id' => 'required|exists:transactions,id',
            'menu_item_id' => 'required|exists:menus,id',
            'quantity' => 'required|numeric',
            'price' => 'required|numeric',
            'kitchen_status' => 'nullable|in:pending,preparing,ready',
            'serving_status' => 'nullable|in:pending,served',
            'type' => 'nullable|in:order,preorder',
            'notes' => 'nullable|string|max:50',
            'bill_id' => 'nullable|exists:bills,id'
        ];
    }
}
