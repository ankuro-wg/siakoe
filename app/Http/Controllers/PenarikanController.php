<?php

namespace App\Http\Controllers;

use App\Penarikan;
use App\Guru;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenarikanController extends Controller
{
    public function index()
    {
        $data_tarik = \App\Penarikan::orderByRaw('tanggal DESC')->get();
        $data_guru = \App\Setoran::groupBy('guru_id')->orderByRaw('tanggal DESC')->get();
        return view('/tabunganguru/tarik/index', compact('data_tarik', 'data_guru'));
    }

    //function untuk tambah
    public function tambah(Request $request)
    {
        $pilih_guru = $request->input('guru_id');
        //Mengambil nilai rombel id
        //$pesdik = \App\Pesdik::select('rombel_id')->where('id', $pilih_pesdik)->get();
        //$data = $pesdik->first();
        //$rombel_id = $data->rombel_id;

        //menghitung saldo tabungan
        $total_setor = DB::table('setoran')->where('setoran.guru_id', '=', $pilih_guru)
            ->sum('setoran.jumlah');
        $total_tarik = DB::table('penarikan')->where('penarikan.guru_id', '=', $pilih_guru)
            ->sum('penarikan.jumlah');
        $saldo_tabungan = $total_setor - $total_tarik;
        $jumlah_penarikan = $request->input('jumlah');

        if ($jumlah_penarikan > $saldo_tabungan) {
            return redirect()->back()->with('warning', 'Maaf saldo tabungan siswa kurang dari nominal yang anda masukkan pada kolom jumlah, harap cek saldo tabungan siswa pada menu Data Peserta Didik !');
        } else {
            $request->validate([
                'jumlah' => 'numeric',
            ]);
            $data_tarik = new Penarikan();
            $data_tarik->guru_id             = $pilih_guru;
            $data_tarik->tanggal             = $request->input('tanggal');
            $data_tarik->jumlah              = $request->input('jumlah');
            $data_tarik->keterangan          = $request->input('keterangan');
            $data_tarik->users_id            = Auth::id();
            $data_tarik->save();
            // return redirect('/tabungan/tarik/index')->with("sukses", "Data Tarik Tunai Berhasil Ditambahkan");
            $tarik = \App\Penarikan::find($data_tarik->id);
            return view('/tabunganguru/tarik/cetak', compact('tarik'));
        }
    }

    //function untuk masuk ke view edit
    public function edit($id_tarik)
    {
        $tarik = \App\Penarikan::find($id_tarik);
        return view('/tabunganguru/tarik/edit', compact('tarik'));
    }

    public function update(Request $request, $id_tarik)
    {
        $request->validate([
            'jumlah' => 'numeric',
        ]);
        $tarik = \App\Penarikan::find($id_tarik);
        $tarik->update($request->all());
        $tarik->save();
        return redirect('/tabunganguru/tarik/index')->with('sukses', 'Data Tarik Tunai Berhasil Diedit');
    }

    //function untuk hapus
    public function delete($id)
    {
        $tarik = \App\Penarikan::find($id);
        $tarik->delete();
        return redirect('/tabunganguru/tarik/index')->with('sukses', 'Data Tarik Tunai Berhasil Dihapus');
    }

    //function untuk masuk ke view cetak
    public function cetak($id_tarik)
    {
        $tarik = \App\Penarikan::find($id_tarik);
        return view('/tabunganguru/tarik/cetak', compact('tarik'));
    }

    //function untuk masuk ke view cetak
    public function cetakprint($id_tarik)
    {
        $tarik = \App\Penarikan::find($id_tarik);
        return view('/tabunganguru/tarik/cetakprint', compact('tarik'));
    }

    public function gurundex($id)
    {
        $guru = \App\Guru::where('id', $id)->get();
        $id_guru_login = $guru->first();

        $data_guru = \App\Guru::where('id', $id)->get();
        $data_tarik = \App\Penarikan::where('guru_id', $id)->orderByRaw('tanggal DESC')->get();
        return view('/tabunganguru/tarik/guruindex', compact('data_guru', 'data_tarik', 'id_guru_login'));
    }
}
