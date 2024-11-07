<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BahanBaku;
use App\Models\Keluar;
use App\Models\Barang;
use App\Models\Kemasan;
use Illuminate\Support\Facades\DB;

class KeluarBahanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:create-keluar|edit-keluar|show-keluar|delete-keluar', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-keluar', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-keluar', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-keluar', ['only' => ['destroy']]);
    }

    public function index()
    {
        $keluarBahan = Keluar::with(['barang'])->paginate(10);

        return view('keluar.bahan.index', compact('keluarBahan'));
    }

    public function create()
    {
        // Ambil data bahan baku dan kemasan yang tersedia
        $bahanBaku = BahanBaku::all();
        $kemasan = Kemasan::all();

        return view('keluar.bahan.create', compact('bahanBaku', 'kemasan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bahan_baku.*.barang_id' => 'required|exists:bahan_baku,barang_id',
            'bahan_baku.*.jumlah' => 'required|integer|min:1',
            'kemasan.*.barang_id' => 'required|exists:kemasan,barang_id',
            'kemasan.*.jumlah' => 'required|integer|min:1',
            'tgl_keluar' => 'required|date'
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->bahan_baku as $bahan) {
                $barang = Barang::findOrFail($bahan['barang_id']);
                if ($barang->stok->jumlah < $bahan['jumlah']) {
                    throw new \Exception('Jumlah bahan baku melebihi stok tersedia');
                }

                // Kurangi stok bahan baku
                $barang->stok->jumlah -= $bahan['jumlah'];
                $barang->stok->save();

                // Simpan data keluaran
                Keluar::create([
                    'barang_id' => $bahan['barang_id'],
                    'jumlah' => $bahan['jumlah'],
                    'tgl_keluar' => $request->tgl_keluar
                ]);
            }

            foreach ($request->kemasan as $kemas) {
                $barang = Barang::findOrFail($kemas['barang_id']);
                if ($barang->stok->jumlah < $kemas['jumlah']) {
                    throw new \Exception('Jumlah kemasan melebihi stok tersedia');
                }

                // Kurangi stok kemasan
                $barang->stok->jumlah -= $kemas['jumlah'];
                $barang->stok->save();

                // Simpan data keluaran
                Keluar::create([
                    'barang_id' => $kemas['barang_id'],
                    'jumlah' => $kemas['jumlah'],
                    'tgl_keluar' => $request->tgl_keluar
                ]);
            }

            DB::commit();
            return redirect()->route('keluarbahan.index')->with('success', 'Data keluar berhasil ditambahkan dan stok telah diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['msg' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $keluarBahan = Keluar::with(['barang'])->findOrFail($id);

        return view('keluar.bahan.show', compact('keluarBahan'));
    }

    public function edit($id)
    {
        $keluarBahan = Keluar::with(['barang'])->findOrFail($id);
        $bahanBaku = BahanBaku::all();
        $kemasan = Kemasan::all();

        return view('keluar.bahan.edit', compact('keluarBahan', 'bahanBaku', 'kemasan'));
    }

    public function update(Request $request, $id)
    {
        // Implementasikan kode update sesuai dengan kebijakan bisnis terkait
    }
}
