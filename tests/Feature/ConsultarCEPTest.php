<?php

namespace Tests\Feature;

use Illuminate\Support\Str;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class ConsultarCEPTest extends TestCase
{
    private function requestCep(string $mode, ?string $cep = null)
    {
        if ($mode === 'path') {
            return ($cep === null || $cep === '')
                ? $this->getJson(route('consultarEndereco'))
                : $this->getJson(route('consultarEndereco', ['cep' => $cep]));
        }

        $query = is_null($cep) ? '' : ('?cep=' . urlencode($cep));
        return $this->getJson(route('consultarEndereco') . $query);
    }

    public static function modesProvider(): array
    {
        return [['path'], ['query']]; // Testa ambos os modos: via path e query ex: /cep/01001000 e /cep?cep=01001000
    }

    public static function invalidCepsProvider(): array
    {
        return [
            ['path', '1', 400],
            ['query', '1', 400],
            ['path', Str::password(8, true, false, false, false), 400], // 8 letras
            ['query', Str::password(8, true, false, false, false), 400],
            ['path', "' OR '1'='1", 400], // SQLi
            ['query', "' OR '1'='1", 400],
            // XSS apenas via query para não quebrar a rota (possui '/')
            ['query', "<script>alert('XSS');</script>", 400],
        ];
    }

    private function expectedPayload(): array
    {
        return [
            'cep' => '01001000',
            'rua' => 'Praça da Sé',
            'bairro' => 'Sé',
            'cidade' => 'São Paulo',
            'uf' => 'SP',
            'estado' => 'São Paulo',
            'regiao' => 'Sudeste',
        ];
    }

    #[DataProvider('modesProvider')]
    public function test_cep_vazio_retorna_400(string $mode): void
    {
        $this->requestCep($mode, '')
            ->assertStatus(400)
            ->assertJson(['error' => 'CEP não fornecido']);
    }

    #[DataProvider('modesProvider')]
    public function test_cep_inexistente_8d_retorna_404(string $mode): void
    {
        $cep = '12344321';

        $this->requestCep($mode, $cep)
            ->assertStatus(404)
            ->assertJson(['error' => "Nenhum Endereço foi encontrado com o CEP fornecido: $cep"]);
    }

    #[DataProvider('invalidCepsProvider')]
    public function test_ceps_invalidos_retorna_400(string $mode, string $cep, int $status): void
    {
        $this->requestCep($mode, $cep)
            ->assertStatus($status)
            ->assertJson(['error' => 'CEP informado não é válido']);
    }

    #[DataProvider('modesProvider')]
    public function test_cep_valido_retorna_payload_esperado(string $mode): void
    {
        $this->requestCep($mode, '01001-000')
            ->assertOk()
            ->assertExactJson($this->expectedPayload());
    }
}
