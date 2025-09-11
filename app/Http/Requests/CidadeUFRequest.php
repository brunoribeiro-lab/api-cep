<?php

namespace App\Http\Requests;

use App\Rules\UF;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CidadeUFRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $rawUF = $this->route('uf') ?? $this->input('uf');

        if ($rawUF !== null && $rawUF !== '')
            $this->merge(['uf' => strtoupper($rawUF)]);
    }

    public function rules(): array
    {
        return [
            'uf' => ['required', new UF()],
        ];
    }

    public function messages(): array
    {
        return [
            'uf.required' => 'UF não fornecido',
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            response()->json(['error' => $validator->errors()->first()], 400)
        );
    }
}