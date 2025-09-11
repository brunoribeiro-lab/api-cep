<?php

use App\Http\Controllers\CEPController;
use App\Http\Controllers\CidadeController;
use App\Http\Controllers\CidadesController;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

// Endpoint Documentação
Route::get('/', function (): View {
    return view('index');
});

// Busca endereço por CEP
Route::get('/cep/{cep?}', [CEPController::class, 'index'])->name('consultarEndereco');
// listar cidades por UF
Route::get('/cidades/{uf?}', [CidadesController::class, 'index'])->name('cidadesUF');
// consultar cidade por nome e opcionalmente por UF
Route::get('/cidade/{city?}/{uf?}', [CidadeController::class, 'index'])->name('cidadeUF');