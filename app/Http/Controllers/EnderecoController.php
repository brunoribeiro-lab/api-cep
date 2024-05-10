<?php

namespace App\Http\Controllers;

use App\Models\Endereco;
use Illuminate\Http\Request;

class EnderecoController extends Controller {

    public function consultarEndereco(Request $request, $cep) {
        if (!$cep)
            return response()->json(['error' => 'CEP não fornecido'], 400);

        $cepNumeros = preg_replace('/[^0-9]/', '', trim($cep));
        $endereco = Endereco::select('enderecos.*', 'cidade.cidade', 'estado.uf', 'estado.estado', 'estado.regiao')
                ->join('cidade', 'enderecos.cidade', '=', 'cidade.id')
                ->join('estado', 'cidade.uf', '=', 'estado.uf')
                ->where('enderecos.cep', $cepNumeros)
                ->first();

        if (!$endereco)
            return response()->json(['error' => sprintf('Endereço não encontrado para o CEP fornecido %s',$cepNumeros)], 404);

        return response()->json($endereco);
    }
}
