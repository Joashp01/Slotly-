<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'slot_id' => ['required', 'integer', 'exists:slots,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
