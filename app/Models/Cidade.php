<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Cidade extends Model
{

    protected $table = 'cidade';

    protected $hidden = ['id'];

    public function scopeByCidade(Builder $query, string $city, ?string $uf = null): Builder
    {
        $query->where('cidade', $city);

        if ($uf !== null && $uf !== '') {
            $query->where('uf', strtoupper($uf));
        }

        return $query;
    } 

    public function scopeByUF(Builder $query, string $uf): Builder
    {
        return $query->where('uf', strtoupper($uf))->orderBy('cidade');
    }
}
