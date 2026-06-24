<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisProduk;
use Illuminate\Http\Request;

class JenisProdukController extends Controller
{
    public function index()
    {
        $jenis_produks = JenisProduk::all();
        return view('admin.jenis_produk.index', compact('jenis_produks'));
    }

    public function create()
    {
        return view('admin.jenis_produk.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_produk' => 'required|string|max:30',
        ]);

        JenisProduk::create($validated);

        return redirect()->route('admin.jenis_produk.index')
            ->with('success', 'Jenis Produk berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $jenis_produk = JenisProduk::findOrFail($id);
        return view('admin.jenis_produk.edit', compact('jenis_produk'));
    }

    public function update(Request $request, $id)
    {
        $jenis_produk = JenisProduk::findOrFail($id);

        $validated = $request->validate([
            'jenis_produk' => 'required|string|max:30',
        ]);

        $jenis_produk->update($validated);

        return redirect()->route('admin.jenis_produk.index')
            ->with('success', 'Jenis Produk berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jenis_produk = JenisProduk::findOrFail($id);
        $jenis_produk->delete();

        return redirect()->route('admin.jenis_produk.index')
            ->with('success', 'Jenis Produk berhasil dihapus.');
    }
}
