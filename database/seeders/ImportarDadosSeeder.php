<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportarDadosSeeder extends Seeder {

    /**
     * Run the database seeds.
     */
    public function run(): void {
        $diretorioSQL = database_path('seeders/sql');
        $this->command->comment("Lendo arquivos SQL na pasta {$diretorioSQL}.");
        if (!File::isDirectory($diretorioSQL)) {
            $this->command->error('O diretório com arquivos SQL não foi encontrado.');
            return;
        }

        $arquivosSQL = File::files($diretorioSQL);
        if (!count($arquivosSQL)) {
            $this->command->error('Nenhum arquivo SQL foi encontrado na pasta database/seeders/sql.');
            return;
        }

        $this->command->info(sprintf("Um total de (%d) arquivos foram carregados com sucesso.", count($arquivosSQL)));
        sort($arquivosSQL);
        foreach ($arquivosSQL as $arquivoSQL) {
            $sql = File::get($arquivoSQL);
            $nomeArquivo = $arquivoSQL->getFilename();
            $this->command->comment("Importando os dados do arquivo {$nomeArquivo}.");

            try {
                DB::unprepared($sql);
                $this->command->info("Dados do arquivo {$nomeArquivo} importados com sucesso.");
            } catch (\Exception $e) {
                $nomeArquivo = $arquivoSQL->getFilename();
                $this->command->error("Erro ao importar dados do arquivo {$nomeArquivo}: " . $e->getMessage());
            }
        }

        $this->command->info("Todos os dados foram importados com sucesso.");
    }
}
