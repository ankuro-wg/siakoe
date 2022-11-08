<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Penarikan extends Model
{
    protected $table = 'penarikan';
    protected $fillable = ['guru_id', 'tanggal', 'jumlah', 'keterangan', 'users_id'];

    public function users()
    {
        return $this->belongsTo('App\User');
    }

    public function guru()
    {
        return $this->belongsTo('App\Guru');
    }
}
