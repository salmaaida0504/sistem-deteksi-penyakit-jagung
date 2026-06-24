@extends('layouts.admin')

@section('title', 'Edit Konten OPT — BHUMI Admin')

@section('content')
<div class="admin-page-header">
    <h1>Edit Konten OPT</h1>
    <a href="{{ route('admin.konten.index') }}" class="btn-back">Kembali</a>
</div>

<form action="{{ route('admin.konten.update', $opt->id_opt) }}" method="POST" enctype="multipart/form-data" class="form-layout-split">
    @csrf
    @method('PUT')

    <div class="form-grid-top">
        <div class="form-group" style="margin-bottom: 0;">
            <label>Foto OPT (Gambar Lampiran)</label>
            @if($opt->foto_opt)
                <div style="margin-bottom: 12px;">
                    <img src="{{ asset('storage/' . $opt->foto_opt) }}" alt="Preview" style="max-height: 150px; border-radius: 8px;">
                </div>
            @endif
            <input type="file" name="image" class="form-control" accept="image/*">
            <p class="form-hint" style="margin-top: 8px;">Abaikan jika tidak ingin mengubah foto.</p>
        </div>
    </div>

    <div class="form-grid-bottom">
        <div style="font-size: 1.1rem; font-weight: 700; margin-bottom: 20px;">Detail Konten OPT</div>

        <div class="form-row">
            <div class="form-group">
                <label>Jenis OPT</label>
                <select name="jenis_opt" class="form-control" required>
                    <option value="" disabled>-- Pilih Jenis OPT --</option>
                    <option value="penyakit" {{ ucwords(strtolower($opt->jenis_opt)) == 'penyakit' ? 'selected' : '' }}>Penyakit</option>
                    <option value="hama" {{ ucwords(strtolower($opt->jenis_opt)) == 'hama' ? 'selected' : '' }}>Hama</option>
                    <option value="gulma" {{ ucwords(strtolower($opt->jenis_opt)) == 'gulma' ? 'selected' : '' }}>Gulma</option>
                    <option value="non-opt" {{ ucwords(strtolower($opt->jenis_opt)) == 'non-opt' ? 'selected' : '' }}>Non-OPT</option>
                </select>
            </div>
            <div class="form-group">
                <label>Nama OPT</label>
                <input type="text" name="nama_opt" class="form-control" value="{{ $opt->nama_opt }}" required>
            </div>
        </div>

        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="4">{{ $opt->deskripsi }}</textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Penyebab</label>
                <textarea name="penyebab" class="form-control" rows="4">{{ $opt->penyebab }}</textarea>
            </div>
            <div class="form-group">
                <label>Gejala</label>
                <textarea name="gejala" class="form-control" rows="4">{{ $opt->gejala }}</textarea>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Pencegahan</label>
                <textarea name="pencegahan" class="form-control" rows="4">{{ $opt->pencegahan }}</textarea>
            </div>
            <div class="form-group">
                <label>Penanganan</label>
                <textarea name="penanganan" class="form-control" rows="4">{{ $opt->penanganan }}</textarea>
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
