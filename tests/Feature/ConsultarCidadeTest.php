<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;

class ConsultarCidadeTest extends TestCase
{
    private function requestCidade(string $mode, ?string $city = null, ?string $uf = null): TestResponse
    {
        if ($mode === 'path') {
            if ($city === null || $city === '') {
                return $this->getJson(route('cidadeUF', ['city' => '', 'uf' => '']));
            }
            return is_null($uf)
                ? $this->getJson(route('cidadeUF', ['city' => $city]))
                : $this->getJson(route('cidadeUF', ['city' => $city, 'uf' => $uf]));
        }

        if ($city === null || $city === '') {
            return $this->getJson(route('cidadeUF', ['city' => '', 'uf' => '']));
        }
        $query = '?city=' . urlencode($city);
        if (!is_null($uf)) {
            $query .= '&uf=' . urlencode($uf);
        }
        return $this->getJson(route('cidadeUF') . $query);
    }

    public static function modesProvider(): array
    {
        return [['path'], ['query']]; // /cidade/{city}/{uf?} e /cidade?city=&uf=
    }

    public static function invalidCitiesProvider(): array
    {
        return [
            ['path', '', null, 400],
            ['query', '', null, 400],
            ['path', "' OR '1'='1", null, 400], // SQLi
            ['query', "' OR '1'='1", null, 400],
            // XSS apenas via query (contém '/')
            ['query', "<script>alert('XSS');</script>", null, 400],
        ];
    }

    public static function invalidUfsProvider(): array
    {
        return [
            ['path', 'São Paulo', 'X', 400],
            ['query', 'São Paulo', 'X', 400],
            ['path', 'São Paulo', 'SPX', 400], // 3 letras
            ['query', 'São Paulo', 'SPX', 400],
            ['path', 'São Paulo', '123', 400], // apenas números
            ['query', 'São Paulo', '123', 400],
            ['path', 'São Paulo', "' OR '1'='1", 400], // SQLi
            ['query', 'São Paulo', "' OR '1'='1", 400],
            // XSS apenas via query (contém '/')
            ['query', 'São Paulo', "<script>alert('XSS');</script>", 400],
        ];
    }
    private function expectedSaoPauloList(): array
    {
        return [
            ['cidade' => 'São Paulo', 'uf' => 'SP'],
            ['cidade' => 'São Paulo', 'uf' => 'RS']
        ];
    }

    private function expectedSantaMariaList(): array
    {
        return [
            ['cidade' => 'Santa Maria', 'uf' => 'RS'],
            ['cidade' => 'Santa Maria', 'uf' => 'SC'],
            ['cidade' => 'Santa Maria', 'uf' => 'RJ']
        ];
    }

    private function assertInvalidCity(TestResponse $response, int $status = 400): void
    {
        $response->assertStatus($status);
        $msg = $response->json('error');
        $this->assertIsString($msg);
        $this->assertContains($msg, [
            'Cidade não fornecida',
            'UF informado não é válido',
            'Nenhuma cidade encontrada com o nome "CidadeInexistente".',
            'Nenhuma cidade encontrada com o nome "São Paulo" e UF "XX".',
        ]);
    }

    #[DataProvider('modesProvider')]
    public function test_retorna_200_e_lista_para_sao_paulo(string $mode): void
    {
        $this->requestCidade($mode, 'São Paulo')
            ->assertOk()
            ->assertExactJson($this->expectedSaoPauloList());
    }

    #[DataProvider('modesProvider')]
    public function test_retorna_200_e_lista_para_santa_maria(string $mode): void
    {
        $this->requestCidade($mode, 'Santa Maria')
            ->assertOk()
            ->assertExactJson($this->expectedSantaMariaList());
    }

    #[DataProvider('modesProvider')]
    public function test_retorna_200_e_objeto_para_santa_maria_com_uf(string $mode): void
    {
        $this->requestCidade($mode, 'Santa Maria', 'SC')
            ->assertOk()
            ->assertExactJson(['cidade' => 'Santa Maria', 'uf' => 'SC']);
    }
}