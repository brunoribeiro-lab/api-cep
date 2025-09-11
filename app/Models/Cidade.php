<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Cidade extends Model
{

    protected $table = 'cidade';

    protected $hidden = ['id'];

    public function scopeByUF(Builder $query, string $uf): Builder
    {
        return $query->where('uf', strtoupper($uf))->orderBy('cidade');
    }
}
