<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('buku', function (Blueprint $table) {
            $table->id();
            $table->string('kode_buku')->unique();
            $table->string('kategori');
            $table->string('pengarang');
            $table->string('judul');
            $table->string('penerbit');
            $table->year('tahun');
            $table->unsignedInteger('jumlah');
            $table->unsignedInteger('keluar')->default(0);
            $table->unsignedInteger('sisa')->default(0);
            $table->string('gambar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku');
    }
};
