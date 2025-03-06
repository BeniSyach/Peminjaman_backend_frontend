<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class BukuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('limit', 10);
        $page = $request->input('page', 1);
        $search = $request->input('search');
        $sortColumn = $request->input('sort', 'id');
        $sortDirection = $request->input('order', 'asc');

        $query = Buku::query();

        if ($search) {
            $query->where('judul', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%")
                  ->orWhere('pengarang', 'like', "%{$search}%");
        }

        $buku = $query->orderBy($sortColumn, $sortDirection)->paginate($perPage, ['*'], 'page', $page);
        
        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => Buku::count(),
            'recordsFiltered' => $buku->total(),
            'data' => $buku,
        ]);
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
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'kode_buku' => 'required|unique:buku',
            'kategori' => 'required',
            'pengarang' => 'required',
            'judul' => 'required',
            'penerbit' => 'required',
            'tahun' => 'required|digits:4|integer',
            'jumlah' => 'required|integer',
            'sisa' => 'nullable|integer',
            'gambar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            if ($request->hasFile('gambar')) {
                $path = $request->file('gambar')->store('buku', 'public');
            } else {
                return response()->json(['error' => 'Gambar tidak ditemukan'], 400);
            }

          // Simpan data ke database
        $buku = Buku::create([
            'kode_buku' => $request->kode_buku,
            'kategori' => $request->kategori,
            'pengarang' => $request->pengarang,
            'judul' => $request->judul,
            'penerbit' => $request->penerbit,
            'tahun' => $request->tahun,
            'jumlah' => $request->jumlah,
            'sisa' => $request->jumlah, // Sisa sama dengan jumlah awal
            'gambar' => $path,
        ]);
            return response()->json($buku, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menambahkan buku', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        try {
            $buku = Buku::findOrFail($id);
            return response()->json($buku);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Buku tidak ditemukan'], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Buku $buku)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $buku = Buku::findOrFail($id);
            
            $request->validate([
                'kode_buku' => 'nullable|unique:buku,kode_buku,' . $id,
                'kategori' => 'nullable',
                'pengarang' => 'nullable',
                'judul' => 'nullable',
                'penerbit' => 'nullable',
                'tahun' => 'nullable|digits:4|integer',
                'jumlah' => 'nullable|integer',
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);
            
            if ($request->hasFile('gambar')) {
                Storage::delete('public/' . $buku->gambar);
                $path = $request->file('gambar')->store('buku', 'public');
                $buku->gambar = $path;
            }

            // dd($request->all());
            
            $buku->update($request->except('gambar'));
            $buku->save();
            return response()->json($buku);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memperbarui buku', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $buku = Buku::findOrFail($id);
            $buku->delete();
            return response()->json(['message' => 'Buku berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menghapus buku', 'message' => $e->getMessage()], 500);
        }
    }
}
