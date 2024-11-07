@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header text-center">
        <h3>LAPORAN KARTU STOK</h3>
        <p>Tanggal Cetak: {{ now()->format('Y-m-d') }}</p>
        <p>Periode: {{ $startDate }} - {{ $endDate }}</p>
        <p>Nama Barang: {{ $barang->nama_barang }}</p>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tanggal Masuk</th>
                    <th>Jumlah Masuk</th>
                    <th>Tanggal Keluar</th>
                    <th>Jumlah Keluar</th>
                    <th>Sisa Stok</th>
                </tr>
            </thead>
            <tbody>
                @php $sisaStok = $barang->stok->jumlah ?? 0; @endphp

                @foreach($masuk as $masukItem)
                    <tr>
                        <td>{{ $masukItem->tgl_masuk }}</td>
                        <td>{{ $masukItem->jumlah }}</td>
                        <td>-</td>
                        <td>-</td>
                        <td>{{ $sisaStok }}</td>
                    </tr>
                @endforeach

                @foreach($keluar as $keluarItem)
                    <tr>
                        <td>-</td>
                        <td>-</td>
                        <td>{{ $keluarItem->tgl_keluar }}</td>
                        <td>{{ $keluarItem->jumlah }}</td>
                        <td>{{ $sisaStok -= $keluarItem->jumlah }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
