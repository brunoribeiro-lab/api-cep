<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConsultarEnderecoRequest;
use App\Models\Endereco;
use App\Models\Cidade;

class EnderecoController extends Controller
{

    public function consultarEndereco(ConsultarEnderecoRequest $request)
    {
        $cepNumeros = $request->validated()['cep'];
        $endereco = Endereco::select('enderecos.*', 'cidade.cidade', 'estado.uf', 'estado.estado', 'estado.regiao')
            ->join('cidade', 'enderecos.cidade', '=', 'cidade.id')
            ->join('estado', 'cidade.uf', '=', 'estado.uf')
            ->where('enderecos.cep', $cepNumeros)
            ->first();

        if (is_null($endereco))
            return response()->json(['error' => sprintf('Nenhum Endereço foi encontrado com o CEP fornecido: %s', $cepNumeros)], 404);

        return response()->json($endereco);
    }

    public function cidadesUF($uf)
    {
        if (!$uf)
            return response()->json(['error' => 'UF não fornecido'], 400);

        if (strlen($uf) !== 2)
            return response()->json(['error' => 'UF informado não é válido'], 400);

        $cidades = Cidade::where("uf", strtoupper($uf))->orderBy("cidade")->get();

        if (!count($cidades->toArray()))
            return response()->json(['error' => sprintf('Nenhuma Cidade foi encontrada com o UF fornecido: %s', $uf)], 404);

        return response()->json($cidades->pluck('cidade'));
    }

    public function cidadeUF($city, $uf = null)
    {
        if (!$city)
            return response()->json(['error' => 'Cidade não fornecida'], 400);

        if ($uf && strlen($uf) !== 2)
            return response()->json(['error' => 'UF informado não é válido'], 400);

        $query = Cidade::where('cidade', $city);
        if ($uf) {
            $query->where('uf', strtoupper($uf));
        }

        $cidades = $uf ? $query->first() : $query->get();
        if (!count($cidades->toArray())) {
            $mensagemErro = $uf
                ? sprintf('Nenhuma cidade encontrada com o nome "%s" e UF "%s".', $city, strtoupper($uf))
                : sprintf('Nenhuma cidade encontrada com o nome "%s".', $city);

            return response()->json(['error' => $mensagemErro], 404);
        }

        return response()->json($cidades);
    }
}
