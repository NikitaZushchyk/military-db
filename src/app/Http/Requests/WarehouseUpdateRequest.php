<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WarehouseUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $warehouseId = $this->route('warehouse')->id;
        return [
            'serial_number' => 'required|integer|min:1|unique:warehouses,serial_number' . $warehouseId,
            'equipment_type_id' => 'required|integer|exists:equipment_types,id',
            'status' => 'nullable|string|in:in_stock,issued,broken,lost',
        ];
    }
}
