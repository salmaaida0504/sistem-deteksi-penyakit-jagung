@extends('layouts.app')

@section('title', 'BHUMI — Sistem Deteksi Penyakit Tanaman Jagung')

@section('content')

{{-- ==================== HERO SECTION ==================== --}}
<section class="hero" id="hero">
    <div class="hero-bg" style="background-image: url('{{ asset('images/LAHAN JAGUNG.jpg') }}');"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1 data-aos="fade-down" data-aos-delay="400">Smart Vision</h1>
        <h1 data-aos="fade-down" data-aos-delay="400">for Smart Farming</h1>
        <p class="hero-sub" data-aos="fade-down" data-aos-delay="600">Empowering Smart Agriculture Through Image Processing and Deep Learning to Detect Corn Diseases Accurately and </br>Enhance Agricultural Knowledge.</p>
        <a href="#deteksi" class="btn-accent" data-aos="fade-up" data-aos-delay="800">
            <div class="svg-wrapper">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg>
            </div>
            <span>Mulai Deteksi</span>
        </a>
    </div>
</section>

{{-- ==================== ABOUT SECTION ==================== --}}
<section class="about-section" id="about">
    <div class="container">
        <div class="about-grid">
            {{-- Left: Image --}}
            <div class="about-image" data-aos="fade-right" data-aos-delay="200">
                <img src="{{ asset('images/JAGUNG ABOUT SECTION.jpg') }}" alt="Jagung About Section">
            </div>

            {{-- Right: Content --}}
            <div class="about-content">
                <div class="about-intro" data-aos="fade-down" data-aos-delay="200" style="padding-right: 40px;">
                    <h3 class="about-intro-sub">Jagung Lebih Sehat, <br>Panen Melimpah!</h3>
                    <p style="text-align: justify; margin-top: 16px; margin-bottom: 16px; color: #555; line-height: 1.8;">
                        Penyakit daun menjadi salah satu ancaman utama untuk produktivitas tanaman jagung. Website <strong>BHUMI</strong> hadir sebagai solusi
                        deteksi berbasis kecerdasan buatan yang dapat membantu mengidentifikasi penyakit daun jagung secara cepat dan akurat, hanya dengan
                        mengunggah foto daun. Selain itu, Dilengkapi konten edukatif seputar gejala, penyebab, dan penanganan yang dapat diakses kapan saja.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ==================== FEATURES SECTION ==================== --}}
<section class="features-section" id="features" style="padding: 20px 0 100px; background: var(--bg);">
    <div class="container">
        <div class="features-header">
            <h2 class="features-title" data-aos="fade-down" data-aos-delay="300">
                Kemampuan Sistem
            </h2>
        </div>
        <div class="about-cards">
            <div class="about-card" data-aos="fade-up" data-aos-delay="200">
                <div class="about-card-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                </div>
                <h3>Deteksi Penyakit</h3>
                <p>
                    Unggah foto daun jagung dan sistem akan otomatis mendeteksi jenis penyakit pada tanaman jagung berdasarkan gambar yang diunggah,
                    lengkap dengan persentase kecurigaan terhadap masing-masing kategori penyakit.
                </p>
            </div>
            <div class="about-card" data-aos="fade-up" data-aos-delay="400">
                <div class="about-card-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </div>
                <h3>Informasi Deteksi</h3>
                <p>
                    Ketahui penjelasan seputar gejala, penyebab, dan langkah penanganan untuk setiap penyakit berdasarkan hasil deteksi.
                </p>
            </div>
            <div class="about-card" data-aos="fade-up" data-aos-delay="600">
                <div class="about-card-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z"/><path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"/></svg>
                </div>
                <h3>Rekomendasi Pestisida</h3>
                <p>
                    Sistem akan menampilkan rekomendasi produk pestisida yang dapat digunakan sebagai penanganan awal untuk tanaman jagung yang terserang penyakit.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- ==================== HOW TO USE SECTION ==================== --}}
