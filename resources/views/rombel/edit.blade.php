@extends('layouts.master')

@section('content')
<section class="content card" style="padding: 10px 10px 10px 10px ">
    <div class="box">
        @if(session('sukses'))
        <div class="callout callout-success alert alert-success alert-dismissible fade show" role="alert">
            <h5><i class="fas fa-check"></i> Sukses :</h5>
            {{session('sukses')}}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        @if(session('warning'))
        <div class="callout callout-warning alert alert-warning alert-dismissible fade show" role="alert">
            <h5><i class="fas fa-info"></i> Informasi :</h5>
            {{session('warning')}}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        @if ($errors->any())
        <div class="callout callout-danger alert alert-danger alert-dismissible fade show">
            <h5><i class="fas fa-exclamation-triangle"></i> Peringatan :</h5>
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
        <form action="/rombel/{{$rombel->id}}/update" method="POST">
            <h4><i class="nav-icon fas fa-users my-1 btn-sm-1"></i> Edit Data Rombongan Belajar</h4>
            <hr>
            {{csrf_field()}}
            <div class="row">
                <div class="col-md-6">
                    <label for="kelas">Tingkat Kelas</label>
                    <select name="kelas" id="kelas" class="form-control bg-light" required oninvalid="this.setCustomValidity('Isian ini tidak boleh kosong !')" oninput="setCustomValidity('')">
                        <option value="TPA" @if ($rombel->kelas == 'TPA') selected @endif>TPA</option>
                        <option value="KB" @if ($rombel->kelas == 'KB') selected @endif>KB</option>
                        <option value="TK A" @if ($rombel->kelas == 'TK A') selected @endif>TK A</option>
                        <option value="TK B" @if ($rombel->kelas == 'TK B') selected @endif>TK B</option>
                        <option value="SD Kelas 1" @if ($rombel->kelas == 'SD Kelas 1') selected @endif>SD Kelas 1</option>
                        <option value="SD Kelas 2" @if ($rombel->kelas == 'SD Kelas 2') selected @endif>SD Kelas 2</option>
                        <option value="SD Kelas 3" @if ($rombel->kelas == 'SD Kelas 3') selected @endif>SD Kelas 3</option>
                        <option value="SD Kelas 4" @if ($rombel->kelas == 'SD Kelas 4') selected @endif>SD Kelas 4</option>
                        <option value="SD Kelas 5" @if ($rombel->kelas == 'SD Kelas 5') selected @endif>SD Kelas 5</option>
                        <option value="SD Kelas 6" @if ($rombel->kelas == 'SD Kelas 6') selected @endif>SD Kelas 6</option>
                    </select>
                    <label for="nama_rombel">Nama Rombel</label>
                    <input name="nama_rombel" type="text" class="form-control bg-light" id="nama_rombel" placeholder="Nama Rombel" value="{{$rombel->nama_rombel}}" required oninvalid="this.setCustomValidity('Isian ini tidak boleh kosong !')" oninput="setCustomValidity('')">
                </div>
                <div class="col-md-6">
                    <label for="guru_id">Wali Kelas</label>
                    <select name="guru_id" class="form-control bg-light" id="guru_id" required oninvalid="this.setCustomValidity('Isian ini tidak boleh kosong !')" oninput="setCustomValidity('')">
                        <option value="{{$rombel->guru_id}}">{{$rombel->guru->nama}}</option>
                        @foreach($data_guru as $guru)
                        <option value="{{$guru->id}}">{{$guru->nama}}</option>
                        @endforeach
                    </select>
                    <label for="tapel_id">Tahun Pelajaran</label>
                    <select name="tapel_id" class="form-control bg-disabled" id="tapel_id" disabled>
                        <option value="{{$rombel->tapel_id}}">{{$rombel->tapel->tahun}} ({{$rombel->tapel->semester}})</option>
                    </select>
                </div>
            </div>
            <hr>
            <button type="submit" class="btn btn-success btn-sm "><i class="fas fa-save"></i> SIMPAN</button>
            <a class="btn btn-danger btn-sm" href="/rombel/index" role="button"><i class="fas fa-undo"></i>
                BATAL</a>
        </form>
    </div>
</section>
@endsection