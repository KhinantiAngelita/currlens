<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>CurrLens — Deteksi & Konversi Mata Uang</title>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet"/>
  
  @vite(['resources/css/landing.css', 'resources/js/landing.js'])
  
  <script>
    window.APP_CONFIG = {
        predictRoute: "{{ route('predict') }}",
        csrfToken: "{{ csrf_token() }}"
    };
  </script>
</head>
<body>

<!-- NAV -->
<nav>
  <a href="#" class="nav-logo">
    <span class="dot">₿</span> CurrLens
  </a>
  <div class="nav-links">
    <a href="#fitur">Fitur</a>
    <a href="#akurasi">Akurasi</a>
    <a href="#demo">Demo</a>
  </div>
  <button class="nav-cta">Coba Sekarang</button>
</nav>

<!-- HERO -->
<section class="hero">
  <div class="hero-glow"></div>
  <div class="scan-line"></div>

  <!-- IDR - Indonesia -->
  <div class="bill bill-usd">
    <div class="bill-inner">
      <div class="bill-top">
        <span class="bill-flag">🇮🇩</span>
        <span class="bill-country">Indonesia</span>
      </div>
      <div class="bill-amount">Rp100K</div>
      <div class="bill-code">RUPIAH · IDR</div>
    </div>
  </div>

  <!-- SGD - Singapore -->
  <div class="bill bill-eur">
    <div class="bill-inner">
      <div class="bill-top">
        <span class="bill-flag">🇸🇬</span>
        <span class="bill-country">Singapore</span>
      </div>
      <div class="bill-amount">S$50</div>
      <div class="bill-code">SINGAPORE DOLLAR · SGD</div>
    </div>
  </div>

  <!-- MYR - Malaysia -->
  <div class="bill bill-jpy">
    <div class="bill-inner">
      <div class="bill-top">
        <span class="bill-flag">🇲🇾</span>
        <span class="bill-country">Malaysia</span>
      </div>
      <div class="bill-amount">RM50</div>
      <div class="bill-code">RINGGIT · MYR</div>
    </div>
  </div>

  <!-- THB - Thailand -->
  <div class="bill bill-gbp">
    <div class="bill-inner">
      <div class="bill-top">
        <span class="bill-flag">🇹🇭</span>
        <span class="bill-country">Thailand</span>
      </div>
      <div class="bill-amount">฿1000</div>
      <div class="bill-code">THAI BAHT · THB</div>
    </div>
  </div>

  <!-- Floating coins -->
  <div class="coin coin1">🪙</div>
  <div class="coin coin2">💶</div>
  <div class="coin coin3">💷</div>
  <div class="coin coin4">💵</div>

  <!-- Hero content -->
  <div class="hero-content">
    <div class="hero-badge"><span></span> AI Deteksi Mata Uang #1</div>
    <h1>Foto Uangmu,<br>Kami <em>Deteksi & Konversi</em></h1>
    <p>Cukup foto uang kertas dari kamera — CurrLens mengenali mata uang dari seluruh dunia dan langsung mengkonversinya ke mata uang pilihanmu dalam hitungan detik.</p>
    <div class="hero-actions">
      <button class="btn-primary">Coba Gratis Sekarang</button>
      <button class="btn-outline">Lihat Demo →</button>
    </div>

    <div class="hero-ticker">
      <div class="ticker-item">
        <div class="ticker-flag">🇸🇬</div>
        <div class="ticker-info">
          <div class="ticker-code">SGD / IDR</div>
          <div class="ticker-rate" id="rate-SGD">Rp 11.920 <span class="ticker-change up">▲ 0.12%</span></div>
        </div>
      </div>
      <div class="ticker-sep"></div>
      <div class="ticker-item">
        <div class="ticker-flag">🇲🇾</div>
        <div class="ticker-info">
          <div class="ticker-code">MYR / IDR</div>
          <div class="ticker-rate" id="rate-MYR">Rp 3.390 <span class="ticker-change up">▲ 0.07%</span></div>
        </div>
      </div>
      <div class="ticker-sep"></div>
      <div class="ticker-item">
        <div class="ticker-flag">🇹🇭</div>
        <div class="ticker-info">
          <div class="ticker-code">THB / IDR</div>
          <div class="ticker-rate" id="rate-THB">Rp 440 <span class="ticker-change dn">▼ 0.04%</span></div>
        </div>
      </div>
      <div class="ticker-sep"></div>
      <div class="ticker-item">
        <div class="ticker-flag">🇵🇭</div>
        <div class="ticker-info">
          <div class="ticker-code">PHP / IDR</div>
          <div class="ticker-rate" id="rate-PHP">Rp 278 <span class="ticker-change up">▲ 0.09%</span></div>
        </div>
      </div>
      <div class="ticker-sep"></div>
      <div class="ticker-item">
        <div class="ticker-flag">🇻🇳</div>
        <div class="ticker-info">
          <div class="ticker-code">VND / IDR</div>
          <div class="ticker-rate" id="rate-VND">Rp 0.62 <span class="ticker-change dn">▼ 0.02%</span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- STATS -->
