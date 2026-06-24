@extends('layouts.admin')

@section('title', 'Kelola Produk — BHUMI Admin')

@section('content')
    <div class="admin-page-header">
        <h1>Kelola Produk</h1>
        <a href="{{ route('admin.produk.create') }}" class="btn-add">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Tambah
        </a>
    </div>

    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">No</th>
                    <th style="width: 80px; text-align: center;">Foto</th>
                    <th style="text-align: center;">Nama Produk</th>
                    <th style="text-align: center;">Jenis</th>
                    <th style="text-align: center;">Dosis Penggunaan</th>
                    <th style="width: 80px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produks as $index => $produk)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td style="text-align: center;">
                            @if($produk->foto_produk)
                                <img src="{{ asset('storage/' . $produk->foto_produk) }}" alt="Foto" style="width: 50px; height: 50px; object-fit: cover; border-radius: var(--admin-radius-sm);">
                            @else
                                <div style="width: 50px; height: 50px; background: #f3f4f6; border-radius: var(--admin-radius-sm); display: flex; align-items: center; justify-content: center; color: #9ca3af; font-size: 0.7rem;">-</div>
                            @endif
                        </td>
                        <td><span style="font-weight: 600;">{{ $produk->nama_produk }}</span></td>
                        <td>{{ $produk->jenisProduk->jenis_produk ?? '-' }}</td>
                        <td>{{ $produk->dosis_penggunaan }}</td>
                        <td style="text-align: right;">
                            <div class="konten-actions" style="justify-content: flex-end;">
                                <button class="btn-view" onclick="openModal('modal-produk-{{ $produk->id_produk }}')">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                </button>
                                <a href="{{ route('admin.produk.edit', $produk->id_produk) }}" class="btn-edit">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                </a>
                                <button class="btn-delete" onclick="confirmDelete({{ $produk->id_produk }}, '{{ $produk->nama_produk }}')">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                </button>
                            </div>

                            <!-- Modal -->
                            <div id="modal-produk-{{ $produk->id_produk }}" class="modal-overlay">
                                <div class="modal-content" style="text-align: left;">
                                    <button class="modal-close" onclick="closeModal('modal-produk-{{ $produk->id_produk }}')">&times;</button>
                                    <h3 style="margin-top: 0; margin-bottom: 20px;">Detail Produk</h3>
                                    @if($produk->foto_produk)
                                        <div style="margin-bottom: 20px; text-align: center;">
                                            <img src="{{ asset('storage/' . $produk->foto_produk) }}" alt="Foto" style="max-height: 200px; border-radius: 8px;">
                                        </div>
                                    @endif
                                    <table style="width: 100%; border-collapse: collapse;">
                                        <tr><td style="padding: 8px 0; width: 140px; font-weight: 600;">Jenis Produk</td><td>: {{ $produk->jenisProduk->jenis_produk ?? '-' }}</td></tr>
                                        <tr><td style="padding: 8px 0; font-weight: 600;">Nama Produk</td><td>: {{ $produk->nama_produk }}</td></tr>
                                        <tr><td style="padding: 8px 0; font-weight: 600;">Target OPT</td><td>:
                                            @foreach($produk->opt as $opt)
                                                <span style="display:inline-block; background:#e5e7eb; padding:2px 8px; border-radius:4px; font-size:0.8rem; margin-right:4px; font-weight:500;">{{ $opt->nama_opt }}</span>
                                            @endforeach
                                        </td></tr>
                                        <tr><td style="padding: 8px 0; font-weight: 600; vertical-align: top;">Deskripsi</td><td>: {{ $produk->deskripsi_produk }}</td></tr>
                                        <tr><td style="padding: 8px 0; font-weight: 600; vertical-align: top;">Manfaat</td><td>: {{ $produk->manfaat }}</td></tr>
                                        <tr><td style="padding: 8px 0; font-weight: 600; vertical-align: top;">Keunggulan</td><td>: {{ $produk->keunggulan }}</td></tr>
                                        <tr><td style="padding: 8px 0; font-weight: 600; vertical-align: top;">Dosis Penggunaan</td><td>: {{ $produk->dosis_penggunaan }}</td></tr>
                                    </table>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center;">Belum ada produk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script>
        function confirmDelete(id, name) {
            Swal.fire({
                title: "Hapus Produk",
                html: `Apakah anda yakin ingin menghapus produk <strong>${name}</strong>?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.createElement('form');
                    form.action = `/admin/produk/${id}`;
                    form.method = 'POST';
                    form.innerHTML = `@csrf @method('DELETE')`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@endsection
