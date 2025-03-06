<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'kode_peminjaman',
        'user_id',
        'buku_id',
        'tanggal_pinjam',
        'tanggal_kembali'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }

     /**
     * Event creating untuk mengurangi stok buku
     */
    protected static function booted()
    {
        static::creating(function ($peminjaman) {
            $buku = Buku::findOrFail($peminjaman->buku_id);

            if ($buku->sisa <= 0) {
                throw new \Exception("Stok buku '{$buku->judul}' habis!");
            }

            $buku->keluar += 1;
            $buku->sisa -= 1;
            $buku->save();
        });

        static::deleting(function ($peminjaman) {
            $buku = Buku::findOrFail($peminjaman->buku_id);
            $buku->keluar -= 1;
            $buku->sisa += 1;
            $buku->save();
        });
    }
}
