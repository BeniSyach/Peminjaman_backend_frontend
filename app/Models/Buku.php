<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;

    protected $table = 'buku';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'kode_buku',
        'kategori',
        'pengarang',
        'judul',
        'penerbit',
        'tahun',
        'jumlah',
        'keluar',
        'sisa',
        'gambar'
    ];
}
