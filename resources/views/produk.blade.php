@extends('layouts.app')

@section('title', 'Katalog Produk — BHUMI')

@section('content')

{{-- ==================== HEADER ==================== --}}
<div class="edukasi-header" style="position: relative; overflow: hidden; padding: 120px 0 60px;">
    <div class="hero-bg" style="background-image: url('{{ asset('images/LAHAN JAGUNG.jpg') }}'); z-index: 1;"></div>
    <div class="hero-overlay" style="z-index: 2;"></div>
    <div class="container" style="position: relative; z-index: 3;">
        <h1 data-aos="fade-down"
            style="color: var(--white); font-size: 2.4rem; font-weight: 800; text-shadow: 0 2px 10px rgba(0,0,0,0.5);">
            Katalog Produk</h1>
        <p data-aos="fade-down" data-aos-delay="200"
            style="color: rgba(255,255,255,0.9); font-size: 1.05rem; margin-top: 8px; text-shadow: 0 2px 8px rgba(0,0,0,0.5);">
            Temukan berbagai jenis produk pestisida untuk <br>mengatasi penyakit tanaman jagung anda
        </p>
    </div>
</div>


{{-- ==================== CATALOG SECTION ==================== --}}
<section class="catalog-section" style="padding: 60px 0 80px 0; background: #fafafa; min-height: 60vh;">
    <div class="container">
        @forelse($pesticides as $type => $products)
            <div class="catalog-category" data-aos="fade-up">
                <h2 class="catalog-category-title">
                    <span style="color: #379777;">JENIS PRODUK</span> / {{ strtoupper($type) }}
                </h2>

                <div class="catalog-grid">
                    @foreach($products as $product)
                        <div class="catalog-card">
                            <div class="catalog-card-image">
                                @if($product->foto_produk)
                                    <img src="{{ asset('storage/' . $product->foto_produk) }}" alt="{{ $product->nama_produk }}">
                                @else
                                    <div class="catalog-no-image">Tidak ada gambar</div>
                                @endif
                            </div>
                            <div class="catalog-card-content">
                                <span class="catalog-card-type">{{ $product->jenisProduk->jenis_produk ?? 'Umum' }}</span>
                                <h3 class="catalog-card-name">{{ $product->nama_produk }}</h3>
                                <button class="btn-detail" type="button" onclick='showProductDetail(@json($product))'>Detail</button>
                            </div>
                            <div class="catalog-card-accent"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="text-center" style="padding: 60px 40px; color: #6b7280; background: white; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #e5e7eb; margin-top: 20px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 48px; height: 48px; margin: 0 auto 16px auto; opacity: 0.5;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p style="font-size: 1.1rem; font-weight: 500;">Belum ada data produk pestisida yang tersedia.</p>
                <p style="font-size: 0.95rem; margin-top: 8px;">Informasi produk akan ditampilkan di sini setelah ditambahkan.</p>
            </div>
        @endforelse
    </div>
</section>

{{-- ==================== MODAL DETAIL PRODUK ==================== --}}
<div class="product-modal-overlay" id="productModal" onclick="closeProductModal(event)">
    <div class="product-modal" onclick="event.stopPropagation()">
        <button class="product-modal-close" onclick="closeProductModal()">&times;</button>
        <div class="product-modal-body">
            <div class="product-modal-image">
                <img id="modalImage" src="" alt="Product Image">
                <div id="modalNoImage" class="catalog-no-image" style="display: none; height: 100%;">Tidak ada gambar</div>
            </div>
            <div class="product-modal-info">
                <span class="product-modal-type" id="modalType">Type</span>
                <h2 class="product-modal-name" id="modalName">Product Name</h2>

                <div class="product-modal-details">
                    <div class="pm-detail-item">
                        <span class="pm-label">Dosis Penggunaan</span>
                        <span class="pm-value" id="modalDosage">-</span>
                    </div>
                    <div class="pm-detail-item">
                        <span class="pm-label">Keunggulan</span>
                        <span class="pm-value" id="modalKeunggulan">-</span>
                    </div>
                    <div class="pm-detail-item">
                        <span class="pm-label">Manfaat</span>
                        <span class="pm-value" id="modalManfaat">-</span>
                    </div>
                </div>

                <div class="product-modal-desc">
                    <h4>Deskripsi Produk</h4>
                    <p id="modalDescription">-</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function showProductDetail(product) {
        const modal = document.getElementById('productModal');

        // Populate data
        document.getElementById('modalType').textContent = product.jenis_produk ? product.jenis_produk.jenis_produk : 'Lainnya';
        document.getElementById('modalName').textContent = product.nama_produk || '-';
        document.getElementById('modalDosage').textContent = product.dosis_penggunaan || '-';
        document.getElementById('modalKeunggulan').textContent = product.keunggulan || '-';
        document.getElementById('modalManfaat').textContent = product.manfaat || '-';
        document.getElementById('modalDescription').textContent = product.deskripsi_produk || 'Tidak ada deskripsi.';

        // Handle image
        const imgEl = document.getElementById('modalImage');
        const noImgEl = document.getElementById('modalNoImage');

        if (product.foto_produk) {
            imgEl.src = '/storage/' + product.foto_produk;
            imgEl.style.display = 'block';
            noImgEl.style.display = 'none';
        } else {
            imgEl.style.display = 'none';
            noImgEl.style.display = 'flex';
        }

        // Show modal
        modal.classList.add('show');
        document.body.style.overflow = 'hidden'; // Prevent scrolling
    }

    function closeProductModal(event) {
        if (event && event.target.id !== 'productModal' && !event.target.classList.contains('product-modal-close')) {
            return;
        }
        const modal = document.getElementById('productModal');
        modal.classList.remove('show');
        document.body.style.overflow = ''; // Restore scrolling

        // Remove id from URL without reloading
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('id')) {
            const newUrl = window.location.pathname;
            window.history.replaceState({}, '', newUrl);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const productId = urlParams.get('id');

        if (productId) {
            // Need a flat map logic, because $pesticides is grouped by type
            const allProducts = {};
            @foreach($pesticides as $group)
                @foreach($group as $item)
                    allProducts[{{ $item->id_produk }}] = @json($item);
                @endforeach
            @endforeach

            if (allProducts[productId]) {
                // Short delay to ensure transition works smoothly
                setTimeout(() => {
                    showProductDetail(allProducts[productId]);
                }, 100);
            }
        }
    });
</script>

@endsection
