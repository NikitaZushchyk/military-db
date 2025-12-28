<?php

namespace App\Http\Requests;

use App\Models\Warehouse;
use Illuminate\Foundation\Http\FormRequest;

class AssignmentReturnRequest extends FormRequest
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
            'warehouse_id' => [
                'required',
                'integer',
                'exists:warehouses,id',
                function ($attribute, $value, $fail) {
                    $item = Warehouse::find($value);
                    if ($item && $item->status !== 'issued') {
                        $fail('Цей предмет не можна повернути, бо він зараз на складі або списаний.');
                    }
                },
            ],
        ];
    }
}
