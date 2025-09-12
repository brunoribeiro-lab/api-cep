<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConsultarCidadeRequest;
use App\Models\Cidade;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class CidadeController extends Controller
{

    /**
     * Lista a cidade pelo nome e opcionalmente pela UF
     * (geralmente usado para verificar se uma cidade existe)
     * 
     * @access public
     * @param ConsultarCidadeRequest $request
     * @return JsonResponse
     */
    public function index(ConsultarCidadeRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $city = $data['city'];
            $uf = $data['uf'] ?? null;

            $cacheKey = "cidade:city:{$city}" . ($uf ? "uf:{$uf}" : '');
            $cidades = Cache::remember($cacheKey, now()->addHour(), function () use ($city, $uf): array {
                $query = Cidade::ByCidade($city, $uf);
                return $uf ? $query->firstOrFail()->toArray() : $query->get()->toArray();
            }); 

            if (!$uf && empty($cidades))
                throw new ModelNotFoundException();

            return response()->json($cidades);

        } catch (ModelNotFoundException $e) {
            $mensagemErro = $uf
                ? sprintf('Nenhuma cidade encontrada com o nome "%s" e UF "%s".', $city, strtoupper($uf))
                : sprintf('Nenhuma cidade encontrada com o nome "%s".', $city);

            return response()->json(['error' => $mensagemErro], 404);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro interno no servidor.'], 500);
        }
    }
}
