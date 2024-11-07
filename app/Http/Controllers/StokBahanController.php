<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stok;
use App\Http\Requests\StoreStokBahanRequest;
use App\Http\Requests\UpdateStokBahanRequest;
use Illuminate\View\View;
use App\Models\BahanBaku;
use App\Models\Barang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;


class StokBahanController extends Controller
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


        $stokbahan = Barang::with(['stok', 'bahanBaku'])
            ->where('kategori_barang', 'Bahan Baku') // Filter berdasarkan kategori
            ->filter(request(['search']))
            ->sortable()
            ->paginate($row)
            ->appends(request()->query());



        return view('stok.bahanbaku.index', ['stokbahan' => $stokbahan,]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('stok.bahanbaku.create');
    }


    public function store(StoreStokBahanRequest $request): RedirectResponse
    {
        // Simpan atau ambil barang berdasarkan nama_barang
        $barang = Barang::firstOrCreate(
            ['nama_barang' => $request->nama],  // Cari atau buat berdasarkan nama_barang
            ['kategori_barang' => 'Bahan Baku'] // Default kategori untuk Bahan Baku
        );

        // Simpan data Bahan Baku
        BahanBaku::create([
            'barang_id'   => $barang->id,
            'supplier'    => $request->supplier,
            'batch'       => $request->batch,
            'tgl_exp'     => $request->tgl_exp,
            'warna'       => $request->warna,
            'bentuk'      => $request->bentuk,
            'harga'       => $request->harga,
            'penyimpanan' => $request->penyimpanan,
            'pemeriksa'   => $request->pemeriksa,
            'keterangan'  => $request->keterangan,
        ]);

        // Simpan data Stok
        Stok::create([
            'barang_id' => $barang->id,
            'satuan'    => $request->satuan,
            'jumlah'    => $request->jumlah ?? 0,  // Default 0 jika null
        ]);

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('stokbahan.index')
            ->with('success', 'Data stok bahan berhasil ditambahkan.');
    }


    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        // Ambil barang beserta relasi bahan baku dan stok
        $stokBahan = Barang::with(['bahanBaku', 'stok'])
            ->where('id', $id)
            ->where('kategori_barang', 'Bahan Baku')
            ->firstOrFail(); // Jika tidak ditemukan, tampilkan 404

        // Kirim data ke view
        return view('stok.bahanbaku.show', ['stokBahan' => $stokBahan]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
{
    // Ambil data Bahan Baku dengan relasi Barang
    $bahanBaku = BahanBaku::with('barang')
        ->whereHas('barang', function($query) {
            $query->where('kategori_barang', 'Bahan Baku'); // Filter berdasarkan kategori
        })
        ->findOrFail($id); // Jika tidak ditemukan, akan mengarah ke halaman 404

    // Log data bahan baku yang diambil
    \Log::info($bahanBaku); // Lihat data bahan baku

    // Kirim data ke view
    return view('stok.bahanbaku.edit', ['bahanBaku' => $bahanBaku]);
}






    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStokBahanRequest $request, BahanBaku $bahanBaku): RedirectResponse
{
    // Mengaktifkan logging query
    \DB::enableQueryLog();

    // Pastikan bahan baku dan relasinya ada
    if (!$bahanBaku || !$bahanBaku->barang) {
        return redirect()->route('stokbahan.index')->with('error', 'Bahan baku atau barang tidak ditemukan.');
    }

    // Cek apakah kategori barang adalah 'Bahan Baku'
    if ($bahanBaku->barang->kategori_barang !== 'Bahan Baku') {
        return redirect()->route('stokbahan.index')->with('error', 'Kategori barang tidak valid untuk diupdate.');
    }

    // Update data barang termasuk kategori
    $bahanBaku->barang->update([
        'nama_barang' => $request->nama
    ]);

    // Update data bahan baku
    $bahanBaku->update($request->only([
        'supplier',
        'batch',
        'tgl_exp',
        'warna',
        'bentuk',
        'harga',
        'penyimpanan',
        'pemeriksa',
        'keterangan'
    ]));

    // Update stok
    $stok = Stok::where('barang_id', $bahanBaku->barang_id)->firstOrFail();
    $stok->update([
        'jumlah' => $request->jumlah,
        'satuan' => $request->satuan,
    ]);

    // Log query yang dijalankan
    \Log::info(\DB::getQueryLog()); // Log semua query yang dijalankan

    return redirect()->route('stokbahan.index')
        ->with('success', 'Data stok bahan berhasil diperbarui.');
}




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BahanBaku $bahanBaku): RedirectResponse
{
    DB::beginTransaction(); // Memulai transaksi
    try {
        // Hapus semua data yang berhubungan dengan barang di tabel 'masuk'
        DB::table('masuk')->where('barang_id', $bahanBaku->barang_id)->delete();

        // Hapus semua data yang berhubungan dengan barang di tabel 'keluar'
        DB::table('keluar')->where('barang_id', $bahanBaku->barang_id)->delete();

        // Hapus stok jika ada
        $stok = Stok::where('barang_id', $bahanBaku->barang_id)->first();
        if ($stok) {
            $stok->delete(); // Hapus secara soft delete
        }

        // Hapus barang terkait
        $barang = Barang::findOrFail($bahanBaku->barang_id);
        $barang->delete(); // Hapus secara soft delete

        // Hapus bahan baku
        $bahanBaku->delete(); // Hapus secara soft delete

        DB::commit(); // Komit transaksi jika semua berhasil

        return redirect()->route('stokbahan.index')
            ->with('success', 'Data stok bahan berhasil dihapus.');
    } catch (\Exception $e) {
        DB::rollBack(); // Batalkan transaksi jika terjadi error
        return redirect()->route('stokbahan.index')
            ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
    }
}




    


}
