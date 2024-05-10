<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EnderecoController;

Route::get('/{cep}', [EnderecoController::class, 'consultarEndereco']);
Route::get('/cep/{cep}', [EnderecoController::class, 'consultarEndereco']);