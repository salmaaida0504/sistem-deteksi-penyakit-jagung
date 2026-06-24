@extends('layouts.admin')

@section('title', 'Master Jenis Produk — BHUMI Admin')

@section('content')
    <div class="admin-page-header">
        <h1>Master Jenis Produk</h1>
        <a href="{{ route('admin.jenis_produk.create') }}" class="btn-add">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Tambah
        </a>
    </div>

    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">No</th>
                    <th style="text-align: center;">Jenis Produk</th>
                    <th style="width: 130px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jenis_produks as $index => $jp)
                    <tr>
                        <td style="text-align: center; font-weight: 600;">{{ $index + 1 }}</td>
                        <td>{{ $jp->jenis_produk }}</td>
                        <td style="text-align: right;">
                            <div class="konten-actions" style="justify-content: flex-center;">
                                <a href="{{ route('admin.jenis_produk.edit', $jp->id_jenis) }}" class="btn-edit">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                </a>
                                <button class="btn-delete" onclick="confirmDelete({{ $jp->id_jenis }}, '{{ $jp->jenis_produk }}')">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center;">Belum ada data jenis produk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script>
        function confirmDelete(id, name) {
            Swal.fire({
                title: "Hapus Jenis Produk",
                html: `Apakah anda yakin ingin menghapus jenis produk <strong>${name}</strong>?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.createElement('form');
                    form.action = `/admin/jenis-produk/${id}`;
                    form.method = 'POST';
                    form.innerHTML = `@csrf @method('DELETE')`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@endsection
