<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CEP implements Rule
{
    public function passes($attribute, $value)
    {
        return $this->isValid($value);
    }

    public function message()
    {
        return 'O CEP fornecido é inválido.';
    }

    private function isValid($cep)
    {
        $cep = preg_replace('/[^0-9]/', '', trim($cep)); // as vezes vem com hífen
        if (strlen($cep) !== 8)
            return false; 

        return true;
    }

}