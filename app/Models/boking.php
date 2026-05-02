<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Table('bokings')]
#[Fillable(['id_lapangan', 'nama_pemesan', 'tanggal', 'jam_mulai', 'jam_selesai', 'status'] )]
class boking extends Model
{
    function lapangan()
    {
        return $this->belongsTo(lapangan::class, 'id_lapangan');
    }
}
