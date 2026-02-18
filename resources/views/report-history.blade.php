@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0" style="border-radius: 10px; overflow: hidden;">
        <div class="card-header bg-danger text-white p-3">
            <h4 class="mb-0">Laporan Riwayat Transaksi</h4>
        </div>
        <div class="card-body bg-light">
            <div class="row g-3 align-items-end mb-4">
                <div class="col-md-3">
                    <label class="small fw-bold">Nomor Bukti:</label>
                    <input type="text" id="filter-bukti" class="form-control form-control-sm" placeholder="Cari Bukti...">
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold">Tanggal:</label>
                    <input type="date" id="filter-tgl" class="form-control form-control-sm">
                </div>
                <div class="col-md-4">
                    <button id="btn-cari" class="btn btn-primary btn-sm px-4">Cari</button>
                    <button id="btn-reset" class="btn btn-outline-secondary btn-sm px-4">Atur Ulang</button>
                </div>
            </div>

            

            <div class="table-responsive bg-white rounded shadow-sm">
                <table class="table table-hover table-bordered mb-0" style="min-width: 1000px;">
                    <thead class="table-light text-center small fw-bold">
                        <tr>
                            <th width="50">No</th>
                            <th>ID Stok</th>
                            <th>Bukti</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Lokasi</th>
                            <th>Kode Barang</th>
                            <th>Jumlah</th>
                            <th>Program</th>
                        </tr>
                    </thead>
                    <tbody id="table-body" class="small text-center">
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
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<script>
$(document).ready(function() {
 
    loadData();

   
    function loadData(bukti = '', tgl = '') {
        $('#table-body').html('<tr><td colspan="9">Sedang memuat data...</td></tr>');
        
        $.ajax({
            url: "/api/report-history",
            type: "GET",
            data: { bukti: bukti, tgl: tgl },
            success: function(response) {
                let rows = '';
                if (response.length > 0) {
                    $.each(response, function(i, item) {
                  
                        let qtyColor = item.Qty_Trn < 0 ? 'text-danger' : 'text-success';
                        
                        rows += `<tr>
                            <td>${i + 1}</td>
                            <td>${item.id_stok}</td>
                            <td class="fw-bold">${item.Bukti}</td>
                            <td>${item.Tgl}</td>
                            <td>${item.Jam}</td>
                            <td>${item.Kode_Lokasi}</td>
                            <td>${item.Kode_Barang}</td>
                            <td class="${qtyColor} fw-bold">${item.Qty_Trn}</td>
                            <td><span class="badge bg-secondary" style="font-size: 10px;">${item.Prog}</span></td>
                        </tr>`;
                    });
                } else {
                    rows = '<tr><td colspan="9" class="text-secondary italic">Data tidak ditemukan</td></tr>';
                }
                $('#table-body').html(rows);
            },
            error: function(xhr) {
                $('#table-body').html('<tr><td colspan="9" class="text-danger">Gagal memuat data.</td></tr>');
            }
        });
    }


    $('#btn-cari').on('click', function() {
        loadData($('#filter-bukti').val(), $('#filter-tgl').val());
    });

    $('#btn-reset').on('click', function() {
        $('#filter-bukti').val('');
        $('#filter-tgl').val('');
        loadData();
    });
});
</script>
@endsection