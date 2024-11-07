<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stok;
use App\Models\Masuk;
use App\Models\Keluar;

class DashboardController extends Controller

{
public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:show-dashboard', ['only' => ['index', 'show']]);

    }



    public function index()
    {
        // Monthly incoming and outgoing data
        $incomingData = Masuk::selectRaw('MONTH(tgl_masuk) as month, SUM(jumlah) as total_quantity')
            ->groupBy('month')
            ->pluck('total_quantity', 'month');

        $outgoingData = Keluar::selectRaw('MONTH(tgl_keluar) as month, SUM(jumlah) as total_quantity')
            ->groupBy('month')
            ->pluck('total_quantity', 'month');

        // Low quantity stock (assuming threshold is 15 units)
        $lowQuantityStock = Stok::where('jumlah', '<=', 15)->with('barang')->get();

        // Top selling stock (based on highest outgoing quantity)
        $topSellingStock = Keluar::selectRaw('barang_id, SUM(jumlah) as sold_quantity')
            ->with('barang')
            ->groupBy('barang_id')
            ->orderByDesc('sold_quantity')
            ->take(3)
            ->get();

        return view('dashboard', compact('incomingData', 'outgoingData', 'lowQuantityStock', 'topSellingStock'));
    }

    
}
