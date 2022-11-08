<?php
  
namespace App\Imports;
  
use App\Guru;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
  
class UsersImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Guru([
            'nama'              => $row['1'],
            'jenis_kelamin'     => $row['2'],
            'tempat_lahir'      => $row['3'],
            'tanggal_lahir'     => $row['4'],
            'alamat'            => $row['5'],
            'no_hp'             => $row['6'],
            'email'             => $row['7'],
        ]);
    }
}