<section class="stats-section" id="stats">
  <div class="section-tag">Dipercaya Pengguna Global</div>
  <h2 class="section-title" style="margin:0 auto;">Telah Membantu</h2>
  <p class="section-sub" style="margin:16px auto 0;">Dari traveler, pedagang, hingga money changer — CurrLens adalah teman setia setiap transaksi lintas negara.</p>
  <div class="stats-grid">
    <div class="stat-card reveal">
      <div class="stat-img">🌍</div>
      <div class="stat-num">150+</div>
      <h4>Mata Uang Didukung</h4>
      <p>Dollar, Euro, Yen, Rupiah, hingga mata uang langka dari seluruh penjuru dunia.</p>
    </div>
    <div class="stat-card reveal">
      <div class="stat-img">👥</div>
      <div class="stat-num">50K+</div>
      <h4>Pengguna Aktif</h4>
      <p>Traveler, pedagang, money changer, dan pebisnis internasional menggunakan CurrLens.</p>
    </div>
    <div class="stat-card reveal">
      <div class="stat-img">⚡</div>
      <div class="stat-num">0.5s</div>
      <h4>Rata-rata Deteksi</h4>
      <p>Deteksi dan konversi selesai kurang dari 1 detik. Secepat kamu membalik uangnya.</p>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- FEATURES -->
<section class="features-section" id="fitur">
  <div class="features-header">
    <div class="section-tag">Kemampuan Platform</div>
    <h2 class="section-title">Fitur Kami</h2>
    <p class="section-sub">Teknologi computer vision terdepan dikombinasi data kurs live untuk pengalaman konversi yang sempurna.</p>
  </div>
  <div class="features-grid">
    <div class="feature-card reveal">
      <div class="feat-icon">📸</div>
      <h4>Deteksi Visual AI</h4>
      <div class="feat-img">$ € ¥ £</div>
      <p>Model AI kami dilatih dengan jutaan foto uang kertas dari seluruh dunia. Cukup foto — sisanya kami yang urus.</p>
    </div>
    <div class="feature-card reveal">
      <div class="feat-icon">🔄</div>
      <h4>Konversi Real-time</h4>
      <div class="feat-img" style="font-size:1rem;letter-spacing:3px;">Rp → $ → ¥</div>
      <p>Kurs diperbarui setiap menit dari sumber terpercaya. Konversi ke 150+ mata uang secara instan.</p>
    </div>
    <div class="feature-card reveal">
      <div class="feat-icon">📷</div>
      <h4>Kamera & Upload</h4>
      <div class="feat-img">📱 → 💵</div>
      <p>Gunakan kamera langsung atau upload dari galeri. Mendukung foto buram, pencahayaan rendah, dan sudut miring.</p>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- ACCURACY -->
