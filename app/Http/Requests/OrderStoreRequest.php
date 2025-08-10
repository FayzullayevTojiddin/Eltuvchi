<?php

namespace App\Http\Requests;

use App\Models\ClientDiscount;
use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'route_id'       => ['required', 'exists:routes,id'],
            'scheduled_at'   => ['required', 'date', 'after_or_equal:now'],
            'passengers'     => ['required', 'integer', 'min:1'],
            'phone'          => ['required', 'string', 'max:20'],
            'optional_phone' => ['nullable', 'string', 'max:20'],
            'note'           => ['nullable', 'string', 'max:500'],
            'discount_id' => ['nullable', 'integer', 'exists:discounts,id'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->filled('discount_id')) {
                $clientId = $this->user()->client->id;

                $exists = ClientDiscount::where('client_id', $clientId)
                    ->where('discount_id', $this->discount_id)
                    ->where('used', false)
                    ->exists();

                if (! $exists) {
                    $validator->errors()->add(
                        'discount_id',
                        'Siz ushbu chegirmadan foydalana olmaysiz yoki u allaqachon ishlatilgan.'
                    );
                }
            }
        });
    }
}