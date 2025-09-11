<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EnderecoController;

// Endpoint Documentação
Route::get('/', function () {
    return view('index');
});

// Endpoints da API
Route::get('/cep/{cep?}', [EnderecoController::class, 'consultarEndereco'])->name('consultarEndereco');
Route::get('/cidades/{uf}', [EnderecoController::class, 'cidadesUF']);
Route::get('/cidade/{city}/{uf?}', [EnderecoController::class, 'cidadeUF']);