<?php

namespace App\Exports;

use App\Setor;
use App\Tarik;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SetorTarikExportExcelHB implements FromView, ShouldAutoSize
{
    private $data_setor;
    private $total_setor;
    private $data_tarik;
    private $total_tarik;

    public function __construct($data_setor, $total_setor, $data_tarik, $total_tarik)
    {
        $this->data_setor = $data_setor;
        $this->total_setor = $total_setor;
        $this->data_tarik = $data_tarik;
        $this->total_tarik = $total_tarik;
    }

    public function view(): view
    {
        return view('laporankeuangan.setortariktunai.DownloadExcelHB', [          
            'data_setor' => $this->data_setor,
            'total_setor' => $this->total_setor,
            'data_tarik' => $this->data_tarik,
            'total_tarik' => $this->total_tarik
        ]);
    }
}
