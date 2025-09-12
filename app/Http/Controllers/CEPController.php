<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConsultarEnderecoRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Endereco;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Exception;

class CEPController extends Controller
{
    /**
     * Consulta um endereço pelo CEP fornecido.
     * 
     * @access public
     * @param ConsultarEnderecoRequest $request
     * @throws Exception
     * @return JsonResponse
     */
    public function index(ConsultarEnderecoRequest $request): JsonResponse
    {
        try {
            $cepNumeros = $request->validated()['cep'];

            $cacheKey = "endereco:cep:{$cepNumeros}";
            $endereco = Cache::remember($cacheKey, now()->addHour(), function () use ($cepNumeros): array {
                return Endereco::withCidadeEstado()
                    ->cep($cepNumeros)
                    ->firstOrFail()
                    ->toArray(); // armazena como array no cache
            });

            return response()->json($endereco);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => sprintf('Nenhum Endereço foi encontrado com o CEP fornecido: %s', $cepNumeros)], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocorreu um erro ao processar a solicitação.'], 500);
        }
    }
}
