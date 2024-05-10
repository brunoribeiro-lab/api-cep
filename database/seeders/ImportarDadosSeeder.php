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
        if (!File::isDirectory($diretorioSQL)) {
            $this->command->error('O diretório com arquivos SQL não foi encontrado.');
            return;
        }

        $arquivosSQL = File::files($diretorioSQL);
        if (!count($arquivosSQL)) {
            $this->command->error('Nenhum arquivo SQL foi encontrado na pasta database/seeders/sql.');
            return;
        }

        foreach ($arquivosSQL as $arquivoSQL) {
            $sql = File::get($arquivoSQL);
            DB::unprepared($sql);
            $nomeArquivo = $arquivoSQL->getFilename();
            $this->command->info("Dados do arquivo {$nomeArquivo} importados com sucesso.");
        }
        
        $this->command->info("Todos os dados foram importados com sucesso.");
    }
}
