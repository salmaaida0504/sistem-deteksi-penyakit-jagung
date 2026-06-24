@extends('layouts.admin')

@section('title', 'Kelola Konten OPT — BHUMI Admin')

@section('content')
    <div class="admin-page-header">
        <h1>Kelola OPT</h1>
        <a href="{{ route('admin.konten.create') }}" class="btn-add">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Tambah
        </a>
    </div>

    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">No</th>
                    <th style="width: 20%; text-align: center;">Nama OPT</th>
                    <th style="width: 20%; text-align: center;">Jenis OPT</th>
                    <th style="width: 45%; text-align: center;">Deskripsi</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($opts as $index => $opt)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="font-weight: 600;">{{ ucwords(strtolower($opt->nama_opt)) }}</span>
                            </div>
                        </td>
                        <td>{{ ucwords(strtolower($opt->jenis_opt)) }}</td>
                        <td>{{ Str::words($opt->deskripsi, 15, '...') }}</td>
                        <td style="text-align: right;">
                            <div class="konten-actions" style="justify-content: flex-end;">
                                <button class="btn-view" onclick="openModal('modal-konten-{{ $opt->id_opt }}')">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                </button>
                                <a href="{{ route('admin.konten.edit', $opt->id_opt) }}" class="btn-edit">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                </a>
                                <button class="btn-delete" onclick="confirmDelete({{ $opt->id_opt }}, '{{ $opt->nama_opt }}')">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                </button>
                            </div>

                            <!-- Modal -->
                            <div id="modal-konten-{{ $opt->id_opt }}" class="modal-overlay">
                                <div class="modal-content" style="text-align: left;">
                                    <button class="modal-close" onclick="closeModal('modal-konten-{{ $opt->id_opt }}')">&times;</button>
                                    <h3 style="margin-top: 0; margin-bottom: 20px;">Detail OPT</h3>
                                    @if($opt->foto_opt)
                                        <div style="margin-bottom: 20px; text-align: center;">
                                            <img src="{{ asset('storage/' . $opt->foto_opt) }}" alt="Foto" style="max-height: 200px; border-radius: 8px;">
                                        </div>
                                    @endif
                                    <table style="width: 100%; border-collapse: collapse;">
                                        <tr><td style="padding: 8px 0; width: 120px; font-weight: 600;">Jenis OPT</td><td>: <span style="display:inline-block; background:#e5e7eb; padding:2px 8px; border-radius:4px; font-size:0.8rem; margin-right:4px; font-weight:500;">{{ ucwords(strtolower($opt->jenis_opt)) }}</span></td></tr>
                                        <tr><td style="padding: 8px 0; font-weight: 600;">Nama OPT</td><td>: {{ ucwords(strtolower($opt->nama_opt)) }}</td></tr>
                                        <tr><td style="padding: 8px 0; font-weight: 600; vertical-align: top;">Deskripsi</td><td>: {{ $opt->deskripsi }}</td></tr>
                                        <tr><td style="padding: 8px 0; font-weight: 600; vertical-align: top;">Penyebab</td><td>: {{ $opt->penyebab }}</td></tr>
                                        <tr><td style="padding: 8px 0; font-weight: 600; vertical-align: top;">Gejala</td><td>: {{ $opt->gejala }}</td></tr>
                                        <tr><td style="padding: 8px 0; font-weight: 600; vertical-align: top;">Pencegahan</td><td>: {{ $opt->pencegahan }}</td></tr>
                                        <tr><td style="padding: 8px 0; font-weight: 600; vertical-align: top;">Penanganan</td><td>: {{ $opt->penanganan }}</td></tr>
                                    </table>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 60px; color: #9ca3af;">Belum ada konten OPT.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script>
        function confirmDelete(id, name) {
            Swal.fire({
                title: "Hapus Konten",
                html: `Apakah anda yakin ingin menghapus konten <strong>${name}</strong>?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.createElement('form');
                    form.action = `/admin/konten/${id}`;
                    form.method = 'POST';
                    form.innerHTML = `@csrf @method('DELETE')`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@endsection
