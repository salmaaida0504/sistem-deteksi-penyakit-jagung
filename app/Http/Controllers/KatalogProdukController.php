<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class KatalogProdukController extends Controller
{
    public function index()
    {
        // Get all products and group by jenis_produk
        $pesticides = Produk::with('jenisProduk')
            ->orderBy('nama_produk')
            ->get()
            ->groupBy(function ($item) {
                return $item->jenisProduk->jenis_produk ?? 'Lainnya';
            });
            
        return view('produk', compact('pesticides'));
    }
}
