<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Table('lapangans')]
#[Fillable(['nama_lapangan', 'harga_per_jam'])]
class lapangan extends Model
{
    //
    function bokings()
    {
        return $this->hasMany(boking::class, 'id_lapangan');
    }
}
