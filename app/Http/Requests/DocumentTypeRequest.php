<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $nameRules = ['required', 'max:150'];

        $this->routeIs('documentTypes.create')
            ? $nameRules[] = 'unique:document_types,name'
            : $nameRules[] = Rule::unique('document_types', 'name')->ignore($this->route('documentType'));

        return [
            'name' => $nameRules,
        ];
    }
}
