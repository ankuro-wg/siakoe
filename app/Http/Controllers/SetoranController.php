<?php

namespace App\Http\Controllers;

use App\Setoran;
use App\Guru;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SetoranController extends Controller
{
    public function index()
    {
        $data_setor = \App\Setoran::orderByRaw('tanggal DESC')->get();
        $data_guru = \App\Guru::orderByRaw('nama ASC')->get();
        return view('/tabunganguru/setor/index', compact('data_setor', 'data_guru'));
    }

    //function untuk tambah
    public function tambah(Request $request)
    {
        $pilih_guru = $request->input('guru_id');
        //Mengambil nilai rombel id
        //$pesdik = \App\Pesdik::select('rombel_id')->where('id', $pilih_pesdik)->get();
        //$data = $pesdik->first();
        //$rombel_id = $data->rombel_id;

        $request->validate([
            'jumlah' => 'numeric',
        ]);
        $data_setor = new Setoran();
        $data_setor->guru_id             = $pilih_guru;
        $data_setor->tanggal             = $request->input('tanggal');
        $data_setor->jumlah              = $request->input('jumlah');
        $data_setor->keterangan          = $request->input('keterangan');
        $data_setor->users_id            = Auth::id();
        $data_setor->save();
        $setor = \App\Setoran::find($data_setor->id);
        return view('/tabunganguru/setor/cetak', compact('setor'));
    }

    //function untuk masuk ke view edit
    public function edit($id_setor)
    {
        $setor = \App\Setoran::find($id_setor);
        return view('/tabunganguru/setor/edit', compact('setor'));
    }
    public function update(Request $request, $id_setor)
    {
        $request->validate([
            'jumlah' => 'numeric',
        ]);
        $setor = \App\Setoran::find($id_setor);
        $setor->update($request->all());
        $setor->save();
        return redirect('/tabunganguru/setor/index')->with('sukses', 'Data Setor Tunai Berhasil Diedit');
    }

    //function untuk hapus
    public function delete($id)
    {
        $setor = \App\Setoran::find($id);
        $setor->delete();
        return redirect('/tabunganguru/setor/index')->with('sukses', 'Data Setor Tunai Berhasil Dihapus');
    }

    //function untuk masuk ke view cetak
    public function cetak($id_setor)
    {
        $setor = \App\Setoran::find($id_setor);
        return view('/tabunganguru/setor/cetak', compact('setor'));
    }

    //function untuk masuk ke view cetak
    public function cetakprint($id_setor)
    {
        $setor = \App\Setoran::find($id_setor);
        return view('/tabunganguru/setor/cetakprint', compact('setor'));
    }

    public function guruindex($id)
    {
        $guru = \App\Guru::where('id', $id)->get();
        $id_guru_login = $guru->first();

        $data_guru = \App\Guru::where('id', $id)->get();
        $data_setor = \App\Setoran::where('guru_id', $id)->orderByRaw('tanggal DESC')->get();
        return view('/tabunganguru/setor/guruindex', compact('data_guru', 'data_setor', 'id_guru_login'));
    }
}
