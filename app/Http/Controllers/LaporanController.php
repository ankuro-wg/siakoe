<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransaksiPembayaranExport;
use App\Exports\SetorTarikExport;
use App\Exports\SetorTarikExportExcelHB;
use App\Exports\KeuanganSekolahExport;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use App\Guru;
use App\Exports\PesdikExport;
use App\Imports\PesdikImport;
use App\Pesdik;
use Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class LaporanController extends Controller
{
    //Laporan Transaksi Pembayaran
    public function tPembayaranIndex()
    {
        $data_id_pesdik = \App\TransaksiPembayaran::select('pesdik_id')->groupBy('pesdik_id')->get();
        $data_id_rombel = \App\TransaksiPembayaran::select('rombel_id')->groupBy('rombel_id')->get();
        $tgl_1 = \App\TransaksiPembayaran::first();
        $tgl_2 = \App\TransaksiPembayaran::latest()->first();
        $tgl_awal = $tgl_1->created_at;
        $tgl_akhir = $tgl_2->created_at;

        $daftar_nama = \App\TransaksiPembayaran::groupBy('pesdik_id')->orderByRaw('pesdik_id DESC')->get();
        $daftar_kelas = \App\TransaksiPembayaran::groupBy('rombel_id')->orderByRaw('rombel_id DESC')->get();
        $data_transaksi = \App\TransaksiPembayaran::orderByRaw('created_at DESC')->get();
        $total_transaksi = \App\TransaksiPembayaran::all()->sum('jumlah_bayar');
        return view('/laporankeuangan/transaksipembayaran/index', compact('data_transaksi', 'daftar_nama', 'daftar_kelas', 'data_id_pesdik', 'data_id_rombel', 'tgl_awal', 'tgl_akhir'));
    }

    public function tPembayaranfilterByNama(Request $request)
    {
        $pesdik_id = $request->input('filterNama');

        $data_id_pesdik = \App\TransaksiPembayaran::select('pesdik_id')->where('pesdik_id', $pesdik_id)->get();
        $data_id_rombel = \App\TransaksiPembayaran::select('rombel_id')->groupBy('rombel_id')->get();
        $tgl_1 = \App\TransaksiPembayaran::first();
        $tgl_2 = \App\TransaksiPembayaran::latest()->first();
        $tgl_awal = $tgl_1->created_at;
        $tgl_akhir = $tgl_2->created_at;

        $daftar_nama = \App\TransaksiPembayaran::groupBy('pesdik_id')->orderByRaw('pesdik_id DESC')->get();
        $daftar_kelas = \App\TransaksiPembayaran::groupBy('rombel_id')->orderByRaw('rombel_id DESC')->get();
        $data_transaksi = \App\TransaksiPembayaran::where('pesdik_id', $pesdik_id)->orderByRaw('created_at DESC')->get();
        return view('/laporankeuangan/transaksipembayaran/index', compact('data_transaksi', 'daftar_nama', 'daftar_kelas', 'data_id_pesdik', 'data_id_rombel', 'tgl_awal', 'tgl_akhir'));
    }

    public function tPembayaranfilterByKelas(Request $request)
    {
        $id_rombel = $request->input('filterKelas');

        $data_id_pesdik = \App\TransaksiPembayaran::select('pesdik_id')->groupBy('pesdik_id')->get();
        $data_id_rombel = \App\TransaksiPembayaran::select('rombel_id')->where('rombel_id', $id_rombel)->get();
        $tgl_1 = \App\TransaksiPembayaran::first();
        $tgl_2 = \App\TransaksiPembayaran::latest()->first();
        $tgl_awal = $tgl_1->created_at;
        $tgl_akhir = $tgl_2->created_at;

        $daftar_nama = \App\TransaksiPembayaran::groupBy('pesdik_id')->orderByRaw('pesdik_id DESC')->get();
        $daftar_kelas = \App\TransaksiPembayaran::groupBy('rombel_id')->orderByRaw('rombel_id DESC')->get();
        $data_transaksi = \App\TransaksiPembayaran::where('rombel_id', $id_rombel)->orderByRaw('created_at DESC')->get();
        return view('/laporankeuangan/transaksipembayaran/index', compact('data_transaksi', 'daftar_nama', 'daftar_kelas', 'data_id_pesdik', 'data_id_rombel', 'tgl_awal', 'tgl_akhir'));
    }

    public function tPembayaranfilterByTanggal(Request $request)
    {
        $tgl_awal = $request->input('tgl_awal');
        $tgl_akhir = $request->input('tgl_akhir');

        $data_id_pesdik = \App\TransaksiPembayaran::select('pesdik_id')->groupBy('pesdik_id')->get();
        $data_id_rombel = \App\TransaksiPembayaran::select('rombel_id')->groupBy('rombel_id')->get();

        $daftar_nama = \App\TransaksiPembayaran::groupBy('pesdik_id')->orderByRaw('pesdik_id DESC')->get();
        $daftar_kelas = \App\TransaksiPembayaran::groupBy('rombel_id')->orderByRaw('rombel_id DESC')->get();
        $data_transaksi = \App\TransaksiPembayaran::whereBetween('created_at', [$tgl_awal, $tgl_akhir])->orderByRaw('created_at DESC')->get();
        return view('/laporankeuangan/transaksipembayaran/index', compact('data_transaksi', 'daftar_nama', 'daftar_kelas', 'data_id_pesdik', 'data_id_rombel', 'tgl_awal', 'tgl_akhir'));
    }

    public function tPembayaranDownloadExcel()
    {
        $namafile = 'Laporan_transaksi_pembayaran_siswa_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new TransaksiPembayaranExport, $namafile);
    }

    public function tPembayaranCetak(Request $request)
    {
        $inst = \App\Instansi::first();
        $data_id_pesdik = $request->id_pesdik;
        $data_id_rombel = $request->id_rombel;
        $tgl_awal = $request->input('tgl_awal');
        $tgl_akhir = $request->input('tgl_akhir');

        $data_transaksi = \App\TransaksiPembayaran::whereIn('pesdik_id', $data_id_pesdik)->whereIn('rombel_id', $data_id_rombel)->whereBetween('created_at', [$tgl_awal, $tgl_akhir])->get();
        $total_transaksi = \App\TransaksiPembayaran::whereIn('pesdik_id', $data_id_pesdik)->whereIn('rombel_id', $data_id_rombel)->whereBetween('created_at', [$tgl_awal, $tgl_akhir])->sum('jumlah_bayar');
        return view('/laporankeuangan/transaksipembayaran/cetak', compact('inst', 'data_transaksi', 'tgl_awal', 'tgl_akhir', 'total_transaksi'));
    }


    //Laporan Setor dan Tarik Tunai
    public function tSetorTarikIndex()
    {
        $data_id_pesdik = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->groupBy('pesdik_id')->orderByRaw('tanggal DESC')->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->groupBy('guru_id')->orderByRaw('tanggal DESC'))->orderByRaw('tanggal DESC')->get();
        $data_id_rombel = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->groupBy('nama_rombel')->orderByRaw('tanggal DESC')->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->groupBy('nama_rombel')->orderByRaw('tanggal DESC'))->orderByRaw('tanggal DESC')->get();
        $tgl_1 = \App\Setor::first();
        $tgl_awal = $tgl_1->created_at;
        $tgl_akhir = Carbon::now();

        $daftar_nama = \App\Setor::groupBy('pesdik_id')->orderByRaw('pesdik_id DESC')->get();
        $daftar_kelas = \App\Setor::groupBy('rombel_id')->orderByRaw('rombel_id DESC')->get();

        //$tabelpesdik = DB::raw("(select pesdik_id, nama INNER JOIN pesdik ON pesdik.id=setor.pesdik_id)) as tabelpesdik");
        //$tabelguru = DB::raw("(select guru_id, nama INNER JOIN guru ON guru.id=setoran.guru_id)) as tabelguru");

        $nama_nasabah = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'nama', 'nisn')->join('pesdik', 'pesdik.id', 'setor.pesdik_id'), 'tabelpesdik')->groupBy('pesdik_id')->orderByRaw('pesdik_id DESC')->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('guru_id', 'nama', 'no_hp')->join('guru', 'guru.id', 'setoran.guru_id'), 'tabelguru')->groupBy('guru_id')->orderByRaw('guru_id DESC'))->get();
        $kelas_nasabah = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->groupBy('nama_rombel')->orderByRaw('nama_rombel DESC')->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->groupBy('nama_rombel')->orderByRaw('nama_rombel DESC'))->get();
        //$nama_nasabah = \App\Setor::select($tabelpesdik)->groupBy('pesdik_id')->orderByRaw('pesdik_id DESC')->unionAll(\App\Setoran::select($tabelguru)->groupBy('guru_id')->orderByRaw('guru_id DESC'))->get();

        $data_setor = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->orderByRaw('tanggal DESC')->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->orderByRaw('tanggal DESC'))->orderByRaw('tanggal DESC')->get();
        $total_setor = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru'))->sum('jumlah');

        $data_tarik = \App\Tarik::select('*')->fromSub(\App\Tarik::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'tarik.pesdik_id')->join('rombel', 'rombel.id', 'tarik.rombel_id')->join('users', 'users.id', 'tarik.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->orderByRaw('tanggal DESC')->unionAll(\App\Penarikan::select('*')->fromSub(\App\Penarikan::select('penarikan.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'penarikan.guru_id')->join('users', 'users.id', 'penarikan.users_id')->join('rombel', 'rombel.guru_id', 'penarikan.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->orderByRaw('tanggal DESC'))->orderByRaw('tanggal DESC')->get();
        $total_tarik = \App\Tarik::select('*')->fromSub(\App\Tarik::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'tarik.pesdik_id')->join('rombel', 'rombel.id', 'tarik.rombel_id')->join('users', 'users.id', 'tarik.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->unionAll(\App\Penarikan::select('*')->fromSub(\App\Penarikan::select('penarikan.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'penarikan.guru_id')->join('users', 'users.id', 'penarikan.users_id')->join('rombel', 'rombel.guru_id', 'penarikan.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru'))->sum('jumlah');

        return view('/laporankeuangan/setortariktunai/index', compact('daftar_nama', 'daftar_kelas', 'nama_nasabah', 'kelas_nasabah', 'data_setor', 'total_setor', 'data_tarik', 'total_tarik', 'data_id_pesdik', 'data_id_rombel', 'tgl_awal', 'tgl_akhir'));
    }

    public function tSetorTarikfilterByNama(Request $request)
    {
        $pesdik_id = $request->input('filterNama');

        //$data_id_pesdik = \App\Setor::select('pesdik_id')->where('pesdik_id', $pesdik_id)->get();
        $data_id_pesdik = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->where('pesdik_id', $pesdik_id)->orderByRaw('tanggal DESC')->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->where('guru_id', $pesdik_id)->orderByRaw('tanggal DESC'))->orderByRaw('tanggal DESC')->get();
        $data_id_rombel = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->groupBy('nama_rombel')->orderByRaw('tanggal DESC')->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->groupBy('nama_rombel')->orderByRaw('tanggal DESC'))->orderByRaw('tanggal DESC')->get();

        $tgl_1 = \App\Setor::first();
        $tgl_awal = $tgl_1->created_at;
        $tgl_akhir = Carbon::now();

        $daftar_nama = \App\Setor::groupBy('pesdik_id')->orderByRaw('pesdik_id DESC')->get();
        $daftar_kelas = \App\Setor::groupBy('rombel_id')->orderByRaw('rombel_id DESC')->get();

        $nama_nasabah = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'nama', 'nisn')->join('pesdik', 'pesdik.id', 'setor.pesdik_id'), 'tabelpesdik')->groupBy('pesdik_id')->orderByRaw('pesdik_id DESC')->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('guru_id', 'nama', 'no_hp')->join('guru', 'guru.id', 'setoran.guru_id'), 'tabelguru')->groupBy('guru_id')->orderByRaw('guru_id DESC'))->get();
        $kelas_nasabah = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->groupBy('nama_rombel')->orderByRaw('nama_rombel DESC')->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->groupBy('nama_rombel')->orderByRaw('nama_rombel DESC'))->get();

        $data_setor = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->where('nisn', $pesdik_id)->orderByRaw('tanggal DESC')->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->where('no_hp', $pesdik_id)->orderByRaw('tanggal DESC'))->get();
        $total_setor = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru'))->where('nisn', $pesdik_id)->sum('jumlah');

        $data_tarik = \App\Tarik::select('*')->fromSub(\App\Tarik::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'tarik.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'tarik.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->where('nisn', $pesdik_id)->orderByRaw('tanggal DESC')->unionAll(\App\Penarikan::select('*')->fromSub(\App\Penarikan::select('penarikan.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'penarikan.guru_id')->join('users', 'users.id', 'penarikan.users_id')->join('rombel', 'rombel.guru_id', 'penarikan.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->where('no_hp', $pesdik_id)->orderByRaw('tanggal DESC'))->get();
        $total_tarik = \App\Tarik::select('*')->fromSub(\App\Tarik::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'tarik.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'tarik.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->unionAll(\App\Penarikan::select('*')->fromSub(\App\Penarikan::select('penarikan.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'penarikan.guru_id')->join('users', 'users.id', 'penarikan.users_id')->join('rombel', 'rombel.guru_id', 'penarikan.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru'))->where('nisn', $pesdik_id)->sum('jumlah');

        return view('/laporankeuangan/setortariktunai/index', compact('daftar_nama', 'daftar_kelas', 'nama_nasabah', 'kelas_nasabah', 'data_setor', 'total_setor', 'data_tarik', 'total_tarik', 'data_id_pesdik', 'data_id_rombel', 'tgl_awal', 'tgl_akhir'));
    }

    public function tSetorTarikfilterByKelas(Request $request)
    {
        $id_rombel = $request->input('filterKelas');

        //$data_id_pesdik = \App\Setor::select('pesdik_id')->groupBy('pesdik_id')->get();
        //$data_id_rombel = \App\Setor::select('rombel_id')->where('rombel_id', $id_rombel)->get();
        $data_id_pesdik = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->groupBy('pesdik_id')->orderByRaw('tanggal DESC')->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->groupBy('guru_id')->orderByRaw('tanggal DESC'))->get();
        $data_id_rombel = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->where('nama_rombel', $id_rombel)->orderByRaw('tanggal DESC')->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->where('nama_rombel', $id_rombel)->orderByRaw('tanggal DESC'))->get();
        $tgl_1 = \App\Setor::first();
        $tgl_awal = $tgl_1->created_at;
        $tgl_akhir = Carbon::now();

        $daftar_nama = \App\Setor::groupBy('pesdik_id')->orderByRaw('pesdik_id DESC')->get();
        $daftar_kelas = \App\Setor::groupBy('rombel_id')->orderByRaw('rombel_id DESC')->get();

        $nama_nasabah = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->groupBy('pesdik_id')->orderByRaw('pesdik_id DESC')->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->groupBy('guru_id')->orderByRaw('guru_id DESC'))->get();
        $kelas_nasabah = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->groupBy('nama_rombel')->orderByRaw('nama_rombel DESC')->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->groupBy('nama_rombel')->orderByRaw('nama_rombel DESC'))->get();

        $data_setor = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->where('nama_rombel', $id_rombel)->orderByRaw('tanggal DESC')->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->where('nama_rombel', $id_rombel)->orderByRaw('tanggal DESC'))->get();
        $total_setor = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru'))->where('nama_rombel', $id_rombel)->sum('jumlah');

        $data_tarik = \App\Tarik::select('*')->fromSub(\App\Tarik::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'tarik.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'tarik.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->where('nama_rombel', $id_rombel)->orderByRaw('tanggal DESC')->unionAll(\App\Penarikan::select('*')->fromSub(\App\Penarikan::select('penarikan.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'penarikan.guru_id')->join('users', 'users.id', 'penarikan.users_id')->join('rombel', 'rombel.guru_id', 'penarikan.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->where('nama_rombel', $id_rombel)->orderByRaw('tanggal DESC'))->get();
        $total_tarik = \App\Tarik::select('*')->fromSub(\App\Tarik::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'tarik.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'tarik.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->unionAll(\App\Penarikan::select('*')->fromSub(\App\Penarikan::select('penarikan.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'penarikan.guru_id')->join('users', 'users.id', 'penarikan.users_id')->join('rombel', 'rombel.guru_id', 'penarikan.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru'))->where('nama_rombel', $id_rombel)->sum('jumlah');

        return view('/laporankeuangan/setortariktunai/index', compact('daftar_nama', 'daftar_kelas', 'nama_nasabah', 'kelas_nasabah', 'data_setor', 'total_setor', 'data_tarik', 'total_tarik', 'data_id_pesdik', 'data_id_rombel', 'tgl_awal', 'tgl_akhir'));
    }

    public function tSetorTarikfilterByTanggal(Request $request)
    {
        $tgl_awal = $request->input('tgl_awal');
        $tgl_akhir = $request->input('tgl_akhir');

        //$data_id_pesdik = \App\Setor::select('pesdik_id')->groupBy('pesdik_id')->get();
        //$data_id_rombel = \App\Setor::select('rombel_id')->groupBy('rombel_id')->get();
        $data_id_pesdik = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->groupBy('pesdik_id')->orderByRaw('tanggal DESC')->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->groupBy('guru_id')->orderByRaw('tanggal DESC'))->orderByRaw('tanggal DESC')->get();
        $data_id_rombel = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->groupBy('nama_rombel')->orderByRaw('tanggal DESC')->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->groupBy('nama_rombel')->orderByRaw('tanggal DESC'))->orderByRaw('tanggal DESC')->get();

        $daftar_nama = \App\Setor::groupBy('pesdik_id')->orderByRaw('pesdik_id DESC')->get();
        $daftar_kelas = \App\Setor::groupBy('rombel_id')->orderByRaw('rombel_id DESC')->get();

        $nama_nasabah = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->groupBy('pesdik_id')->orderByRaw('pesdik_id DESC')->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->groupBy('guru_id')->orderByRaw('guru_id DESC'))->get();
        $kelas_nasabah = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->groupBy('nama_rombel')->orderByRaw('nama_rombel DESC')->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->groupBy('nama_rombel')->orderByRaw('nama_rombel DESC'))->get();

        $data_setor = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])->orderByRaw('tanggal DESC')->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])->orderByRaw('tanggal DESC'))->get();
        $total_setor = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru'))->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])->sum('jumlah');

        $data_tarik = \App\Tarik::select('*')->fromSub(\App\Tarik::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'tarik.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'tarik.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])->orderByRaw('tanggal DESC')->unionAll(\App\Penarikan::select('*')->fromSub(\App\Penarikan::select('penarikan.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'penarikan.guru_id')->join('users', 'users.id', 'penarikan.users_id')->join('rombel', 'rombel.guru_id', 'penarikan.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])->orderByRaw('tanggal DESC'))->get();
        $total_tarik = \App\Tarik::select('*')->fromSub(\App\Tarik::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'tarik.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'tarik.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->unionAll(\App\Penarikan::select('*')->fromSub(\App\Penarikan::select('penarikan.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'penarikan.guru_id')->join('users', 'users.id', 'penarikan.users_id')->join('rombel', 'rombel.guru_id', 'penarikan.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru'))->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])->sum('jumlah');

        return view('/laporankeuangan/setortariktunai/index', compact('daftar_nama', 'daftar_kelas', 'nama_nasabah', 'kelas_nasabah', 'data_setor', 'total_setor', 'data_tarik', 'total_tarik', 'data_id_pesdik', 'data_id_rombel', 'tgl_awal', 'tgl_akhir'));
    }

    public function tSetorTarikfilterByTanggalSekarang(Request $request)
    {
        $tgl_awal = Carbon::now()->day;
        $tgl_akhir = Carbon::now()->day;

        $data_id_pesdik = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->groupBy('pesdik_id')->orderByRaw('tanggal DESC')->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->groupBy('guru_id')->orderByRaw('tanggal DESC'))->orderByRaw('tanggal DESC')->get();
        $data_id_rombel = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->groupBy('nama_rombel')->orderByRaw('tanggal DESC')->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->groupBy('nama_rombel')->orderByRaw('tanggal DESC'))->orderByRaw('tanggal DESC')->get();

        $daftar_nama = \App\Setor::groupBy('pesdik_id')->orderByRaw('pesdik_id DESC')->get();
        $daftar_kelas = \App\Setor::groupBy('rombel_id')->orderByRaw('rombel_id DESC')->get();

        $nama_nasabah = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->groupBy('pesdik_id')->orderByRaw('pesdik_id DESC')->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->groupBy('guru_id')->orderByRaw('guru_id DESC'))->get();
        $kelas_nasabah = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->groupBy('nama_rombel')->orderByRaw('nama_rombel DESC')->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->groupBy('nama_rombel')->orderByRaw('nama_rombel DESC'))->get();

        $data_setor = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])->orderByRaw('tanggal DESC')->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])->orderByRaw('tanggal DESC'))->get();
        $total_setor = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru'))->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])->sum('jumlah');

        $data_tarik = \App\Tarik::select('*')->fromSub(\App\Tarik::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'tarik.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'tarik.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])->orderByRaw('tanggal DESC')->unionAll(\App\Penarikan::select('*')->fromSub(\App\Penarikan::select('penarikan.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'penarikan.guru_id')->join('users', 'users.id', 'penarikan.users_id')->join('rombel', 'rombel.guru_id', 'penarikan.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])->orderByRaw('tanggal DESC'))->get();
        $total_tarik = \App\Tarik::select('*')->fromSub(\App\Tarik::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'tarik.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'tarik.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->unionAll(\App\Penarikan::select('*')->fromSub(\App\Penarikan::select('penarikan.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'penarikan.guru_id')->join('users', 'users.id', 'penarikan.users_id')->join('rombel', 'rombel.guru_id', 'penarikan.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru'))->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])->sum('jumlah');

        return view('/laporankeuangan/setortariktunai/index', compact('daftar_nama', 'daftar_kelas', 'nama_nasabah', 'kelas_nasabah', 'data_setor', 'total_setor', 'data_tarik', 'total_tarik', 'data_id_pesdik', 'data_id_rombel', 'tgl_awal', 'tgl_akhir'));
    }

    public function tSetorTarikCetak(Request $request)
    {
        $inst = \App\Instansi::first();
        $data_id_pesdik = $request->id_pesdik;
        $data_id_rombel = $request->id_rombel;
        $tgl_awal = $request->input('tgl_awal');
        $tgl_akhir = $request->input('tgl_akhir');

        //$data_setor = \App\Setor::whereIn('pesdik_id', $data_id_pesdik)->whereIn('rombel_id', $data_id_rombel)->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])->get();
        //$total_setor = \App\Setor::whereIn('pesdik_id', $data_id_pesdik)->whereIn('rombel_id', $data_id_rombel)->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])->sum('jumlah');

        //$data_tarik = \App\Tarik::whereIn('pesdik_id', $data_id_pesdik)->whereIn('rombel_id', $data_id_rombel)->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])->get();
        //$total_tarik = \App\Tarik::whereIn('pesdik_id', $data_id_pesdik)->whereIn('rombel_id', $data_id_rombel)->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])->sum('jumlah');

        $data_setor = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->whereIn('nisn', $data_id_pesdik)->whereIn('nama_rombel', $data_id_rombel)->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->whereIn('no_hp', $data_id_pesdik)->whereIn('nama_rombel', $data_id_rombel)->whereBetween('tanggal', [$tgl_awal, $tgl_akhir]))->get();
        $total_setor = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->whereIn('nisn', $data_id_pesdik)->whereIn('nama_rombel', $data_id_rombel)->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->whereIn('no_hp', $data_id_pesdik)->whereIn('nama_rombel', $data_id_rombel)->whereBetween('tanggal', [$tgl_awal, $tgl_akhir]))->sum('jumlah');

        $data_tarik = \App\Tarik::select('*')->fromSub(\App\Tarik::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'tarik.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'tarik.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->whereIn('nisn', $data_id_pesdik)->whereIn('nama_rombel', $data_id_rombel)->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])->unionAll(\App\Penarikan::select('*')->fromSub(\App\Penarikan::select('penarikan.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'penarikan.guru_id')->join('users', 'users.id', 'penarikan.users_id')->join('rombel', 'rombel.guru_id', 'penarikan.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->whereIn('no_hp', $data_id_pesdik)->whereIn('nama_rombel', $data_id_rombel)->whereBetween('tanggal', [$tgl_awal, $tgl_akhir]))->get();
        $total_tarik = \App\Tarik::select('*')->fromSub(\App\Tarik::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'tarik.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'tarik.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->whereIn('nisn', $data_id_pesdik)->whereIn('nama_rombel', $data_id_rombel)->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])->unionAll(\App\Penarikan::select('*')->fromSub(\App\Penarikan::select('penarikan.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'penarikan.guru_id')->join('users', 'users.id', 'penarikan.users_id')->join('rombel', 'rombel.guru_id', 'penarikan.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->whereIn('no_hp', $data_id_pesdik)->whereIn('nama_rombel', $data_id_rombel)->whereBetween('tanggal', [$tgl_awal, $tgl_akhir]))->sum('jumlah');

        $cetakPDF = $request->cetakpdf;
        $cetakEXCEL = $request->cetakexcel;

        if($request->submit == "cetakpdf")
        {
            return view('/laporankeuangan/setortariktunai/cetak', compact('inst', 'tgl_awal', 'tgl_akhir', 'data_setor', 'total_setor', 'data_tarik', 'total_tarik'));
        }
        elseif($request->submit == "cetakexcel")
        {
            $namafile = 'Laporan_Setor_dan_Tarik_Tunai_' . date('Y-m-d_H-i-s') . '.xlsx';
            return Excel::download(new SetorTarikExportExcelHB($data_setor, $total_setor, $data_tarik, $total_tarik), $namafile);
        }
    }

    public function tSetorTarikDownloadExcel()
    {
        $namafile = 'Laporan_setor_tarik_tunai_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new SetorTarikExport, $namafile);
    }

    public function tSetorTarikDownloadExcelHB(Request $request)
    {
        $data_id_pesdik = $request->id_pesdik;
        $data_id_rombel = $request->id_rombel;
        $tgl_awal = $request->input('tgl_awal');
        $tgl_akhir = $request->input('tgl_akhir');

        //$data_setor = \App\Setor::whereIn('pesdik_id', $data_id_pesdik)->whereIn('rombel_id', $data_id_rombel)->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])->get();
        //$total_setor = \App\Setor::whereIn('pesdik_id', $data_id_pesdik)->whereIn('rombel_id', $data_id_rombel)->whereBetween('created_at', [$tgl_awal, $tgl_akhir])->sum('jumlah');

        //$data_tarik = \App\Tarik::whereIn('pesdik_id', $data_id_pesdik)->whereIn('rombel_id', $data_id_rombel)->whereBetween('created_at', [$tgl_awal, $tgl_akhir])->get();
        //$total_tarik = \App\Tarik::whereIn('pesdik_id', $data_id_pesdik)->whereIn('rombel_id', $data_id_rombel)->whereBetween('created_at', [$tgl_awal, $tgl_akhir])->sum('jumlah');

        $data_setor = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->whereIn('nisn', $data_id_pesdik)->whereIn('nama_rombel', $data_id_rombel)->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->whereIn('no_hp', $data_id_pesdik)->whereIn('nama_rombel', $data_id_rombel)->whereBetween('tanggal', [$tgl_awal, $tgl_akhir]))->get();
        $total_setor = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->whereIn('nisn', $data_id_pesdik)->whereIn('nama_rombel', $data_id_rombel)->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->whereIn('no_hp', $data_id_pesdik)->whereIn('nama_rombel', $data_id_rombel)->whereBetween('tanggal', [$tgl_awal, $tgl_akhir]))->sum('jumlah');

        $data_tarik = \App\Tarik::select('*')->fromSub(\App\Tarik::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'tarik.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'tarik.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->whereIn('nisn', $data_id_pesdik)->whereIn('nama_rombel', $data_id_rombel)->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])->unionAll(\App\Penarikan::select('*')->fromSub(\App\Penarikan::select('penarikan.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'penarikan.guru_id')->join('users', 'users.id', 'penarikan.users_id')->join('rombel', 'rombel.guru_id', 'penarikan.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->whereIn('no_hp', $data_id_pesdik)->whereIn('nama_rombel', $data_id_rombel)->whereBetween('tanggal', [$tgl_awal, $tgl_akhir]))->get();
        $total_tarik = \App\Tarik::select('*')->fromSub(\App\Tarik::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'tarik.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'tarik.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->whereIn('nisn', $data_id_pesdik)->whereIn('nama_rombel', $data_id_rombel)->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])->unionAll(\App\Penarikan::select('*')->fromSub(\App\Penarikan::select('penarikan.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'penarikan.guru_id')->join('users', 'users.id', 'penarikan.users_id')->join('rombel', 'rombel.guru_id', 'penarikan.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->whereIn('no_hp', $data_id_pesdik)->whereIn('nama_rombel', $data_id_rombel)->whereBetween('tanggal', [$tgl_awal, $tgl_akhir]))->sum('jumlah');

        $namafile = 'Laporan_setor_tarik_tunai_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new SetorTarikExportExcelHB($data_setor, $total_setor, $data_tarik, $total_tarik), $namafile);
    }

    //Laporan Keuangan Sekolah
    public function tKeuanganSekolahIndex()
    {
        $daftar_kategori = \App\Kategori::orderByRaw('nama_kategori ASC')->get();

        $data_id_kategori = \App\Kategori::all();
        $tgl_1 = \App\Pemasukan::first();
        $tgl_awal = $tgl_1->created_at;
        $tgl_akhir = Carbon::now();


        $data_pemasukan = \App\Pemasukan::orderByRaw('created_at DESC')->get();
        $total_pemasukan = \App\Pemasukan::all()->sum('jumlah');

        $data_pengeluaran = \App\Pengeluaran::orderByRaw('created_at DESC')->get();
        $total_pengeluaran = \App\Pengeluaran::all()->sum('jumlah');

        return view('/laporankeuangan/keuangansekolah/index', compact('daftar_kategori', 'data_id_kategori', 'tgl_awal', 'tgl_akhir', 'data_pemasukan', 'total_pemasukan', 'data_pengeluaran', 'total_pengeluaran'));
    }

    public function tKeuanganSekolahfilterByKategori(Request $request)
    {
        $kategori_id = $request->input('filterKategori');

        $data_id_kategori = \App\Kategori::select('id')->where('id', $kategori_id)->get();
        $tgl_1 = \App\Pemasukan::first();
        $tgl_awal = $tgl_1->created_at;
        $tgl_akhir = Carbon::now();


        $daftar_kategori = \App\Kategori::orderByRaw('nama_kategori ASC')->get();

        $data_pemasukan = \App\Pemasukan::where('kategori_id', $kategori_id)->orderByRaw('created_at DESC')->get();
        $total_pemasukan = \App\Pemasukan::where('kategori_id', $kategori_id)->sum('jumlah');

        $data_pengeluaran = \App\Pengeluaran::where('kategori_id', $kategori_id)->orderByRaw('created_at DESC')->get();
        $total_pengeluaran = \App\Pengeluaran::where('kategori_id', $kategori_id)->sum('jumlah');

        return view('/laporankeuangan/keuangansekolah/index', compact('daftar_kategori', 'data_id_kategori', 'tgl_awal', 'tgl_akhir', 'data_pemasukan', 'total_pemasukan', 'data_pengeluaran', 'total_pengeluaran'));
    }

    public function tKeuanganSekolahfilterByTanggal(Request $request)
    {
        $tgl_awal = $request->input('tgl_awal');
        $tgl_akhir = $request->input('tgl_akhir');
        $data_id_kategori = \App\Kategori::all();

        $daftar_kategori = \App\Kategori::orderByRaw('nama_kategori ASC')->get();

        $data_pemasukan = \App\Pemasukan::whereBetween('created_at', [$tgl_awal, $tgl_akhir])->orderByRaw('created_at DESC')->get();
        $total_pemasukan = \App\Pemasukan::whereBetween('created_at', [$tgl_awal, $tgl_akhir])->sum('jumlah');

        $data_pengeluaran = \App\Pengeluaran::whereBetween('created_at', [$tgl_awal, $tgl_akhir])->orderByRaw('created_at DESC')->get();
        $total_pengeluaran = \App\Pengeluaran::whereBetween('created_at', [$tgl_awal, $tgl_akhir])->sum('jumlah');

        return view('/laporankeuangan/keuangansekolah/index', compact('daftar_kategori', 'data_id_kategori', 'tgl_awal', 'tgl_akhir', 'data_pemasukan', 'total_pemasukan', 'data_pengeluaran', 'total_pengeluaran'));
    }

    public function tKeuanganSekolahCetak(Request $request)
    {
        $inst = \App\Instansi::first();
        $data_id_kategori = $request->id_kategori;
        $tgl_awal = $request->input('tgl_awal');
        $tgl_akhir = $request->input('tgl_akhir');

        $data_pemasukan = \App\Pemasukan::whereIn('kategori_id', $data_id_kategori)->whereBetween('created_at', [$tgl_awal, $tgl_akhir])->get();
        $total_pemasukan = \App\Pemasukan::whereIn('kategori_id', $data_id_kategori)->whereBetween('created_at', [$tgl_awal, $tgl_akhir])->sum('jumlah');

        $data_pengeluaran = \App\Pengeluaran::whereIn('kategori_id', $data_id_kategori)->whereBetween('created_at', [$tgl_awal, $tgl_akhir])->get();
        $total_pengeluaran = \App\Pengeluaran::whereIn('kategori_id', $data_id_kategori)->whereBetween('created_at', [$tgl_awal, $tgl_akhir])->sum('jumlah');

        return view('/laporankeuangan/keuangansekolah/cetak', compact('inst', 'tgl_awal', 'tgl_akhir', 'data_pemasukan', 'total_pemasukan', 'data_pengeluaran', 'total_pengeluaran'));
    }

    public function tKeuanganSekolahDownloadExcel()
    {
        $namafile = 'Laporan_keuangan_sekolah_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new KeuanganSekolahExport, $namafile);
    }


    public function importExportView()
    {
        return view('import');
    }

    public function export()
    {
        return Excel::download(new UsersExport, 'dataGuru.csv');
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);

        $file = $request->file('file');

        $nama_file = $file->getClientOriginalName();

        $file->move('dataguru',$nama_file);

        Excel::import(new UsersImport, public_path('/dataguru/'.$nama_file));

        return redirect('/guru/index')->with('sukses', 'Data Guru Berhasil Ditambahkan');
    }

    //Import Export data Peserta Didik (Pesdik)
    public function importexportViewPesdik() 
    {
        return view('importPesdik');
    }

    public function exportPesdik()
    {
        return Excel::download(new PesdikExport, 'dataPesdik.csv');
    }

    public function importPesdik(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);

        $file = $request->file('file');

        $nama_file = $file->getClientOriginalName();

        $file->move('dataPesdik',$nama_file);

        Excel::import(new PesdikImport, public_path('/dataPesdik/'.$nama_file));

        return redirect('/pesdik/index')->with('sukses', 'Data Peserta Didik Berhasil Ditambahkan');
    }
}
