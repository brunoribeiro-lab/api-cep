<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Endereco extends Model
{

    protected $table = 'enderecos';

    public function scopeWithCidadeEstado(Builder $query): Builder
    {
        return $query->select([
            'enderecos.*',
            'cidade.cidade',
            'estado.uf',
            'estado.estado',
            'estado.regiao'
        ])
            ->join('cidade', 'enderecos.cidade', '=', 'cidade.id')
            ->join('estado', 'cidade.uf', '=', 'estado.uf');
    }

    public function scopeCep(Builder $query, string $cep): Builder
    {
        return $query->where('enderecos.cep', $cep);
    }
}
