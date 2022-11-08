@extends('layouts.master')
@section('content')
@if(session('warning'))
<div class="callout callout-warning alert alert-warning alert-dismissible fade show" role="alert">
  <h5><i class="fas fa-info"></i> Peringatan :</h5>
  {{session('warning')}}
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif
<div class="row">
  <div class="container-fluid">
    <!-- Info boxes -->
    @if (auth()->user()->role == 'admin' || auth()->user()->role == 'PetugasAdministrasiSurat')
    <div class="row">
      <div class="flex-fill col-md-3" style="padding: 4px 4px 4px 4px">
        <div class="info-box md-3">
          <span class="info-box-icon bg-info elevation-1"><i class="fas fa-donate"></i></span>

          <div class="info-box-content">
            <span class="info-box-text"><b class="text-info">Setoran</b> Bulan Ini</span>
            <span class="info-box-number">
              <?php
                $bln_ini = \Carbon\Carbon::now()->month;
                $total_setor = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->whereMonth('tanggal', $bln_ini)->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->whereMonth('tanggal', $bln_ini))->sum('jumlah');
              ?>
              @currency($total_setor),00
            </span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
      </div>
      <!-- /.col -->
      <div class=" flex-fill col-md-3" style="padding: 4px 4px 4px 4px">
        <div class="info-box md-3">
          <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-exchange-alt"></i></span>

          <div class="info-box-content">
            <span class="info-box-text"><b class="text-danger">Penarikan</b> Bulan Ini</span>
            <span class="info-box-number">
            <?php
                $bln_ini = \Carbon\Carbon::now()->month;
                $total_tarik = \App\Tarik::select('*')->fromSub(\App\Tarik::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'tarik.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'tarik.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->whereMonth('tanggal', $bln_ini)->unionAll(\App\Penarikan::select('*')->fromSub(\App\Penarikan::select('penarikan.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'penarikan.guru_id')->join('users', 'users.id', 'penarikan.users_id')->join('rombel', 'rombel.guru_id', 'penarikan.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->whereMonth('tanggal', $bln_ini))->sum('jumlah');
              ?>
              @currency($total_tarik),00
            </span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
      </div>
      <!-- /.col -->

      <!-- fix for small devices only -->
      <div class="flex-fill col-md-3" style="padding: 4px 4px 4px 4px">
        <div class="info-box md-3">
          <span class="info-box-icon bg-success elevation-1"><i class="fas fa-wallet"></i></span>

          <div class="info-box-content">
            <span class="info-box-text"><b class="text-success">Saldo</b> Bulan Ini</span>
            <span class="info-box-number">
              @currency($total_setor-$total_tarik),00
            </span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
      </div>
      <!-- /.col -->
      <div class=" flex-fill" style="padding: 4px 4px 4px 4px">
        <div class="info-box md-3">
          <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-coins"></i></span>
          <div class="info-box-content">
            <span class="info-box-text"><b class="text-warning">Transaksi</b> Bulan Ini</span>
            <span class="info-box-number">
              <?php
                $bln_ini = \Carbon\Carbon::now()->month;
                $total_setor = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->whereMonth('tanggal', $bln_ini)->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->whereMonth('tanggal', $bln_ini))->count('jumlah');
                $total_tarik = \App\Tarik::select('*')->fromSub(\App\Tarik::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'tarik.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'tarik.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->whereMonth('tanggal', $bln_ini)->unionAll(\App\Penarikan::select('*')->fromSub(\App\Penarikan::select('penarikan.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'penarikan.guru_id')->join('users', 'users.id', 'penarikan.users_id')->join('rombel', 'rombel.guru_id', 'penarikan.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->whereMonth('tanggal', $bln_ini))->count('jumlah');
              ?>
              {{$total_setor+$total_tarik}}
            </span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
      </div>
      <!-- /.col -->
    </div>
    @endif
    <!-- /.row -->
  </div>
  @if (auth()->user()->role == 'admin' || auth()->user()->role == 'PetugasAdministrasiKeuangan')
  <div class="col-md-9">
    <section class="content card" style="padding: 15px 15px 40px 15px ">
      <div class="box">
        <div class="row">
          <div class="col">
            <h4><i class="nav-icon fas fa-warehouse my-0 btn-sm-1"></i> Rekap Data Transaksi <b>Hari Ini</b> </h4>
            <hr>
          </div>
        </div>
        <div class="card-body">
          <!-- Small boxes (Stat box) -->
          <div class="filter-container p-0 row d-flex justify-content-center">
            <div class="col-lg-6 col-md-6">
              <!-- small box -->
              <div class="small-box bg-info">
                <div class="inner">
                  <?php
                    $hr_ini = \Carbon\Carbon::now()->day;
                    $total_setor_hr = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->whereDay('tanggal', $hr_ini)->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->whereDay('tanggal', $hr_ini))->sum('jumlah');
                  ?>
                  <h3>@currency($total_setor_hr),00</h3>
                  <p><b>Setoran</b> Hari Ini</p>
                </div>
                <div class="icon">
                  <i class="nav-icon fas fa-donate"></i>
                </div>
                <form id="form" action="/laporankeuangan/setortariktunai/filterByTanggalSekarang" method="POST">@csrf</form>
                <a href="javascript:void(0)" onclick="document.getElementById('form').submit()" class="small-box-footer">Info Lebih Lanjut <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <div class="col-lg-6 col-md-6">
              <!-- small box -->
              <div class="small-box bg-danger">
                <div class="inner">
                  <?php
                    $hr_ini = \Carbon\Carbon::now()->day;
                    $total_tarik_hr = \App\Tarik::select('*')->fromSub(\App\Tarik::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'tarik.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'tarik.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->whereMonth('tanggal', $hr_ini)->unionAll(\App\Penarikan::select('*')->fromSub(\App\Penarikan::select('penarikan.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'penarikan.guru_id')->join('users', 'users.id', 'penarikan.users_id')->join('rombel', 'rombel.guru_id', 'penarikan.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->whereMonth('tanggal', $hr_ini))->sum('jumlah');
                  ?>
                  <h3>@currency($total_tarik_hr),00</h3>
                  <p><b>Penarikan</b> Hari ini</p>
                </div>
                <div class="icon">
                  <i class="nav-icon fas fa-exchange-alt"></i>
                </div>
                <form id="form" action="/laporankeuangan/setortariktunai/filterByTanggalSekarang" method="POST">@csrf</form>
                <a href="javascript:void(0)" onclick="document.getElementById('form').submit()" class="small-box-footer">Info Lebih Lanjut <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-6 col-md-6">
              <!-- small box -->
              <div class="small-box bg-success">
                <div class="inner">
                  <!-- <h3>{{DB::table('pesdik')->where('status',"Aktif")->count()}}</h3> -->
                  <h3>@currency($total_setor_hr-$total_tarik_hr),00</h3>
                  <p><b>Saldo</b> Hari Ini</p>
                </div>
                <div class="icon">
                  <i class="nav-icon fas fa-wallet nav-icon"></i>
                </div>
                <form id="form" action="/laporankeuangan/setortariktunai/filterByTanggalSekarang" method="POST">@csrf</form>
                <a href="javascript:void(0)" onclick="document.getElementById('form').submit()" class="small-box-footer">Info Lebih Lanjut <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <div class="col-lg-6 col-md-6">
              <!-- small box -->
              <div class="small-box bg-warning">
                <div class="inner">
                  <?php
                    $hr_ini = \Carbon\Carbon::now()->day;
                    $total_trans_setor = \App\Setor::select('*')->fromSub(\App\Setor::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'setor.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'setor.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->whereMonth('tanggal', $hr_ini)->unionAll(\App\Setoran::select('*')->fromSub(\App\Setoran::select('setoran.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'setoran.guru_id')->join('users', 'users.id', 'setoran.users_id')->join('rombel', 'rombel.guru_id', 'setoran.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->whereMonth('tanggal', $hr_ini))->count('jumlah');
                    $total_trans_tarik = \App\Tarik::select('*')->fromSub(\App\Tarik::select('pesdik_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'nisn', 'name', 'tahun', 'semester')->join('pesdik', 'pesdik.id', 'tarik.pesdik_id')->join('rombel', 'rombel.id', 'pesdik.rombel_id')->join('users', 'users.id', 'tarik.users_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelpesdik')->whereMonth('tanggal', $hr_ini)->unionAll(\App\Penarikan::select('*')->fromSub(\App\Penarikan::select('penarikan.guru_id', 'users_id', 'nama_rombel', 'tanggal', 'jumlah', 'keterangan', 'nama', 'no_hp', 'name', 'tahun', 'semester')->join('guru', 'guru.id', 'penarikan.guru_id')->join('users', 'users.id', 'penarikan.users_id')->join('rombel', 'rombel.guru_id', 'penarikan.guru_id')->join('tapel', 'tapel.id', 'rombel.tapel_id'), 'tabelguru')->whereMonth('tanggal', $hr_ini))->count('jumlah');
                  ?>
                  <!-- <h3>{{DB::table('rombel')->where('tapel_id', DB::table('rombel')->MAX('tapel_id'))->count()}}</h3> -->
                  <h3>{{ $total_trans_setor+$total_trans_tarik }}</h3>
                  <p><b>Transaksi</b> Hari Ini</p>
                </div>
                <div class="icon">
                  <i class="nav-icon fas fa-coins"></i>
                </div>
                <form id="form" action="/laporankeuangan/setortariktunai/filterByTanggalSekarang" method="POST">@csrf</form>
                <a href="javascript:void(0)" onclick="document.getElementById('form').submit()" class="small-box-footer">Info Lebih Lanjut <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  <div class="col-md-3">
    <section class="content card" style="padding: 15px 15px 0px 15px ">
      <div class="box">
        <div class="row">
          <div class="col">
            <h4><i class="nav-icon fas fa-dollar-sign my-0 btn-sm-1"></i> Tabungan Sekolah</h4>
            <hr>
          </div>
        </div>
        <div class="card-body p-0">
          <?php
          $jumlah_pengeluaran = DB::table('tarik')
            ->sum('tarik.jumlah');
          $jumlah_pemasukan = DB::table('setor')
            ->sum('setor.jumlah');
          $jumlah_transaksi_setoran = DB::table('tarik')
            ->count('tarik.jumlah');
          $jumlah_transaksi_penarikan = DB::table('setor')
            ->count('setor.jumlah');
          ?>
          <ul class="products-list product-list-in-card pl-1 pr-1">
            <a href="javascript:void(0)" class="product-title">Jumlah Setoran</a>
            <h5>@currency($jumlah_pemasukan),00</h5>
            <hr/>
          </ul>
          <ul class="products-list product-list-in-card pl-1 pr-1">
            <a href="javascript:void(0)" class="product-title">Jumlah Penarikan</a>
            <h5> @currency($jumlah_pengeluaran),00</h5>
            <hr/>
          </ul>
          <ul class="products-list product-list-in-card pl-1 pr-1">
            <a href="javascript:void(0)" class="product-title">Jumlah Transaksi</a>
            <h5>{{ $jumlah_transaksi_setoran+$jumlah_transaksi_penarikan }}</h5>
            <hr/>
          </ul>
          <ul class="products-list product-list-in-card pl-1 pr-1">
            <a href="javascript:void(0)" class="product-title">Saldo</a>
            <h5>@currency($jumlah_pemasukan-$jumlah_pengeluaran),00</h5>
            <hr/>
          </ul>
        </div>
      </div>
    </section>
  </div>
  @endif
  @endsection