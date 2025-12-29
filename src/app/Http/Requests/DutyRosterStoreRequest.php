<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DutyRosterStoreRequest extends FormRequest
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
            'soldier_id' => 'required|exists:soldiers,id',
            'duty_type_id' => 'required|exists:duty_types,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ];
    }
}
