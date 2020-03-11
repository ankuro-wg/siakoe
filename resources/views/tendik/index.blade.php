@extends('layouts.master')
@section('content')
<section class="content card" style="padding: 10px 10px 20px 20px  ">
    <div class="box">
        @if(session('sukses'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{session('sukses')}}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
        @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
        <div class="row">
            <div class="col">
                <h3><i class="nav-icon fas fa-graduation-cap my-0 btn-sm-1"></i> Data Tenaga Kependidikan</h3>
                <hr>
            </div>
        </div>
        <div>
                <div class="col">
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal"
                        data-target="#tambahTendik"><i class="fas fa-plus"></i>
                        Tambah Data
                    </button>
                </div>
                <br>
        </div>
        <div class="row">
            <div class="row table-responsive">
                <div class="col-12">
                    <table class="table table-head-fixed" id='tabelAgendaMasuk'>
                        <thead>
                            <tr class="bg-light">
                                <th>No.</th>
                                <th>Nama Lengkap</th>
                                <th>Jenis Kelamin</th>
                                <th>Tempat Lahir</th>
                                <th>Tanggal Lahir</th>
                                <th>Alamat</th>
                                <th>Nomor HP</th>
                                <th>Email</th>
                                <th>Tugas Sebagai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 0;?>
                            @foreach($data_tendik as $tendik)
                            <?php $no++ ;?>
                            <tr>
                                <td>{{$no}}</td>
                                <td>{{$tendik->nama}}</td>
                                <td>{{$tendik->jenis_kelamin}}</td>
                                <td>{{$tendik->tempat_lahir}}</td>
                                <td>{{$tendik->tanggal_lahir}}</td>
                                <td>{{$tendik->alamat}}</td>
                                <td>{{$tendik->no_hp}}</td>
                                <td>{{$tendik->email}}</td>
                                <td>{{$tendik->tugas}}</td>
                                <td>
                                <a href="/tendik/{{$tendik->id}}/edit"
                                    class="btn btn-primary btn-sm my-1 mr-sm-1"><i
                                        class="nav-icon fas fa-pencil-alt"></i> Edit</a>
                                @if (auth()->user()->role == 'admin')
                                <a href="/tendik/{{$tendik->id}}/delete"
                                    class="btn btn-danger btn-sm my-1 mr-sm-1"
                                    onclick="return confirm('Hapus Data ?')"><i class="nav-icon fas fa-trash"></i>
                                    Hapus</a>
                                @endif
                            </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Modal Tambah -->
        <div class="modal fade" id="tambahTendik" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><i
                                class="nav-icon fas fa-graduation-cap my-1 btn-sm-1"></i> Tambah Data Tenaga Kependidikan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="/tendik/tambah" method="POST">
                            {{csrf_field()}}
                            <div class="row">
                                    <label for="nama">Nama Lengkap</label>
                                    <input value="{{old('nama')}}" name="nama" type="text" class="form-control bg-light"
                                        id="nama" placeholder="Nama Lengkap" required>
                                    <label for="kode">Jenis Kelamin</label>
                                    <select name="jk" class="custom-select my-1 mr-sm-2 bg-light" id="jk"required>
                                        <option value="">-- Pilih Jenis Kelamin --</option>
                                        <option value="Laki-Laki">Laki-laki</option>
                                        <option value="Perempuan">Perempuan</option>
                                    </select>
                                    <label for="tempatlahir">Tempat Lahir</label>
                                    <input value="{{old('tempatlahir')}}" name="tempatlahir" type="text" class="form-control bg-light"
                                        id="tempatlahir" placeholder="Tempat Lahir" required>
                                    <label for="tgllahir">Tanggal Lahir</label>
                                    <input value="{{old('tgllahir')}}" name="tgllahir" type="date" class="form-control bg-light"
                                        id="tgllahir" required>
                                    <label for="alamat">Alamat Lengkap</label>
                                    <textarea name="alamat" class="form-control bg-light" id="alamat" rows="2"
                                        placeholder="Alamat Lengkap" required>{{old('alamat')}}</textarea>
                                    <label for="no_hp">Nomor HP</label>
                                    <input value="{{old('no_hp')}}" name="no_hp" type="text" class="form-control bg-light"
                                        id="no_hp" placeholder="Nomor HP" required>
                                    <label for="email">Email</label>
                                    <input value="{{old('email')}}"name="email" type="email" class="form-control bg-light" id="email" placeholder="Email"
                                        required>
                                        <label for="kode">Bertugas Sebagai</label>
                                    <select name="tugas" class="custom-select my-1 mr-sm-2 bg-light" id="tugas"required>
                                        <option value="">-- Pilih Jenis Tugas--</option>
                                        <option value="Pengelola Administrasi Surat">Pengelola Administrasi Surat</option>
                                        <option value="Pengelola Administrasi Keuangan">Pengelola Administrasi Keuangan</option>
                                        <option value="Pengelola Data Sekolah">Pengelola Data Sekolah</option>
                                    </select>
                            </div>
                            <hr>
                            <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-save"></i>
                                SIMPAN</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</section>
@endsection