<section id="akurasi" style="text-align:center;">
  <div class="section-tag">Benchmark & Metrik</div>
  <h2 class="section-title" style="margin:0 auto;">Tingkat Akurasi<br>dan Validasi</h2>
  <p class="section-sub" style="margin:16px auto 0;">Model kami diuji terhadap ribuan sampel uang kertas dari berbagai kondisi dan pencahayaan.</p>
  <div class="accuracy-layout">
    <div class="acc-card reveal">
      <div class="acc-label">Tingkat Akurasi Deteksi</div>
      <div class="acc-img">
        <div style="position:absolute;bottom:12px;left:12px;right:12px;display:flex;align-items:flex-end;gap:5px;height:90px;">
          <div style="flex:1;background:rgba(245,200,66,.2);border-radius:4px 4px 0 0;height:50%;"></div>
          <div style="flex:1;background:rgba(245,200,66,.35);border-radius:4px 4px 0 0;height:68%;"></div>
          <div style="flex:1;background:rgba(245,200,66,.5);border-radius:4px 4px 0 0;height:82%;"></div>
          <div style="flex:1;background:var(--accent);border-radius:4px 4px 0 0;height:97%;"></div>
          <div style="flex:1;background:rgba(245,200,66,.4);border-radius:4px 4px 0 0;height:75%;"></div>
          <div style="flex:1;background:rgba(245,200,66,.25);border-radius:4px 4px 0 0;height:58%;"></div>
        </div>
        <div style="position:absolute;top:12px;right:14px;font-family:'Syne',sans-serif;font-size:1.6rem;font-weight:800;color:var(--accent);">97.1%</div>
      </div>
      <div class="acc-caption" style="color:var(--accent);font-size:1.1rem;font-weight:700;font-family:'Syne',sans-serif;">Mean Detection Accuracy</div>
    </div>
    <div class="acc-card reveal">
      <div class="acc-label">Dataset Pelatihan</div>
      <div class="acc-img">
        <div style="position:absolute;inset:14px;display:grid;grid-template-columns:1fr 1fr 1fr;grid-template-rows:1fr 1fr;gap:6px;">
          <div style="background:rgba(245,200,66,.15);border-radius:5px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;">💵</div>
          <div style="background:rgba(59,130,246,.12);border-radius:5px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;">💶</div>
          <div style="background:rgba(245,200,66,.1);border-radius:5px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;">💴</div>
          <div style="background:rgba(59,130,246,.1);border-radius:5px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;">💷</div>
          <div style="background:rgba(245,200,66,.12);border-radius:5px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;">🪙</div>
          <div style="background:rgba(59,130,246,.15);border-radius:5px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;">💸</div>
        </div>
      </div>
      <div class="acc-caption">2 juta+ gambar uang dari 150 negara</div>
    </div>
    <div class="acc-card reveal">
      <div class="acc-label">Hasil Pengujian</div>
      <div class="acc-img" style="align-items:flex-start;padding:16px;">
        <div style="width:100%">
          <div class="acc-bar-row"><span>Deteksi Nominal</span><span>97.1%</span></div>
          <div class="mini-bar"><div class="mini-fill" style="width:97.1%"></div></div>
          <div class="acc-bar-row"><span>Deteksi Negara Asal</span><span>98.4%</span></div>
          <div class="mini-bar"><div class="mini-fill" style="width:98.4%"></div></div>
          <div class="acc-bar-row"><span>Kondisi Foto Buram</span><span>89.2%</span></div>
          <div class="mini-bar"><div class="mini-fill" style="width:89.2%"></div></div>
          <div class="acc-bar-row"><span>Pencahayaan Rendah</span><span>85.7%</span></div>
          <div class="mini-bar"><div class="mini-fill" style="width:85.7%"></div></div>
        </div>
      </div>
      <div class="acc-caption">Diuji pada 50.000+ sampel independen</div>
    </div>
    <div class="acc-card reveal">
      <div class="acc-label">Standar Referensi & Dataset</div>
      <div class="acc-img" style="height:auto;background:transparent;margin-bottom:0;">
        <div style="display:flex;flex-direction:column;gap:10px;width:100%;padding:4px 0 12px;">

          <!-- Bank Indonesia -->
          <div style="display:flex;align-items:center;gap:12px;background:rgba(245,200,66,.06);border:1px solid rgba(245,200,66,.12);border-radius:12px;padding:12px 16px;">
            <div style="width:38px;height:38px;border-radius:10px;background:rgba(245,200,66,.12);display:grid;place-items:center;font-size:1.1rem;flex-shrink:0;">🏦</div>
            <div style="flex:1;">
              <div style="font-size:.88rem;font-weight:600;color:var(--text);">Bank Indonesia</div>
              <div style="font-size:.72rem;color:var(--muted);margin-top:1px;">Referensi kurs resmi pemerintah</div>
            </div>
            <span class="chip" style="font-size:.65rem;padding:4px 10px;">Live</span>
          </div>

          <!-- Kaggle -->
          <div style="display:flex;align-items:center;gap:12px;background:rgba(32,158,255,.06);border:1px solid rgba(32,158,255,.12);border-radius:12px;padding:12px 16px;">
            <div style="width:38px;height:38px;border-radius:10px;background:rgba(32,158,255,.12);display:grid;place-items:center;flex-shrink:0;">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18" stroke="#20a4f3" stroke-width="1.5" stroke-linecap="round"/></svg>
            </div>
            <div style="flex:1;">
              <div style="font-size:.88rem;font-weight:600;color:var(--text);">Kaggle Dataset</div>
              <div style="font-size:.72rem;color:var(--muted);margin-top:1px;">Dataset uang ASEAN tervalidasi</div>
            </div>
            <span class="chip-blue chip" style="font-size:.65rem;padding:4px 10px;">Dataset</span>
          </div>

          <!-- Roboflow -->
          <div style="display:flex;align-items:center;gap:12px;background:rgba(124,58,237,.06);border:1px solid rgba(124,58,237,.12);border-radius:12px;padding:12px 16px;">
            <div style="width:38px;height:38px;border-radius:10px;background:rgba(124,58,237,.12);display:grid;place-items:center;flex-shrink:0;">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke="#7c3aed" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <div style="flex:1;">
              <div style="font-size:.88rem;font-weight:600;color:var(--text);">Roboflow</div>
              <div style="font-size:.72rem;color:var(--muted);margin-top:1px;">Platform anotasi & training model</div>
            </div>
            <span style="display:inline-flex;align-items:center;background:rgba(124,58,237,.1);border:1px solid rgba(124,58,237,.2);color:#a78bfa;font-size:.65rem;font-weight:600;padding:4px 10px;border-radius:99px;">Vision AI</span>
          </div>

        </div>
      </div>
      <div class="acc-caption" style="margin-top:4px;">Kurs & data diperbarui secara berkala</div>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- DEMO -->
