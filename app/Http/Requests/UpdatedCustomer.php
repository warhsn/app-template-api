<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $first_name
 * @property string $last_name
 * @property string $name
 * @property string $email
 * @property string? $billing_email
 * @property string? $company_name
 * @property string? $registration_number
 * @property string? $vat_number
 * @property int? $primary_user_id
 * @property int? $property_id
 * @property int? $billing_address_property_id
 * @property array<int> $user_ids
 */
class UpdatedCustomer extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'The full name is required',
            'last_name.required' => 'The full name is required',
        ];
    }
}
