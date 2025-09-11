<?php

namespace App\Http\Requests;

use App\Rules\UF;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ConsultarCidadeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $city = $this->route('city') ?? $this->query('city') ?? $this->input('city');
        $uf = $this->route('uf') ?? $this->query('uf') ?? $this->input('uf');

        $city = is_string($city) ? trim($city) : null;
        $uf = is_string($uf) ? strtoupper(trim($uf)) : null;

        $this->merge([
            'city' => $city,
            'uf' => $uf ?: null,
        ]);
    }

    public function rules(): array
    {
        // Nota: usamos size e in para customizar a mensagem da UF,
        // e ainda reaproveitamos a Rule UF.
        $ufs = implode(',', [
            'AC',
            'AL',
            'AP',
            'AM',
            'BA',
            'CE',
            'DF',
            'ES',
            'GO',
            'MA',
            'MT',
            'MS',
            'MG',
            'PA',
            'PB',
            'PR',
            'PE',
            'PI',
            'RJ',
            'RN',
            'RS',
            'RO',
            'RR',
            'SC',
            'SP',
            'SE',
            'TO'
        ]);

        return [
            'city' => [
                'required',
                'string',
                'max:100',
                // Apenas letras (com acentos), espaços, ponto e hífen
                'regex:/^[\p{L}\s\.\-]+$/u',
            ],
            'uf' => [
                'nullable',
                'string',
                'size:2',
                "in:$ufs",
                new UF(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'city.required' => 'Cidade não fornecida',
            'city.string' => 'Cidade não fornecida',
            'city.regex' => 'Cidade não fornecida',
            'city.max' => 'Cidade não fornecida',

            'uf.string' => 'UF informado não é válido',
            'uf.size' => 'UF informado não é válido',
            'uf.in' => 'UF informado não é válido',
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            response()->json(['error' => $validator->errors()->first()], 400)
        );
    }
}