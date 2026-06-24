<?php

namespace App\Http\Controllers;

use App\Models\Opt;
use Illuminate\Http\Request;

class EdukasiController extends Controller
{
    public function index(Request $request)
    {
        $opts = Opt::with('produk.jenisProduk')->orderBy('id_opt')->get();
        $openSlug = $request->query('open');

        return view('edukasi', compact('opts', 'openSlug'));
    }
}
