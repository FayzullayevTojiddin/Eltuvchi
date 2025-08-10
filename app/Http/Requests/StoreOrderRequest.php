<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'route_id' => ['required', 'integer', 'exists:routes,id'],
            'date' => ['required', 'date_format:Y-m-d'],
            'time' => ['required', 'date_format:H:i'],
            'passengers' => ['required', 'integer', 'min:1'],
            'phone' => ['required', 'string', 'max:20'],
            'optional_phone' => ['nullable', 'string', 'max:20'],
            'note' => ['nullable', 'string'],
            'discount_id' => ['nullable', 'integer', 'exists:discounts,id'],
        ];
    }
}