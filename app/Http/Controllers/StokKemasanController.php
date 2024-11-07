<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stok;
use App\Http\Requests\StoreStokKemasanRequest;
use App\Http\Requests\UpdateStokRequest;
use Illuminate\View\View;
use App\Models\BahanBaku;
use App\Models\Kemasan;
use App\Models\ProdukJadi;
use App\Models\Barang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class StokKemasanController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:create-stok|edit-stok|show-stock|delete-stok', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-stok', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-stok', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-stok', ['only' => ['destroy']]);
        $this->middleware('permission:show-stock', ['only' => ['index','show']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $row = (int)request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }


        $stokkemasan = Barang::with(['stok', 'kemasan'])
            ->where('kategori_barang', 'Kemasan') // Filter berdasarkan kategori
            ->filter(request(['search']))
            ->sortable()
            ->paginate($row)
            ->appends(request()->query());

        return view('stok.kemasan.index', ['stokkemasan' => $stokkemasan,]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('stok.kemasan.create');
    }


    public function store(StoreStokKemasanRequest $request): RedirectResponse
    {
        // Simpan atau ambil barang berdasarkan nama_barang
        $barang = Barang::firstOrCreate(
            ['nama_barang' => $request->nama],  // Cari atau buat berdasarkan nama_barang
            ['kategori_barang' => 'Kemasan'] // Default kategori untuk Bahan Baku
        );

        // Simpan data Bahan Baku
        Kemasan::create([
            'barang_id'    => $barang->id,
            'supplier'     => $request->supplier,
            'jenis_bahan'  => $request->jenis_bahan,
            'jenis_kemasan'=> $request->jenis_kemasan,
            'warna_badan'  => $request->warna_badan,
            'warna_tutup'  => $request->warna_tutup,
            'volume'       => $request->volume,
            'harga'        => $request->harga,
            'pemeriksa'    => $request->pemeriksa,
            'keterangan'   => $request->keterangan,
        ]);

        // Simpan data Stok
        Stok::create([
            'barang_id' => $barang->id,
            'satuan'    => $request->satuan,
            'jumlah'    => $request->jumlah ?? 0,  // Default 0 jika null
        ]);

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('stokkemasan.index')
            ->with('success', 'Data stok kemasan berhasil ditambahkan.');
    }


    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        // Ambil barang beserta relasi bahan baku dan stok
        $stokKemasan = Barang::with(['kemasan', 'stok'])
            ->where('id', $id)
            ->where('kategori_barang', 'Kemasan')
            ->firstOrFail(); // Jika tidak ditemukan, tampilkan 404

        // Kirim data ke view
        return view('stok.kemasan.show', ['stokKemasan' => $stokKemasan]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Stok $stok): View
    {
        return view('stok.edit', [
            'stok' => $stok
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStokRequest $request, Stok $stok): RedirectResponse
    {
        $stok->update($request->validated());
        return redirect()->back()
            ->withSuccess('Stock data updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stok $stok): RedirectResponse
    {
        $stok->delete();
        return redirect()->route('stok.index')
            ->withSuccess('Stock entry deleted successfully.');
    }

}

