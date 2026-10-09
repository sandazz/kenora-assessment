<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\WorkshopStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateWorkshopRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role->canManageWorkshops();
    }

    public function rules(): array
    {
        $workshopId = $this->route('workshop')?->id;

        return [
            'code' => ['sometimes', 'required', 'string', 'max:20', "unique:workshops,code,{$workshopId}", 'regex:/^[A-Z]{2,6}-\d{2,6}$/i'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'instructor' => ['sometimes', 'required', 'string', 'max:255'],
            'location' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'starts_at' => ['sometimes', 'required', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'capacity' => ['sometimes', 'required', 'integer', 'min:1', 'max:1000'],
            'status' => ['sometimes', 'required', new Enum(WorkshopStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'code.regex' => 'The code must be in the format ABC-123 (letters, dash, numbers).',
            'ends_at.after' => 'The end time must be after the start time.',
        ];
    }
}
