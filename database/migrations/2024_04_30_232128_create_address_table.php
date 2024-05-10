<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('estado', function (Blueprint $table) {
            $table->string('uf', 2)->primary();
            $table->string('estado', 100);
            $table->enum('regiao', ['Norte', 'Nordeste', 'Centro-Oeste', 'Sudeste', 'Sul']);
        });

        Schema::create('cidade', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cidade', 100);
            $table->string('uf', 2);
            $table->foreign('uf')->references('uf')->on('estado');
        });

        Schema::create('enderecos', function (Blueprint $table) {
            $table->string('cep', 9)->primary();
            $table->string('rua', 100);
            $table->string('bairro', 100)->nullable();
            $table->unsignedInteger('cidade');
            $table->foreign('cidade')->references('id')->on('cidade');
        });

        // índices
        Schema::table('cidade', function (Blueprint $table) {
            $table->index('uf');
        });

        Schema::table('enderecos', function (Blueprint $table) {
            $table->index('cidade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('enderecos');
        Schema::dropIfExists('cidade');
        Schema::dropIfExists('estado');
    }
}
