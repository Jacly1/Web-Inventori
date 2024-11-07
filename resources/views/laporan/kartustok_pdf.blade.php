<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kartu Stok</title>
    <style>
        /* CSS untuk tampilan PDF */
        body { font-family: Arial, sans-serif; font-size: 14px; }
        h3 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h3>LAPORAN KARTU STOK</h3>
    <p>Tanggal Cetak: {{ now()->format('Y-m-d') }}</p>
    <p>Periode: {{ $startDate }} - {{ $endDate }}</p>
    <p>Nama Barang: {{ $barang->nama_barang }}</p>

    <table>
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
                    <td>-<
