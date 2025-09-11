<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class UF implements Rule
{
    public function passes($attribute, $value)
    {
        return $this->isValid($value);
    }

    public function message()
    {
        return 'O UF fornecido é inválido.';
    }

    private function isValid($uf)
    {
        if (strlen($uf) !== 2)
            return false;

        // Lista de UF válidos do Brasil
        $UFValidos = [
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
        ];

        if (!in_array(strtoupper($uf), $UFValidos))
            return false;

        return true;
    }

}