<section class="demo-section" id="demo">
  <div class="section-tag">Coba Langsung</div>
  <h2 class="section-title" style="margin:0 auto;">Coba Sekarang</h2>
  <p class="section-sub" style="margin:16px auto 0;">Upload foto uang kertas kamu dan lihat hasilnya — deteksi + konversi dalam hitungan detik.</p>

  <div class="demo-card reveal">

    <!-- Upload Zone -->
    <div class="demo-upload-container" id="upload-container">
      <div class="demo-upload" id="demo-drop" onclick="document.getElementById('image-input').click()">
        <div id="upload-placeholder">
          <div style="width:64px;height:64px;border-radius:18px;background:linear-gradient(135deg,rgba(245,200,66,.15),rgba(245,200,66,.05));border:1px solid rgba(245,200,66,.2);display:grid;place-items:center;margin: 0 auto 12px;">
            <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="var(--accent)" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
          </div>
          <p id="upload-label">Upload foto uang kertas untuk dideteksi</p>
          <span class="chip" style="font-size:.7rem;">Klik untuk pilih gambar</span>
        </div>
        
        <!-- Preview Image (Hidden by default) -->
        <img id="image-preview" src="" alt="Preview" style="display:none; max-width: 100%; max-height: 300px; border-radius: 12px; margin-bottom: 12px;">
        
        <input type="file" name="image" id="image-input" style="display:none" accept="image/*" onchange="handleFileSelect(event)">
      </div>

      <!-- Action Buttons -->
      <div id="action-buttons" style="display:none; margin-top: 16px; gap: 12px; justify-content: center;">
        <button type="button" id="btn-detect" class="btn-upload-d" style="flex: 2; margin: 0;" onclick="startDetection()">
          🚀 Mulai Deteksi
        </button>
        <button type="button" class="btn-cam" style="flex: 1; margin: 0;" onclick="document.getElementById('image-input').click()">
          🔄 Ganti
        </button>
        <button type="button" class="btn-cam" style="flex: 1; margin: 0; border-color: rgba(239, 68, 68, 0.3); color: #f87171;" onclick="resetDetection()">
          🗑️ Reset
        </button>
      </div>
    </div>

    <!-- Detection Result -->
    <div id="detection-results-container" style="margin-top:20px; display: none;">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
        <span style="font-size:.72rem;color:var(--muted);letter-spacing:.08em;text-transform:uppercase;font-weight:500;">Hasil Deteksi AI</span>
        <span id="detect-badge" class="chip-green chip" style="font-size:.65rem;padding:3px 10px;">✓ <span id="object-count">0</span> Objek</span>
      </div>
      
      <!-- Summary Box -->
      <div style="background:var(--card);border:1px solid rgba(245,200,66,.3);border-radius:12px;padding:20px;margin-bottom:12px;text-align:center;">
        <div style="font-size:.75rem;color:var(--muted);margin-bottom:4px;">TOTAL NOMINAL TERDETEKSI</div>
        <div id="total-nominal-display" style="font-family:'Syne',sans-serif;font-size:2.2rem;font-weight:800;color:var(--accent);line-height:1.2;">
           —
        </div>
        <div id="multi-currency-warning" style="font-size:.7rem;color:#f87171;margin-top:4px;display:none;">
          ⚠️ Multiple currencies detected. Breakdown below.
        </div>
      </div>

      <!-- Detailed List -->
      <div id="detection-list" style="display: flex; flex-direction: column; gap: 8px; max-height: 200px; overflow-y: auto; padding-right: 4px;">
        <!-- Items will be injected here -->
      </div>
    </div>

    <!-- Empty State -->
    <div id="detection-empty-state" style="margin-top:20px;">
       <div style="background:var(--card);border:1px solid rgba(255,255,255,.05);border-radius:12px;padding:20px;display:flex;align-items:center;gap:15px;opacity:0.6;">
          <div style="font-size:1.8rem;">🔍</div>
          <div style="font-size:.9rem;color:var(--muted);">Belum ada foto. Upload gambar uang untuk melihat hasil deteksi.</div>
       </div>
    </div>

    <!-- Converter -->
    <div class="converter-container">
      <div class="converter-row">
        <!-- From -->
        <div class="currency-select-group">
          <label>Dari (Detected)</label>
          <div class="custom-select-wrapper" id="from-select">
            <div class="custom-select-trigger" onclick="toggleSelect('from-select')">
              <span>🇮🇩 IDR — Rupiah</span>
            </div>
            <div class="custom-options">
              <div class="custom-option" data-value="IDR" onclick="selectOption('from-select', 'IDR', '🇮🇩 IDR — Rupiah')"><span class="flag">🇮🇩</span> IDR — Rupiah</div>
              <div class="custom-option" data-value="MYR" onclick="selectOption('from-select', 'MYR', '🇲🇾 MYR — Ringgit')"><span class="flag">🇲🇾</span> MYR — Ringgit</div>
              <div class="custom-option" data-value="SGD" onclick="selectOption('from-select', 'SGD', '🇸🇬 SGD — Singapore Dollar')"><span class="flag">🇸🇬</span> SGD — Singapore Dollar</div>
              <div class="custom-option" data-value="THB" onclick="selectOption('from-select', 'THB', '🇹🇭 THB — Thai Baht')"><span class="flag">🇹🇭</span> THB — Thai Baht</div>
              <div class="custom-option" data-value="PHP" onclick="selectOption('from-select', 'PHP', '🇵🇭 PHP — Philippine Peso')"><span class="flag">🇵🇭</span> PHP — Philippine Peso</div>
            </div>
            <input type="hidden" id="from-cur" value="IDR">
          </div>
        </div>

        <!-- Swap -->
        <button class="swap-button" onclick="swapCurrency()" title="Swap Currencies">
          ⇄
        </button>

        <!-- To -->
        <div class="currency-select-group">
          <label>Ke Mata Uang</label>
          <div class="custom-select-wrapper" id="to-select">
            <div class="custom-select-trigger" onclick="toggleSelect('to-select')">
              <span>🇸🇬 SGD — Singapore Dollar</span>
            </div>
            <div class="custom-options">
              <div class="custom-option" data-value="IDR" onclick="selectOption('to-select', 'IDR', '🇮🇩 IDR — Rupiah')"><span class="flag">🇮🇩</span> IDR — Rupiah</div>
              <div class="custom-option" data-value="MYR" onclick="selectOption('to-select', 'MYR', '🇲🇾 MYR — Ringgit')"><span class="flag">🇲🇾</span> MYR — Ringgit</div>
              <div class="custom-option" data-value="SGD" class="selected" onclick="selectOption('to-select', 'SGD', '🇸🇬 SGD — Singapore Dollar')"><span class="flag">🇸🇬</span> SGD — Singapore Dollar</div>
              <div class="custom-option" data-value="THB" onclick="selectOption('to-select', 'THB', '🇹🇭 THB — Thai Baht')"><span class="flag">🇹🇭</span> THB — Thai Baht</div>
              <div class="custom-option" data-value="PHP" onclick="selectOption('to-select', 'PHP', '🇵🇭 PHP — Philippine Peso')"><span class="flag">🇵🇭</span> PHP — Philippine Peso</div>
            </div>
            <input type="hidden" id="to-cur" value="SGD">
          </div>
        </div>
      </div>

      <!-- Result Display -->
      <div class="result-display">
        <div id="convert-result" class="result-value">—</div>
        <div id="convert-sub" class="result-sub">Upload foto uang untuk melihat hasil konversi</div>
        
        <div id="live-rate-info" style="display: none;">
          <div class="rate-pill">
            <div class="pulse-dot"></div>
            Live: 1 <span id="rate-from-label">...</span> = <span id="rate-val">...</span> <span id="rate-to-label">...</span>
            <span style="opacity: 0.4; margin-left: 4px;">• <span id="rate-time">...</span></span>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats row -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:14px;">
      <div style="background:var(--card);border:1px solid var(--border);border-radius:12px;padding:16px 18px;">
        <div style="font-size:.7rem;color:var(--muted);letter-spacing:.07em;text-transform:uppercase;margin-bottom:6px;">Tingkat Akurasi</div>
        <div style="display:flex;align-items:baseline;gap:4px;">
          <div id="acc-val" style="font-family:'Syne',sans-serif;font-size:1.6rem;font-weight:800;color:var(--accent);">—</div>
        </div>
        <div style="height:3px;background:rgba(255,255,255,.06);border-radius:99px;margin-top:8px;overflow:hidden;">
          <div id="acc-bar" style="height:100%;width:0%;background:linear-gradient(90deg,#f59e0b,var(--accent));border-radius:99px;transition:width 1s ease;"></div>
        </div>
      </div>
      <div style="background:var(--card);border:1px solid var(--border);border-radius:12px;padding:16px 18px;">
        <div style="font-size:.7rem;color:var(--muted);letter-spacing:.07em;text-transform:uppercase;margin-bottom:6px;">Waktu Proses</div>
        <div id="time-val" style="font-family:'Syne',sans-serif;font-size:1.6rem;font-weight:800;color:var(--accent2);">—</div>
        <div style="font-size:.72rem;color:var(--muted);margin-top:6px;">Real-time inference</div>
      </div>
    </div>

    <!-- Upload button -->
    <button class="btn-upload-d" style="width:100%;margin-top:14px;padding:15px;" onclick="document.getElementById('demo-drop').click()">
      <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
      Upload Foto Uang
    </button>

  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="footer-grid">
    <div class="footer-brand">
      <div class="footer-logo"><span class="dot">₿</span> CurrLens</div>
      <p>Deteksi dan konversi mata uang dari foto — cepat, akurat, dan selalu update dengan kurs terkini.</p>
    </div>
    <div class="footer-col">
      <h5>Produk</h5>
      <a href="#">Fitur</a><a href="#">Mata Uang</a><a href="#">API</a><a href="#">Dokumentasi</a>
    </div>
    <div class="footer-col">
      <h5>Perusahaan</h5>
      <a href="#">Tentang Kami</a><a href="#">Blog</a><a href="#">Karir</a><a href="#">Kontak</a>
    </div>
  </div>
  <div class="footer-bottom">
    <p>© 2025 CurrLens. All rights reserved.</p>
    <div><a href="#">Terms &amp; Conditions</a><a href="#">Privacy Policy</a></div>
  </div>
</footer>
</body>
</html>
