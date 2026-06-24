@extends('layouts.admin')

@section('title', 'Tambah Jenis Produk — BHUMI Admin')

@section('content')
<div class="admin-page-header">
    <h1>Tambah Jenis Produk</h1>
    <a href="{{ route('admin.jenis_produk.index') }}" class="btn-back" onclick="confirmBack(event, this.href)">Kembali</a>
</div>

<form action="{{ route('admin.jenis_produk.store') }}" method="POST" class="form-layout-split">
    @csrf
    <div class="form-grid-bottom">
        <div class="form-group">
            <label>Jenis Produk</label>
            <input type="text" name="jenis_produk" class="form-control" placeholder="isikan nama jenis produk pestisida" required>
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
