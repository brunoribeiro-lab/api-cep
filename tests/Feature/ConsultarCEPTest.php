<?php

namespace Tests\Feature;

use Illuminate\Support\Str;
use Tests\TestCase;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;

class ConsultarCEPTest extends TestCase
{
    private function requestCep(string $mode, ?string $cep = null): TestResponse
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
        return [['path'], ['query']]; // /cep/{cep} e /cep?cep=
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
            // XSS apenas via query (contém '/')
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

    private function assertInvalidCep(TestResponse $response, int $status = 400): void
    {
        $response->assertStatus($status);
        $msg = $response->json('error');
        $this->assertIsString($msg);
        $this->assertContains($msg, [
            'CEP informado não é válido',
            'O CEP fornecido é inválido.',
            'CEP não fornecido',
        ]);
    }

    #[DataProvider('modesProvider')]
    public function test_retorna_400_quando_cep_vazio(string $mode): void
    {
        $this->assertInvalidCep($this->requestCep($mode, ''), 400);
    }

    #[DataProvider('modesProvider')]
    public function test_retorna_404_quando_cep_de_8_digitos_nao_existe(string $mode): void
    {
        $cep = '12344321';
        $this->requestCep($mode, $cep)
            ->assertStatus(404)
            ->assertJson(['error' => "Nenhum Endereço foi encontrado com o CEP fornecido: $cep"]);
    }

    #[DataProvider('invalidCepsProvider')]
    public function test_retorna_400_para_ceps_invalidos(string $mode, string $cep, int $status): void
    {
        $this->assertInvalidCep($this->requestCep($mode, $cep), $status);
    }

    #[DataProvider('modesProvider')]
    public function test_retorna_200_e_payload_esperado_para_cep_valido(string $mode): void
    {
        $this->requestCep($mode, '01001-000')
            ->assertOk()
            ->assertExactJson($this->expectedPayload());
    }
}