<section class="howto-section" id="howto">
    <div class="container">
        {{-- Steps --}}
        <div class="howto-block" data-aos="fade-down" data-aos-delay="200">
            <div class="features-header">
                <h2 class="features-title" data-aos="fade-down" data-aos-delay="300" style=" margin-bottom: 20px;">
                    Cara Menggunakan Fitur Deteksi Penyakit
                </h2>
            </div>
            <p class="howto-block-sub"></p>
            <div class="steps-grid">
                <div class="step-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="step-number">1</div>
                    <h4>Upload Foto</h4>
                    <p>
                        Pilih atau drag foto daun jagung ke area upload yang tersedia. Pastikan daun terlihat jelas dan pencahayaan cukup <br>
                        untuk hasil terbaik.
                    </p>
                </div>
                <div class="step-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="step-number">2</div>
                    <h4>Proses Analisis</h4>
                    <p>Tunggu beberapa saat agar sistem dapat menganalisis foto yang diunggah.</p>
                </div>
                <div class="step-card" data-aos="fade-up" data-aos-delay="600">
                    <div class="step-number">3</div>
                    <h4>Lihat Hasil Deteksi</h4>
                    <p>
                        Dapatkan hasil berupa informasi jenis penyakit yang terdeteksi, tingkat keyakinan sistem,
                        informasi penyakit, dan langkah penanganan awal yang dapat dilakukan.
                    </p>
                </div>
            </div>
        </div>

        {{-- Tips --}}
        <div class="howto-block" data-aos="fade-down" data-aos-delay="200">
            <p class="howto-block-sub">Pastikan gambar yang digunakan memenuhi kriteria berikut.</p>
            <div class="tips-grid">
                <div class="tip-card good" data-aos="fade-up" data-aos-delay="200">
                    <div class="tip-card-inner">
                        <div class="tip-card-front" style="border: 1px solid #13b333;">
                            <h4>Kriteria Kualitas Foto yang Baik</h4>
                            <ul class="tip-list">
                                <li><span class="icon-check">✓</span> Fokus pada satu daun</li>
                                <li><span class="icon-check">✓</span> Pencahayaan cukup</li>
                                <li><span class="icon-check">✓</span> Background bersih</li>
                                <li><span class="icon-check">✓</span> Gejala penyakit terlihat jelas</li>
                            </ul>
                        </div>
                        <div class="tip-card-back">
                            <img src="{{ asset('images/FOTO JAGUNG BAGUS.png') }}" alt="Contoh Foto Baik">
                        </div>
                    </div>
                </div>
                <div class="tip-card bad" data-aos="fade-up" data-aos-delay="400">
                    <div class="tip-card-inner">
                        <div class="tip-card-front" style="border: 1px solid #e61818;">
                            <h4>Hindari Gambar dengan Kriteria Berikut</h4>
                            <ul class="tip-list">
                                <li><span class="icon-cross">✗</span> Banyak daun sekaligus</li>
                                <li><span class="icon-cross">✗</span> Terlalu gelap/silau</li>
                                <li><span class="icon-cross">✗</span> Gambar terlalu jauh</li>
                                <li><span class="icon-cross">✗</span> Gambar blur</li>
                            </ul>
                        </div>
                        <div class="tip-card-back">
                            <img src="{{ asset('images/FOTO JAGUNG JELEK.jpg') }}" alt="Contoh Foto Buruk">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ==================== UPLOAD SECTION ==================== --}}
