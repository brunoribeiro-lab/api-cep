<?php

namespace App\Http\Requests;

use App\Rules\CEP;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ConsultarEnderecoRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $rawCep = $this->route('cep') ?? $this->input('cep');
        $cepNumeros = preg_replace('/\D/', '', (string) $rawCep);

        $this->merge(['cep' => $cepNumeros]);
    }

    public function rules(): array
    {
        return [
            'cep' => ['required', new CEP()],
        ];
    }

    public function messages(): array
    {
        return [
            'cep.required' => 'CEP não fornecido',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(['error' => $validator->errors()->first()], 400)
        );
    }
}