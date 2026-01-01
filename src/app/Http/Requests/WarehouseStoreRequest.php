<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WarehouseStoreRequest extends FormRequest
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
            'serial_number' => [
                'required',
                'string',
                'unique:warehouses,serial_number',
                'regex:/^[a-z]{2}-\d{5}$/'
            ],
            'equipment_type_id' => 'required|integer|exists:equipment_types,id',
            'status' => 'nullable|string|in:in_stock,issued,broken',
        ];
    }
}
