@extends('layouts.admin')

@section('title', 'Tambah Produk — BHUMI Admin')

@section('content')
<div class="admin-page-header">
    <h1>Tambah Produk</h1>
    <a href="{{ route('admin.produk.index') }}" class="btn-back" onclick="confirmBack(event, this.href)">Kembali</a>
</div>

<form action="{{ route('admin.produk.store') }}" method="POST" enctype="multipart/form-data" class="form-layout-split">
    @csrf

    <div class="form-grid-top">
        <div class="form-group" style="margin-bottom: 0;">
            <label>Foto Produk (Gambar Lampiran)</label>
            <input type="file" name="image" class="form-control" accept="image/*">
            <p class="form-hint" style="margin-top: 8px;">Format file: JPG, PNG, JPEG. Disarankan resolusi HD.</p>
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
                        <option value="{{ $jp->id_jenis }}">{{ $jp->jenis_produk }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Nama Produk</label>
                <input type="text" name="nama_produk" class="form-control" required>
            </div>
        </div>

        <div class="form-group">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                <label style="margin-bottom: 0;">Target OPT</label>
                <p class="form-hint" style="margin: 0;">Pilih satu atau lebih target OPT.</p>
            </div>
            <div class="disease-checkbox-grid">
                @foreach($opts as $opt)
                    <label class="disease-checkbox-item">
                        <input type="checkbox" name="opt_ids[]" value="{{ $opt->id_opt }}">
                        <span>{{ $opt->nama_opt }}</span>
                    </label>
                @endforeach
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi_produk" class="form-control" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label>Manfaat</label>
                <textarea name="manfaat" class="form-control" rows="4"></textarea>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Keunggulan</label>
                <textarea name="keunggulan" class="form-control" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label>Dosis Penggunaan</label>
                <input type="text" name="dosis_penggunaan" class="form-control">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-save">Simpan</button>
            <button type="reset" class="btn-cancel">Reset</button>
        </div>
    </div>
</form>

@push('scripts')
<script>
    function confirmBack(event, url) {
        event.preventDefault();
        Swal.fire({
            title: 'Konfirmasi Kembali',
            text: 'Apakah anda yakin ingin kembali? Data yang belum disimpan akan hilang.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#2d7d62',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Kembali',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }
</script>
@endpush
@endsection
