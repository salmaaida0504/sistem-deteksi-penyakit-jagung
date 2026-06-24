@extends('layouts.admin')

@section('title', 'Edit Produk — BHUMI Admin')

@section('content')
<div class="admin-page-header">
    <h1>Edit Produk</h1>
    <a href="{{ route('admin.produk.index') }}" class="btn-back">Kembali</a>
</div>

<form action="{{ route('admin.produk.update', $produk->id_produk) }}" method="POST" enctype="multipart/form-data" class="form-layout-split">
    @csrf
    @method('PUT')

    <div class="form-grid-top">
        <div class="form-group" style="margin-bottom: 0;">
            <label>Foto Produk (Gambar Lampiran)</label>
            @if($produk->foto_produk)
                <div style="margin-bottom: 12px;">
                    <img src="{{ asset('storage/' . $produk->foto_produk) }}" alt="Preview" style="max-height: 150px; border-radius: 8px;">
                </div>
            @endif
            <input type="file" name="image" class="form-control" accept="image/*">
            <p class="form-hint" style="margin-top: 8px;">Abaikan jika tidak ingin mengubah foto.</p>
        </div>
    </div>

    <div class="form-grid-bottom">
        <div style="font-size: 1.1rem; font-weight: 700; margin-bottom: 20px;">Detail Produk</div>

        <div class="form-row">
            <div class="form-group">
                <label>Jenis Produk</label>
                <select name="id_jenis" class="form-control" required>
                    <option value="">Pilih Jenis</option>
                    @foreach($jenis_produks as $jp)
                        <option value="{{ $jp->id_jenis }}" {{ $jp->id_jenis == $produk->id_jenis ? 'selected' : '' }}>{{ $jp->jenis_produk }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Nama Produk</label>
                <input type="text" name="nama_produk" class="form-control" value="{{ $produk->nama_produk }}" required>
            </div>
        </div>

        <div class="form-group">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                <label style="margin-bottom: 0;">Target OPT</label>
                <p class="form-hint" style="margin: 0;">Pilih satu atau lebih target OPT. Hasil dari "Kelola Konten" akan muncul di sini otomatis.</p>
            </div>
            <div class="disease-checkbox-grid">
                @foreach($opts as $opt)
                    <label class="disease-checkbox-item">
                        <input type="checkbox" name="opt_ids[]" value="{{ $opt->id_opt }}" {{ in_array($opt->id_opt, $selectedOpts) ? 'checked' : '' }}>
                        <span>{{ $opt->nama_opt }}</span>
                    </label>
                @endforeach
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi_produk" class="form-control" rows="4">{{ $produk->deskripsi_produk }}</textarea>
            </div>
            <div class="form-group">
                <label>Manfaat</label>
                <textarea name="manfaat" class="form-control" rows="4">{{ $produk->manfaat }}</textarea>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Keunggulan</label>
                <textarea name="keunggulan" class="form-control" rows="4">{{ $produk->keunggulan }}</textarea>
            </div>
            <div class="form-group">
                <label>Dosis Penggunaan</label>
                <input type="text" name="dosis_penggunaan" class="form-control" value="{{ $produk->dosis_penggunaan }}">
            </div>
        </div>

        <div class="form-actions">
            <button type="button" class="btn-save" onclick="confirmSave(event)">Simpan</button>
            <button type="reset" class="btn-cancel">Reset</button>
        </div>
    </div>
</form>

@push('scripts')
<script>
    function confirmSave(event) {
        event.preventDefault();
        const form = event.target.closest('form');
        Swal.fire({
            title: 'Konfirmasi Simpan',
            text: 'Apakah anda yakin ingin menyimpan perubahan data ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2d7d62',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
</script>
@endpush
@endsection
