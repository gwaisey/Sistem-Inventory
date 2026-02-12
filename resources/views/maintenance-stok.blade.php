@extends('layouts.app')

@section('content')
<div class="card shadow-sm" style="max-width: 650px; margin: auto; border: 2px solid #333; border-radius: 12px;">
    <div class="card-header text-center fw-bold bg-white" style="border-bottom: 2px solid #333; border-radius: 12px 12px 0 0; padding: 15px;">
        <span class="text-muted">———————</span> Maintenance Stok <span class="text-muted">———————</span>
    </div>
    
    <div class="card-body p-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form id="stokForm" action="{{ route('stok.store') }}" method="POST">
            @csrf 
            
            <div class="row mb-3 align-items-center">
                <label class="col-sm-4 fw-bold">Jenis Transaksi :</label>
                <div class="col-sm-8">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jenis_transaksi" id="masuk" value="MASUK" checked>
                        <label class="form-check-label" for="masuk">Masuk</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jenis_transaksi" id="keluar" value="KELUAR">
                        <label class="form-check-label" for="keluar">Keluar</label>
                    </div>
                </div>
            </div>

            <div class="row mb-3 align-items-center">
                <label class="col-sm-4 fw-bold">Bukti :</label>
                <div class="col-sm-8">
                    <input type="text" name="bukti" class="form-control" placeholder="Masukkan Nomor Bukti" required>
                </div>
            </div>

            <div class="row mb-3 align-items-center">
                <label class="col-sm-4 fw-bold">Kode Lokasi :</label>
                <div class="col-sm-8">
                    <input type="text" name="kode_lokasi" class="form-control" placeholder="GUDANG-A / RAK-01" required>
                    <div class="form-text text-muted" style="font-size: 0.75rem;">Lokasi baru akan otomatis terdaftar di sistem.</div>
                </div>
            </div>

            <div class="row mb-3 align-items-center">
                <label class="col-sm-4 fw-bold">Kode Barang :</label>
                <div class="col-sm-8">
                    <input type="text" id="kode_barang" name="kode_barang" class="form-control" placeholder="Ketik Kode Barang" required>
                </div>
            </div>

            <div class="row mb-3 align-items-center">
                <label class="col-sm-4 fw-bold">Nama Barang :</label>
                <div class="col-sm-8">
                    <input type="text" id="nama_barang" name="nama_barang" class="form-control" placeholder="Nama barang otomatis/manual">
                </div>
            </div>

            <div class="row mb-3 align-items-center">
                <label class="col-sm-4 fw-bold">Tanggal Transaksi :</label>
                <div class="col-sm-8">
                    <input type="date" name="tgl_transaksi" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <div class="row mb-4 align-items-center">
                <label class="col-sm-4 fw-bold">Quantity :</label>
                <div class="col-sm-8">
                    <input type="number" name="quantity" class="form-control" placeholder="0" min="1" required>
                </div>
            </div>

            <hr style="border: 1px solid #333; opacity: 1;">

            <div class="d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-dark px-4 shadow-sm">Posting</button>
                <button type="button" class="btn btn-outline-secondary px-4" onclick="window.location.href='/';">Exit</button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#kode_barang').on('change', function() {
        let kode = $(this).val();
        if (kode) {
            $.ajax({
                url: '/get-nama-barang/' + kode,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    
                    if(data.nama) {
                        $('#nama_barang').val(data.nama);
                    }
                },
                error: function() {
                    
                    console.log('Barang baru terdeteksi, silakan isi nama manual.');
                }
            });
        }
    });
});
</script>
@endsection