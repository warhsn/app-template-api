<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewProduct extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|unique:products,name',
            'product_type_id' => 'required|exists:product_types,id',
            'service_id' => 'required|exists:services,id',
            'prices' => 'required|array|min:1',
        ];
    }
}
