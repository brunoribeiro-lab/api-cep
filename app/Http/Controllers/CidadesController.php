<?php

namespace App\Http\Controllers;

use App\Http\Requests\CidadeUFRequest;
use App\Models\Cidade;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CidadesController extends Controller
{
    /**
     * Lista as cidades de um estado (UF) fornecido.
     * 
     * @access public
     * @param CidadeUFRequest $request
     * @throws ModelNotFoundException
     * @return JsonResponse
     */
    public function index(CidadeUFRequest $request): JsonResponse
    {
        try {
            $uf = $request->validated()['uf'];
            $cidades = Cidade::byUF($uf)->get();

            if ($cidades->isEmpty())
                throw new ModelNotFoundException("Nenhuma Cidade foi encontrada com o UF fornecido: $uf");

            return response()->json($cidades->pluck('cidade'));
            
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Ocorreu um erro ao processar a solicitação.'], 500);
        }
    }

}
