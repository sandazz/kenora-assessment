<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancelRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role->canHandleRegistrations();
    }

    public function rules(): array
    {
        return [
            'cancel_reason' => ['nullable', 'string', 'max:500'],
        ];
    }
}
