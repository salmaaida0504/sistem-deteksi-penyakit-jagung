@extends('layouts.admin')

@section('title', 'Tambah Konten OPT — BHUMI Admin')

@section('content')
<div class="admin-page-header">
    <h1>Tambah Konten OPT</h1>
    <a href="{{ route('admin.konten.index') }}" class="btn-back" onclick="confirmBack(event, this.href)">Kembali</a>
</div>

<form action="{{ route('admin.konten.store') }}" method="POST" enctype="multipart/form-data" class="form-layout-split">
    @csrf

    <div class="form-grid-top">
        <div class="form-group" style="margin-bottom: 0;">
            <label>Foto OPT (Gambar Lampiran)</label>
            <input type="file" name="image" class="form-control" accept="image/*">
            <p class="form-hint" style="margin-top: 8px;">Format file: JPG, PNG, JPEG. Disarankan resolusi HD.</p>
        </div>
    </div>

    <div class="form-grid-bottom">
        <div style="font-size: 1.1rem; font-weight: 700; margin-bottom: 20px;">Detail Konten OPT</div>

        <div class="form-row">
            <div class="form-group">
                <label>Jenis OPT</label>
                <select name="jenis_opt" class="form-control" required>
                    <option value="" disabled selected>-- Pilih Jenis OPT --</option>
                    <option value="penyakit">Penyakit</option>
                    <option value="hama">Hama</option>
                    <option value="gulma">Gulma</option>
                    <option value="non-opt">Non-OPT</option>
                </select>
            </div>
            <div class="form-group">
                <label>Nama OPT</label>
                <input type="text" name="nama_opt" class="form-control" required>
            </div>
        </div>

        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="4"></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Penyebab</label>
                <textarea name="penyebab" class="form-control" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label>Gejala</label>
                <textarea name="gejala" class="form-control" rows="4"></textarea>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Pencegahan</label>
                <textarea name="pencegahan" class="form-control" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label>Penanganan</label>
                <textarea name="penanganan" class="form-control" rows="4"></textarea>
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
