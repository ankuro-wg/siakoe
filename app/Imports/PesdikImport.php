<?php

namespace App\Imports;

use App\Pesdik;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PesdikImport implements ToModel
{
    /**
     * @param array @row
     * 
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Pesdik([
            'status'            => $row['1'],
            'nama'              => $row['2'],
            'jenis_kelamin'     => $row['3'],
            'nisn'              => $row['4'],
            'induk'             => $row['5'],
            'rombel_id'         => $row['6'],
            'tempat_lahir'      => $row['7'],
            'tanggal_lahir'     => $row['8'],
            'jenis_pendaftaran' => $row['9'],
            'tanggal_masuk'     => $row['10'],
        ]);
    }
}
