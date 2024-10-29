<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'user_id' => ['required', 'exists:users,id'],
            'total_amount' => ['required', 'numeric'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'shipping' => ['required', 'array'],
            'shipping.address_line1' => ['required', 'string'],
            'shipping.address_line2' => ['string'],
            'shipping.city' => ['required', 'string'],
            'shipping.state' => ['required', 'string'],
            'shipping.postal_code' => ['required', 'string'],
            'shipping.country' => ['string'],
            'shipping.phone' => ['string'],
            'payment' => ['required', 'array'],
            'payment.method' => ['required', 'string'],
            'payment.amount' => ['required', 'numeric', 'min:0'],
        ];
    }
}