<section class="upload-section" id="deteksi">
    <div class="container">
        <div class="features-header">
            <h2 class="features-title" data-aos="fade-down" data-aos-delay="300" style=" margin-bottom: 20px;">
                Deteksi Penyakit Tanaman Jagung
            </h2>
        </div>
        <p class="howto-block-sub" data-aos="fade-down" data-aos-delay="100"></p>

        <div class="upload-wrapper" data-aos="fade-up" data-aos-delay="200">
            {{-- Upload Zone --}}
            <div class="upload-zone" id="uploadZone">
                <div class="upload-zone-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#379777" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17,8 12,3 7,8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                </div>
                <h3>Upload foto daun jagung</h3>
                <p class="upload-zone-sub">Format foto : JPG, PNG | Size : Maksimal 5MB</p>
                <label class="btn-choose" for="fileInput">Pilih berkas</label>
                <span class="upload-zone-or">atau drag & drop file ke sini</span>
                <input type="file" id="fileInput" accept=".jpg,.jpeg,.png" hidden>
            </div>

            {{-- Error Message --}}
            <p class="upload-error" id="uploadError"></p>

            {{-- Preview --}}
            <div class="upload-preview" id="uploadPreview">
                <div class="preview-header">
                    <img src="" alt="Preview" class="preview-thumb" id="previewThumb">
                    <div class="preview-info">
                        <h4 id="previewName">filename.jpg</h4>
                        <span id="previewSize">0 KB</span>
                    </div>
                </div>

                <div class="preview-badges">
                    <span class="preview-badge valid">✓ Format valid</span>
                    <span class="preview-badge valid">✓ Ukuran aman</span>
                </div>

                <button class="btn-change" id="changeFileBtn" type="button">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1,4 1,10 7,10"/><path d="M3.51 15a9 9 0 102.13-9.36L1 10"/></svg>
                    Ganti gambar
                </button>

                <div class="preview-info-box">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#379777" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    Pastikan daun terlihat jelas dan pencahayaan cukup untuk hasil deteksi yang optimal.
                </div>

                <div class="preview-actions">
                    <button type="button" class="btn-outline" id="resetBtn">Deteksi Ulang</button>
                    <button type="button" class="btn-primary" id="detectBtn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        Mulai Deteksi
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ==================== LOADING MODAL ==================== --}}
<div class="loading-modal" id="loadingModal">
    <div class="loading-card">
        <img src="" alt="Preview" class="loading-thumb" id="loadingThumb">
        <div class="loading-spinner" id="loadingSpinner"></div>

        <div class="loading-steps">
            <div class="loading-step" id="step1">
                <span class="step-icon">✓</span>
                <span>Preprocessing gambar selesai</span>
            </div>
            <div class="loading-step" id="step2">
                <span class="step-icon">✓</span>
                <span>Ekstraksi fitur selesai</span>
            </div>
            <div class="loading-step" id="step3">
                <span class="step-icon">●</span>
                <span>Klasifikasi penyakit...</span>
            </div>
        </div>

        <div class="loading-progress">
            <div class="loading-progress-bar" id="progressBar"></div>
        </div>
        <p class="loading-text">Sistem sedang melakukan proses deteksi untuk penyakit daun jagungmu...</p>
    </div>
</div>

{{-- ==================== RESULT SECTION ==================== --}}
<section class="result-section" id="resultSection">
    <div class="container">
        <div class="upload-wrapper">
            <div class="result-header">
                <h2>Hasil Deteksi Penyakit</h2>
            </div>
            <div class="result-card">
                <div class="result-top-section" style="display: flex; gap: 24px; align-items: flex-start; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap;">
                    <div class="result-text-info" style="flex: 1; min-width: 250px;">
                        <div class="result-badge" id="resultBadge">Terdeteksi penyakit</div>
                        <h2 class="result-disease-name" id="resultName">-</h2>
                        <p class="result-disease-latin" id="resultLatin">-</p>
                    </div>
                    <div class="result-image-info" style="flex-shrink: 0;">
                        <img id="resultImage" src="" alt="Uploaded image" style="width: 140px; height: 140px; object-fit: cover; border-radius: var(--radius-sm); border: 2px solid rgba(55, 151, 119, 0.2); display: none;">
                    </div>
                </div>

                <div class="prob-list" id="probList">
                    {{-- Filled by JS --}}
                </div>

                <div class="result-info-card">
                    <h4>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#379777" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                        Deskripsi Penyakit
                    </h4>
                    <p id="resultDescription">-</p>
                </div>

                {{-- Pesticide Recommendations --}}
                <div class="pesticide-result-section" id="pesticideRecommendations" style="display: none;">
                    <h4 class="pesticide-result-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#379777" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>
                        Rekomendasi Pestisida
                    </h4>
                    <div class="pesticide-result-list" id="pesticideList">
                        {{-- Filled by JS --}}
                    </div>
                </div>

                <a href="/edukasi" class="result-more-btn" id="resultMoreBtn">
                    Selengkapnya di halaman edukasi
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
