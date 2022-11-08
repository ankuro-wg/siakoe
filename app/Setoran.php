<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setoran extends Model
{
    protected $table = 'setoran';
    protected $fillable = ['guru_id', 'tanggal', 'jumlah', 'keterangan', 'users_id'];

    public function guru()
    {
        return $this->belongsTo('App\Guru');
    }

    public function users()
    {
        return $this->belongsTo('App\User');
    }
}
