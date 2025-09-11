<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;

class ConsultarCidadesporUFTest extends TestCase
{

    private function requestCidades(string $mode, ?string $uf = null): TestResponse
    {
        if ($mode === 'path') {
            return ($uf === null || $uf === '')
                ? $this->getJson(route('cidadesUF', ['uf' => '']))
                : $this->getJson(route('cidadesUF', ['uf' => $uf]));
        }

        $query = is_null($uf) ? '' : ('?uf=' . urlencode($uf));
        return $this->getJson(route('cidadesUF') . $query);
    }


    public static function modesProvider(): array
    {
        return [['path'], ['query']]; // /cidades/{uf} e /cidades?uf=
    }

    public static function invalidUfsProvider(): array
    {
        return [
            ['path', 'X', 400],
            ['query', 'X', 400],
            ['path', 'SPX', 400], // 3 letras
            ['query', 'SPX', 400],
            ['path', '123', 400], // apenas números
            ['query', '123', 400],
            ['path', "' OR '1'='1", 400], // SQLi
            ['query', "' OR '1'='1", 400],
            // XSS apenas via query (contém '/')
            ['query', "<script>alert('XSS');</script>", 400],
        ];
    }
    
    private function expectedPayload(): array
    {
        return [
            'Arapiraca',
            'Maceió',
            'Palmeira dos Índios'
        ];
    }

    private function assertInvalidUf(TestResponse $response, int $status = 400): void
    {
        $response->assertStatus($status);
        $msg = $response->json('error');
        $this->assertIsString($msg);
    }

    #[DataProvider('modesProvider')]
    public function test_retorna_200_e_payload_esperado_para_uf_valido(string $mode): void
    {
        $this->requestCidades($mode, 'AL')
            ->assertOk()
            ->assertExactJson($this->expectedPayload());
    }
}