@extends('layouts.admin')

@section('title', 'Edit Jenis Produk — BHUMI Admin')

@section('content')
<div class="admin-page-header">
    <h1>Edit Jenis Produk</h1>
    <a href="{{ route('admin.jenis_produk.index') }}" class="btn-back">Kembali</a>
</div>

<form action="{{ route('admin.jenis_produk.update', $jenis_produk->id_jenis) }}" method="POST" class="form-layout-split">
    @csrf
    @method('PUT')
    <div class="form-grid-bottom">
        <div style="font-size: 1.1rem; font-weight: 700; margin-bottom: 20px;">Detail Jenis Produk</div>

        <div class="form-group">
            <label>Nama Jenis Produk</label>
            <input type="text" name="jenis_produk" class="form-control" value="{{ $jenis_produk->jenis_produk }}" required>
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
