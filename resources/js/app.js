/**
 * BHUMI — User-Facing JavaScript
 * Handles: navbar, smooth scroll, drag & drop, file validation,
 * image preview, detection AJAX, loading modal, result display
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================
    // NAVBAR — Sticky scroll effect & hamburger
    // ============================================
    const navbar = document.getElementById('navbar');
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const navbarMenu = document.getElementById('navbarMenu');

    if (navbar) {
        const heroSection = document.getElementById('hero');
        if (heroSection) {
            window.addEventListener('scroll', () => {
                const heroBottom = heroSection.offsetTop + heroSection.offsetHeight;
                navbar.classList.toggle('scrolled', window.scrollY > heroBottom - 80);
            });
            // Trigger on load to set initial state
            window.dispatchEvent(new Event('scroll'));
        } else {
            // No hero section — always show dark rounded navbar
            navbar.classList.add('scrolled');
        }
    }

    if (hamburgerBtn && navbarMenu) {
        hamburgerBtn.addEventListener('click', () => {
            hamburgerBtn.classList.toggle('active');
            navbarMenu.classList.toggle('open');
        });

        // Close menu when clicking a link
        navbarMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                hamburgerBtn.classList.remove('active');
                navbarMenu.classList.remove('open');
            });
        });
    }

    // ============================================
    // SMOOTH SCROLL — CTA buttons
    // ============================================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                const offset = 80;
                const top = target.getBoundingClientRect().top + window.pageYOffset - offset;
                window.scrollTo({ top, behavior: 'smooth' });
            }
        });
    });

    // ============================================
    // UPLOAD — File handling
    // ============================================
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('fileInput');
    const uploadError = document.getElementById('uploadError');
    const uploadPreview = document.getElementById('uploadPreview');
    const previewThumb = document.getElementById('previewThumb');
    const previewName = document.getElementById('previewName');
    const previewSize = document.getElementById('previewSize');
    const changeFileBtn = document.getElementById('changeFileBtn');
    const resetBtn = document.getElementById('resetBtn');
    const detectBtn = document.getElementById('detectBtn');

    let selectedFile = null;

    if (!uploadZone) return; // Not on homepage

    // Click to upload
    uploadZone.addEventListener('click', (e) => {
        if (e.target.tagName !== 'LABEL') {
            fileInput.click();
        }
    });

    // Drag & Drop
    ['dragenter', 'dragover'].forEach(evt => {
        uploadZone.addEventListener(evt, (e) => {
            e.preventDefault();
            e.stopPropagation();
            uploadZone.classList.add('dragover');
        });
    });

    ['dragleave', 'drop'].forEach(evt => {
        uploadZone.addEventListener(evt, (e) => {
            e.preventDefault();
            e.stopPropagation();
            uploadZone.classList.remove('dragover');
        });
    });

    uploadZone.addEventListener('drop', (e) => {
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFile(files[0]);
        }
    });

    // File input change
    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            handleFile(fileInput.files[0]);
        }
    });

    // Validate & preview
    function handleFile(file) {
        // Reset error
        uploadError.classList.remove('show');
        uploadError.textContent = '';

        // Validate format
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!validTypes.includes(file.type)) {
            showError('Format file tidak didukung. Gunakan format JPG atau PNG.');
            return;
        }

        // Validate size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            showError('Ukuran file terlalu besar. Format file maksimal 5MB.');
            return;
        }

        selectedFile = file;
        showPreview(file);
    }

    function showError(msg) {
        uploadError.textContent = msg;
        uploadError.classList.add('show');
    }

    function showPreview(file) {
        // Thumbnail
        const reader = new FileReader();
        reader.onload = (e) => {
            previewThumb.src = e.target.result;
        };
        reader.readAsDataURL(file);

        // Info
        previewName.textContent = file.name;
        previewSize.textContent = formatFileSize(file.size);

        // Show preview, hide upload zone
        uploadZone.style.display = 'none';
        uploadPreview.classList.add('show');

        // Hide result section if a new file is previewed
        const resultSection = document.getElementById('resultSection');
        if (resultSection) resultSection.classList.remove('show');
    }

    function formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }

    // Change file button
    if (changeFileBtn) {
        changeFileBtn.addEventListener('click', () => {
            fileInput.click();
        });
    }

    // Reset button
    if (resetBtn) {
        resetBtn.addEventListener('click', resetUpload);
    }

    function resetUpload() {
        selectedFile = null;
        fileInput.value = '';
        previewThumb.src = '';
        uploadPreview.classList.remove('show');
        uploadZone.style.display = '';
        uploadError.classList.remove('show');

        // Also hide result section
        const resultSection = document.getElementById('resultSection');
        if (resultSection) resultSection.classList.remove('show');
    }

    // ============================================
    // DETECTION — Submit & loading animation
    // ============================================
    if (detectBtn) {
        detectBtn.addEventListener('click', startDetection);
    }

    function startDetection() {
        if (!selectedFile) return;

        // Show loading modal
        showLoadingModal();

        // Prepare form data
        const formData = new FormData();
        formData.append('image', selectedFile);

        // Set loading thumb
        const loadingThumb = document.getElementById('loadingThumb');
        if (loadingThumb && previewThumb.src) {
            loadingThumb.src = previewThumb.src;
        }

        // Animate loading steps
        animateLoadingSteps();

        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Send AJAX request
        fetch('/deteksi', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: formData,
        })
        .then(response => {
            return response.json().then(data => ({ status: response.status, data }));
        })
        .then(({ status, data }) => {
            console.log('[BHUMI] Response status:', status, '| Data:', data);
            // Wait for animation to finish, then show result
            setTimeout(() => {
                hideLoadingModal();
                if (data.success) {
                    displayResult(data);
                } else if (status === 422 && data.errors) {
                    // Validation error from Laravel
                    const messages = Object.values(data.errors).flat().join(' ');
                    showError('Validasi gagal: ' + messages);
                } else if (data.message) {
                    showError('Error: ' + data.message);
                } else {
                    showError('Terjadi kesalahan saat memproses deteksi. (Status: ' + status + ')');
                }
            }, 3500);
        })
        .catch(error => {
            console.error('[BHUMI] Detection error:', error);
            setTimeout(() => {
                hideLoadingModal();
                showError('Terjadi kesalahan jaringan. Coba lagi.');
            }, 1500);
        });
    }

    // ============================================
    // LOADING MODAL
    // ============================================
    const loadingModal = document.getElementById('loadingModal');

    function showLoadingModal() {
        if (loadingModal) loadingModal.classList.add('show');
    }

    function hideLoadingModal() {
        if (loadingModal) loadingModal.classList.remove('show');
    }

    function animateLoadingSteps() {
        const step1 = document.getElementById('step1');
        const step2 = document.getElementById('step2');
        const step3 = document.getElementById('step3');
        const progressBar = document.getElementById('progressBar');

        // Reset all steps
        [step1, step2, step3].forEach(s => {
            if (s) {
                s.classList.remove('done', 'active');
            }
        });
        if (progressBar) progressBar.style.width = '0%';

        // Step 1
        setTimeout(() => {
            if (step1) { step1.classList.add('active'); }
            if (progressBar) progressBar.style.width = '20%';
        }, 300);

        setTimeout(() => {
            if (step1) { step1.classList.remove('active'); step1.classList.add('done'); }
            if (progressBar) progressBar.style.width = '40%';
        }, 1000);

        // Step 2
        setTimeout(() => {
            if (step2) { step2.classList.add('active'); }
            if (progressBar) progressBar.style.width = '55%';
        }, 1100);

        setTimeout(() => {
            if (step2) { step2.classList.remove('active'); step2.classList.add('done'); }
            if (progressBar) progressBar.style.width = '70%';
        }, 2000);

        // Step 3
        setTimeout(() => {
            if (step3) { step3.classList.add('active'); }
            if (progressBar) progressBar.style.width = '85%';
        }, 2100);

        setTimeout(() => {
            if (step3) { step3.classList.remove('active'); step3.classList.add('done'); }
            if (progressBar) progressBar.style.width = '100%';
        }, 3200);
    }

    // ============================================
    // RESULT DISPLAY
    // ============================================
    const colorMap = {
        'Cercospora': '#FF9B9B',
        'Common_Rust': '#FFD6A5',
        'Northern_Leaf_Blight': '#FFFEC4',
        'Healthy': '#CBFFA9'
    };

    const strikingColorMap = {
        'Cercospora': '#ef4444',
        'Common_Rust': '#f97316',
        'Northern_Leaf_Blight': '#eab308',
        'Healthy': '#22c55e'
    };

    function displayResult(data) {
        const resultSection = document.getElementById('resultSection');
        if (!resultSection) return;

        const detection = data.detection;
        const disease = data.opt;

        // Badge
        const resultBadge = document.getElementById('resultBadge');
        if (resultBadge) {
            if (detection.predicted_class === 'Healthy') {
                resultBadge.textContent = 'Tanaman terdeteksi sehat';
                resultBadge.style.backgroundColor = colorMap['Healthy'];
                resultBadge.style.color = '#14532d';
            } else {
                resultBadge.textContent = 'Terdeteksi penyakit';
                resultBadge.style.backgroundColor = colorMap[detection.predicted_class] || '#FF9B9B';
                resultBadge.style.color = '#7f1d1d';
            }
        }

        // Uploaded Image
        const resultImage = document.getElementById('resultImage');
        if (resultImage && data.image_url) {
            resultImage.src = data.image_url;
            resultImage.style.display = 'block';
        }

        // Name & Latin
        const resultName = document.getElementById('resultName');
        const resultLatin = document.getElementById('resultLatin');
        if (resultName && disease) resultName.textContent = disease.nama_opt;
        if (resultLatin && disease && disease.jenis_opt) {
            resultLatin.textContent = disease.jenis_opt.charAt(0).toUpperCase() + disease.jenis_opt.slice(1);
        }

        // Description
        const resultDescription = document.getElementById('resultDescription');
        if (resultDescription && disease) {
            resultDescription.textContent = disease.deskripsi;
        }

        // Probability bars
        const probList = document.getElementById('probList');
        if (probList && detection.all_predictions) {
            probList.innerHTML = '';

            // Sort predictions by value descending
            const sorted = Object.entries(detection.all_predictions)
                .sort((a, b) => b[1] - a[1]);

            sorted.forEach(([cls, prob]) => {
                const color = strikingColorMap[cls] || '#9ca3af';
                const displayName = cls.replace(/_/g, ' ');
                const borderStyle = cls === 'Northern_Leaf_Blight' ? 'border: 1px solid #e5e7eb;' : '';

                const item = document.createElement('div');
                item.className = 'prob-item';
                item.innerHTML = `
                    <span class="prob-label">${displayName}</span>
                    <div class="prob-bar-wrap">
                        <div class="prob-bar" style="width: 0%; background-color: ${color}; ${borderStyle}"></div>
                    </div>
                    <span class="prob-value">${parseFloat(prob).toFixed(2)}%</span>
                `;
                probList.appendChild(item);

                // Animate bar width
                setTimeout(() => {
                    item.querySelector('.prob-bar').style.width = prob + '%';
                }, 100);
            });
        }

        // More button link
        const resultMoreBtn = document.getElementById('resultMoreBtn');
        if (resultMoreBtn && disease) {
            resultMoreBtn.href = '/edukasi?open=' + disease.id_opt;
        }

        // Pesticide Recommendations
        const pesticideSection = document.getElementById('pesticideRecommendations');
        const pesticideList = document.getElementById('pesticideList');

        if (pesticideSection && pesticideList) {
            pesticideList.innerHTML = '';

            const isDisease = disease && disease.jenis_opt && disease.jenis_opt.toLowerCase() === 'penyakit';

            if (disease && disease.produk && disease.produk.length > 0 && isDisease) {
                // Show pesticide recommendations
                disease.produk.forEach(pesticide => {
                    const card = document.createElement('div');
                    card.className = 'pesticide-result-card';
                    const jenisName = (pesticide.jenis_produk && pesticide.jenis_produk.nama_jenis) || (pesticide.jenisProduk && pesticide.jenisProduk.nama_jenis) || 'Produk';
                    card.innerHTML = `
                        <div class="pesticide-card-header">
                            <div class="pesticide-card-title">${pesticide.nama_produk}</div>
                            <span class="pesticide-type-badge">${jenisName}</span>
                        </div>
                        <div class="pesticide-card-details">
                            <div class="pesticide-detail-item">
                                <span class="pesticide-detail-label">Manfaat/Keunggulan</span>
                                <span class="pesticide-detail-value">${pesticide.keunggulan || pesticide.manfaat || '-'}</span>
                            </div>
                            ${pesticide.dosis_penggunaan ? `
                            <div class="pesticide-detail-item">
                                <span class="pesticide-detail-label">Dosis Penggunaan</span>
                                <span class="pesticide-detail-value">${pesticide.dosis_penggunaan}</span>
                            </div>` : ''}
                        </div>
                        <div style="margin-top: 16px; text-align: right;">
                            <a href="/produk?id=${pesticide.id_produk}" class="btn-primary" style="display: inline-block; text-decoration: none; padding: 8px 16px; font-size: 0.85rem; background: var(--primary);">
                                Deskripsi Produk
                            </a>
                        </div>
                    `;
                    pesticideList.appendChild(card);
                });
                pesticideSection.style.display = 'block';
            } else if (disease && !isDisease) {
                // Healthy plant - show positive message
                pesticideList.innerHTML = `
                    <div class="pesticide-healthy-message">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22,4 12,14.01 9,11.01"/></svg>
                        <p>Tanaman jagung Anda terlihat sehat! Tidak perlu penanganan pestisida. Tetap jaga kebersihan lahan dan lakukan monitoring rutin.</p>
                    </div>
                `;
                pesticideSection.style.display = 'block';
            } else {
                pesticideSection.style.display = 'none';
            }
        }

        // Show result section
        resultSection.classList.add('show');

        // Scroll to result
        setTimeout(() => {
            resultSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 200);
    }
});
