<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index()
    {
        return view('backend.home.main');
    }


    public function users()
    {
        return view('backend.user.main');
    }

    public function buku()
    {
        return view('backend.buku.main');
    }

    public function peminjaman()
    {
        return view('backend.peminjaman.main');
    }
}