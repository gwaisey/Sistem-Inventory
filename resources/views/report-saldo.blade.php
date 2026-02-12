@extends('layouts.app')

@section('content')
    <div class="card shadow-sm mt-5">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">Laporan Saldo Barang</h4>
        </div>
        <div class="card-body">
            <form action="/report-saldo" method="GET" class="mb-5">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label class="small font-weight-bold">Lokasi:</label>
                        <input type="text" name="lokasi" class="form-control form-control-sm" placeholder="Contoh: GBJ01" value="{{ request('lokasi') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="small font-weight-bold">Kode Barang:</label>
                        <input type="text" name="kode_barang" class="form-control form-control-sm" placeholder="Kode Barang" value="{{ request('kode_barang') }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary btn-sm px-4">Cari</button>
                        <a href="/report-saldo" class="btn btn-outline-secondary btn-sm px-4 ml-2">Atur Ulang</a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>ID Stok</th>
                            <th>Kode Lokasi</th> <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Saldo</th>
                            <th>Tgl. Masuk</th> </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $row)
                        <tr>
                            <td>{{ $row->ID_Stok }}</td>
                            <td>{{ $row->Kode_Lokasi }}</td>
                            <td>{{ $row->Kode_Barang }}</td>
                            <td>{{ $row->Nama_Barang }}</td>
                            <td class="fw-bold text-primary">{{ $row->Saldo }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->Tgl_Masuk)->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                <a href="/maintenance-stok" class="btn btn-secondary shadow-sm px-4" style="border-radius: 8px; font-size: 14px;">
                    Kembali ke Input
                </a>
            </div>
        </div>
    </div>

    

    <style>
        .table td, .table th { padding: 12px !important; vertical-align: middle; }
    </style>
@endsection