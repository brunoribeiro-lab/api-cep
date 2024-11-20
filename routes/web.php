<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EnderecoController;

Route::get('/cep/{cep}', [EnderecoController::class, 'consultarEndereco']);
Route::get('/cidades/{uf}', [EnderecoController::class, 'cidadesUF']);
Route::get('/cidade/{city}/{uf?}', [EnderecoController::class, 'cidadeUF']);