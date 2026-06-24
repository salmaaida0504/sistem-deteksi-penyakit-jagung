<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Opt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KontenController extends Controller
{
    public function index()
    {
        $opts = Opt::all();
        return view('admin.konten.index', compact('opts'));
    }

    public function create()
    {
        return view('admin.konten.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_opt' => 'required|string|max:50',
            'nama_opt' => 'required|string|max:500',
            'deskripsi' => 'nullable|string|max:1000',
            'penyebab' => 'nullable|string|max:500',
            'gejala' => 'nullable|string|max:500',
            'pencegahan' => 'nullable|string|max:500',
            'penanganan' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $validated['foto_opt'] = $request->file('image')->store('opts', 'public');
        }

        Opt::create($validated);

        return redirect()->route('admin.konten.index')
            ->with('success', 'Konten berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $opt = Opt::findOrFail($id);
        return view('admin.konten.edit', compact('opt'));
    }

    public function update(Request $request, $id)
    {
        $opt = Opt::findOrFail($id);

        $validated = $request->validate([
            'jenis_opt' => 'required|string|max:50',
            'nama_opt' => 'required|string|max:500',
            'deskripsi' => 'nullable|string|max:1000',
            'penyebab' => 'nullable|string|max:500',
            'gejala' => 'nullable|string|max:500',
            'pencegahan' => 'nullable|string|max:500',
            'penanganan' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('image')) {
            if ($opt->foto_opt && Storage::disk('public')->exists($opt->foto_opt)) {
                Storage::disk('public')->delete($opt->foto_opt);
            }
            $validated['foto_opt'] = $request->file('image')->store('opts', 'public');
        }

        $opt->update($validated);

        return redirect()->route('admin.konten.index')
            ->with('success', 'Konten berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $opt = Opt::findOrFail($id);

        if ($opt->foto_opt && Storage::disk('public')->exists($opt->foto_opt)) {
            Storage::disk('public')->delete($opt->foto_opt);
        }

        $opt->delete();

        return redirect()->route('admin.konten.index')
            ->with('success', 'Konten berhasil dihapus.');
    }
}
