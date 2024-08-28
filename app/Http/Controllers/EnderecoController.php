<?php

namespace App\Http\Controllers;

use App\Models\Endereco;
use App\Models\Cidade;
use Illuminate\Http\Request;

class EnderecoController extends Controller {

    public function consultarEndereco(Request $request, $cep) {
        if (!$cep)
            return response()->json(['error' => 'CEP não fornecido'], 400);

        $cepNumeros = preg_replace('/[^0-9]/', '', trim($cep));
        if (strlen($cepNumeros) !== 8)
            return response()->json(['error' => 'CEP informado não é válido'], 400);

        $endereco = Endereco::select('enderecos.*', 'cidade.cidade', 'estado.uf', 'estado.estado', 'estado.regiao')
                ->join('cidade', 'enderecos.cidade', '=', 'cidade.id')
                ->join('estado', 'cidade.uf', '=', 'estado.uf')
                ->where('enderecos.cep', $cepNumeros)
                ->first();

        if (is_null($endereco)) 
            return response()->json(['error' => sprintf('Nenhum Endereço foi encontrado com o CEP fornecido: %s', $cepNumeros)], 404);

        return response()->json($endereco);
    }

    public function cidadesUF($uf){
        if (!$uf)
            return response()->json(['error' => 'UF não fornecido'], 400);

        if (strlen($uf) !== 2)
            return response()->json(['error' => 'UF informado não é válido'], 400);

       $cidades = Cidade::where("uf", strtoupper($uf))->orderBy("cidade")->get();
       if (is_null($cidades)) 
            return response()->json(['error' => sprintf('Nenhuma Cidade foi encontrada com o UF fornecido: %s', $uf)], 404);

        return response()->json($cidades);
    }
}
