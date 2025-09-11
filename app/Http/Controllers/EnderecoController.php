<?php

namespace App\Http\Controllers;

use App\Http\Requests\CidadeUFRequest;
use App\Http\Requests\ConsultarEnderecoRequest;
use App\Models\Endereco;
use App\Models\Cidade;
use Illuminate\Http\JsonResponse;

class EnderecoController extends Controller
{
    /**
     * Consulta um endereço pelo CEP fornecido.
     * 
     * @access public
     * @param \App\Http\Requests\ConsultarEnderecoRequest $request
     * @return JsonResponse
     */
    public function consultarEndereco(ConsultarEnderecoRequest $request): JsonResponse
    {
        $cepNumeros = $request->validated()['cep'];

        $endereco = Endereco::withCidadeEstado()
            ->cep($cepNumeros)
            ->first();

        if (!$endereco)
            return response()->json(['error' => sprintf('Nenhum Endereço foi encontrado com o CEP fornecido: %s', $cepNumeros)], 404);

        return response()->json($endereco);
    }

    public function cidadesUF(CidadeUFRequest $request): JsonResponse
    {
        $uf = $request->validated()['uf'];
        $cidades = Cidade::byUF($uf)->get();

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
