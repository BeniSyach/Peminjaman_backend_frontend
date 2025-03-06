<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Buku;

class PeminjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 10); // Default 10 data per halaman
            $peminjaman = Peminjaman::with(['user', 'buku'])->paginate($perPage);

            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => Peminjaman::count(),
                'recordsFiltered' => $peminjaman->total(),
                'data' => $peminjaman,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengambil data peminjaman', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'kode_peminjaman' => 'required|unique:peminjaman,kode_peminjaman',
                'user_id' => 'required|exists:users,id',
                'buku_id' => 'required|exists:buku,id',
                'tanggal_pinjam' => 'required|date',
                'tanggal_kembali' => 'nullable|date|after_or_equal:tanggal_pinjam',
            ]);

            $peminjaman = Peminjaman::create($request->all());

            DB::commit();

            return response()->json($peminjaman, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menambahkan peminjaman', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
   public function show($id)
    {
        try {
            $peminjaman = Peminjaman::with(['user', 'buku'])->find($id);

            if (!$peminjaman) {
                return response()->json(['error' => 'Peminjaman tidak ditemukan'], 404);
            }

            return response()->json($peminjaman);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengambil data peminjaman', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Peminjaman $peminjaman)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $peminjaman = Peminjaman::find($id);

            if (!$peminjaman) {
                return response()->json(['error' => 'Peminjaman tidak ditemukan'], 404);
            }

            $request->validate([
                'kode_peminjaman' => 'nullable|unique:peminjaman,kode_peminjaman,' . $id,
                'user_id' => 'nullable|exists:users,id',
                'buku_id' => 'nullable|exists:buku,id',
                'tanggal_pinjam' => 'nullable|date',
                'tanggal_kembali' => 'nullable|date|after_or_equal:tanggal_pinjam',
            ]);

            DB::beginTransaction();

            if ($request->buku_id != $peminjaman->buku_id) {
                // Kembalikan stok buku lama
                $bukuLama = Buku::findOrFail($peminjaman->buku_id);
                $bukuLama->keluar -= 1;
                $bukuLama->sisa += 1;
                $bukuLama->save();
    
                // Kurangi stok buku baru jika tersedia
                $bukuBaru = Buku::findOrFail($request->buku_id);
                if ($bukuBaru->sisa <= 0) {
                    throw new \Exception("Stok buku '{$bukuBaru->judul}' habis!");
                }
                $bukuBaru->keluar += 1;
                $bukuBaru->sisa -= 1;
                $bukuBaru->save();
            }

            $peminjaman->update($request->all());

            DB::commit();

            return response()->json($peminjaman);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memperbarui peminjaman', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $peminjaman = Peminjaman::find($id);

            if (!$peminjaman) {
                return response()->json(['error' => 'Peminjaman tidak ditemukan'], 404);
            }

            $peminjaman->delete();

            return response()->json(['message' => 'Peminjaman berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menghapus peminjaman', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Mengembalikan buku yang dipinjam
     */
    public function returnBook($id)
    {
        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::find($id);

            if (!$peminjaman) {
                return response()->json(['error' => 'Peminjaman tidak ditemukan'], 404);
            }

            if ($peminjaman->status === 'dikembalikan') {
                return response()->json(['error' => 'Buku sudah dikembalikan'], 400);
            }

            // Update status peminjaman
            $peminjaman->status = 'dikembalikan';
            $peminjaman->save();

            // Update jumlah keluar dan sisa di buku
            $buku = Buku::findOrFail($peminjaman->buku_id);
            $buku->keluar -= 1;
            $buku->sisa += 1;
            $buku->save();

            DB::commit();
            return response()->json(['message' => 'Buku berhasil dikembalikan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Gagal mengembalikan buku', 'message' => $e->getMessage()], 500);
        }
    }
}
