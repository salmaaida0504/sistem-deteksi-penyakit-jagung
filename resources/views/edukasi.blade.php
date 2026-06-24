@extends('layouts.app')

@section('title', 'Edukasi Penyakit Tanaman Jagung — BHUMI')

@section('content')

    {{-- ==================== HEADER ==================== --}}
    <div class="edukasi-header" style="position: relative; overflow: hidden; padding: 120px 0 60px;">
        <div class="hero-bg" style="background-image: url('{{ asset('images/LAHAN JAGUNG.jpg') }}'); z-index: 1;"></div>
        <div class="hero-overlay" style="z-index: 2;"></div>
        <div class="container" style="position: relative; z-index: 3;">
            <h1 data-aos="fade-down"
                style="color: var(--white); font-size: 2.4rem; font-weight: 800; text-shadow: 0 2px 10px rgba(0,0,0,0.5);">
                Edukasi OPT Tanaman Jagung</h1>
            <p data-aos="fade-down" data-aos-delay="200"
                style="color: rgba(255,255,255,0.9); font-size: 1.05rem; margin-top: 8px; text-shadow: 0 2px 8px rgba(0,0,0,0.5);">
                Kenali berbagai jenis Organisme Pengganggu Tumbuhan (OPT) yang dapat menyerang <br>tanaman jagung, penyebab, gejala, hingga cara penanganannya
            </p>
        </div>
    </div>

    {{-- ==================== ACCORDION ==================== --}}
    <section class="edukasi-section" style="min-height: 60vh; padding-bottom: 80px;">
        <div class="container">
            <div class="accordion-list" id="accordionList">
                @forelse ($opts as $opt)
                    <div class="accordion-item {{ $openSlug === Str::slug($opt->nama_opt) ? 'open' : '' }}"
                        id="accordion-{{ Str::slug($opt->nama_opt) }}" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="accordion-header" style="background-color: #379777;"
                            onclick="toggleAccordion(this)">
                            <span class="accordion-dot"
                                style="background-color: #1b4d3c;"></span>
                            <div class="accordion-title-wrap">
                                <div class="accordion-title" style="color:white;">{{ $opt->nama_opt }}</div>
                                <div class="accordion-latin" style="color:#e2e8f0;">{{ ucwords(strtolower($opt->jenis_opt)) }}</div>
                            </div>
                            <span class="accordion-chevron" style="color:white;">▼</span>
                        </div>
                        <div class="accordion-body">
                            <div class="accordion-content">
                                @if ($opt->foto_opt)
                                    <div class="accordion-content-section" style="margin-bottom: 24px;">
                                        <h4 style="margin-bottom: 12px;">Contoh Daun</h4>
                                        <img src="{{ asset('storage/' . $opt->foto_opt) }}" alt="{{ $opt->nama_opt }}"
                                            style="max-width: 250px; width: 100%; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                    </div>
                                @endif

                                @if ($opt->deskripsi)
                                    <div class="accordion-content-section">
                                        <h4>Deskripsi</h4>
                                        <p>{{ $opt->deskripsi }}</p>
                                    </div>
                                @endif

                                @if ($opt->penyebab)
                                    <div class="accordion-content-section">
                                        <h4>Penyebab</h4>
                                        <p>{{ $opt->penyebab }}</p>
                                    </div>
                                @endif

                                @if ($opt->gejala)
                                    <div class="accordion-content-section">
                                        <h4>Gejala</h4>
                                        <p>{{ $opt->gejala }}</p>
                                    </div>
                                @endif

                                @if ($opt->pencegahan)
                                    <div class="accordion-content-section">
                                        <h4>Pencegahan</h4>
                                        <p>{{ $opt->pencegahan }}</p>
                                    </div>
                                @endif

                                @if ($opt->penanganan)
                                    <div class="accordion-content-section">
                                        <h4>Penanganan</h4>
                                        <p>{{ $opt->penanganan }}</p>
                                    </div>
                                @endif

                                @if ($opt->produk->count() > 0)
                                    <div class="accordion-content-section">
                                        <h4>Rekomendasi Produk</h4>
                                        <div class="pesticide-grid">
                                            @foreach ($opt->produk as $produk)
                                                <div class="pesticide-card"
                                                    onclick="window.location.href='/produk?id={{ $produk->id_produk }}'"
                                                    style="cursor: pointer;" title="Lihat detail produk">
                                                    <div class="pesticide-card-header">
                                                        <div class="pesticide-card-title">{{ $produk->nama_produk }}</div>
                                                        <span class="pesticide-type-badge">{{ $produk->jenisProduk->jenis_produk ?? 'Umum' }}</span>
                                                    </div>
                                                    <div class="pesticide-card-details">
                                                        @if ($produk->manfaat)
                                                            <div class="pesticide-detail-item">
                                                                <span class="pesticide-detail-label">Manfaat</span>
                                                                <span class="pesticide-detail-value">{{ Str::limit($produk->manfaat, 50) }}</span>
                                                            </div>
                                                        @endif
                                                        @if ($produk->dosis_penggunaan)
                                                            <div class="pesticide-detail-item">
                                                                <span class="pesticide-detail-label">Dosis</span>
                                                                <span
                                                                    class="pesticide-detail-value">{{ $produk->dosis_penggunaan }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center" style="padding: 60px 40px; color: #6b7280; background: white; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #e5e7eb; margin-top: 20px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 48px; height: 48px; margin: 0 auto 16px auto; opacity: 0.5;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p style="font-size: 1.1rem; font-weight: 500;">Belum ada data konten edukasi yang tersedia.</p>
                        <p style="font-size: 0.95rem; margin-top: 8px;">Informasi OPT akan ditampilkan di sini setelah ditambahkan.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <script>
        function toggleAccordion(header) {
            const item = header.parentElement;
            const isOpen = item.classList.contains('open');

            // Close all
            document.querySelectorAll('.accordion-item').forEach(el => el.classList.remove('open'));

            // Toggle current
            if (!isOpen) {
                item.classList.add('open');
                // Smooth scroll to item
                setTimeout(() => {
                    item.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 100);
            }
        }

        // Auto-scroll to open accordion on page load
        document.addEventListener('DOMContentLoaded', function() {
            const openItem = document.querySelector('.accordion-item.open');
            if (openItem) {
                setTimeout(() => {
                    openItem.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }, 500);
            }

            // Check URL hash
            const hash = window.location.hash;
            if (hash) {
                const target = document.querySelector(hash);
                if (target && target.classList.contains('accordion-item')) {
                    target.classList.add('open');
                    setTimeout(() => {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }, 500);
                }
            }
        });
    </script>

@endsection
