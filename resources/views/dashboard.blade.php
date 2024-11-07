@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Incoming & Outgoing Chart -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
            <span>Incoming & Outgoing</span>
            <button class="btn btn-secondary btn-sm">Monthly</button>
        </div>
        <div class="card-body">
            <canvas id="incomingOutgoingChart"></canvas>
        </div>
    </div>

    <!-- Low Quantity Stock -->
    <div class="card mb-4">
        <div class="card-header">Low Quantity Stock</div>
        <div class="card-body">
            @foreach($lowQuantityStock as $stock)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <strong>{{ $stock->barang->nama_barang ?? 'Unknown' }}</strong>
                        <p>Remaining Quantity: {{ $stock->jumlah }} {{ $stock->satuan }}</p>
                    </div>
                    <span class="badge bg-danger">Low</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Top Selling Stock -->
    <div class="card mb-4">
        <div class="card-header">Top Selling Stock</div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Sold Quantity</th>
                        <th>Remaining Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topSellingStock as $stock)
                        <tr>
                            <td>{{ $stock->barang->nama_barang ?? 'Unknown' }}</td>
                            <td>{{ $stock->sold_quantity }}</td>
                            <td>{{ $stock->barang->stok->jumlah ?? 0 }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx = document.getElementById('incomingOutgoingChart').getContext('2d');
    var incomingOutgoingChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [
                {
                    label: 'Incoming',
                    data: {!! json_encode(array_values($incomingData->toArray())) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                },
                {
                    label: 'Outgoing',
                    data: {!! json_encode(array_values($outgoingData->toArray())) !!},
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endsection
