/**
 * CurrLens — Landing Page Logic
 * Refactored: 2026-04-29
 */

document.addEventListener('DOMContentLoaded', () => {
    // Reveal animations on scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(e => { if(e.isIntersecting) e.target.classList.add('visible'); });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

    // Global click listener for custom selects
    window.addEventListener('click', (e) => {
        if (!e.target.closest('.custom-select-wrapper')) {
            document.querySelectorAll('.custom-select-wrapper').forEach(s => s.classList.remove('open'));
        }
    });

    // Drag and Drop support
    const dropZone = document.getElementById('demo-drop');
    if (dropZone) {
        dropZone.addEventListener('dragover', e => {
            e.preventDefault();
            dropZone.style.borderColor = 'var(--accent)';
        });
        dropZone.addEventListener('dragleave', () => {
            dropZone.style.borderColor = '';
        });
        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.style.borderColor = '';
            if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                const event = { target: { files: e.dataTransfer.files } };
                handleFileSelect(event);
            }
        });
    }

    // Load real-time ticker rates
    loadTickerRates();
});

async function loadTickerRates() {
    try {
        const response = await fetch('/api/rates?base=IDR&symbols=SGD,MYR,THB,PHP,VND');
        const data = await response.json();

        if (data.error || !data.quotes) return;

        // API returns quotes as { "USDIDR": 1.0, "USDSGD": 1.35, ... } 
        // if base is IDR, it might return { "IDRSGD": 0.00008, ... }
        // We want the inverse (SGD/IDR) which is 1 / quote
        
        const symbols = ['SGD', 'MYR', 'THB', 'PHP', 'VND'];
        symbols.forEach(symbol => {
            const el = document.getElementById(`rate-${symbol}`);
            if (!el) return;

            // The exchangerate.host /live endpoint with access_key often uses USD as source 
            // even if we specify source=IDR (depending on the plan).
            // Let's check how the data is structured.
            let rate;
            const quoteKey = `IDR${symbol}`;
            if (data.quotes[quoteKey]) {
                rate = 1 / data.quotes[quoteKey];
            } else {
                // Fallback for USD-based quotes if IDR-based ones aren't available
                const usdToIdr = data.quotes['USDIDR'];
                const usdToSym = data.quotes[`USD${symbol}`];
                if (usdToIdr && usdToSym) {
                    rate = usdToIdr / usdToSym;
                }
            }

            if (rate) {
                // Update the text, keeping the span if possible or just replacing the whole content
                const changeHtml = el.querySelector('.ticker-change')?.outerHTML || '';
                el.innerHTML = `Rp ${formatNumber(rate)} ${changeHtml}`;
            }
        });
    } catch (e) {
        console.error("Failed to load ticker rates", e);
    }
}

// --- Custom Select Logic ---
window.toggleSelect = function(id) {
    const el = document.getElementById(id);
    const isOpen = el.classList.contains('open');
    document.querySelectorAll('.custom-select-wrapper').forEach(s => s.classList.remove('open'));
    if (!isOpen) el.classList.add('open');
}

window.selectOption = function(id, value, label) {
    const el = document.getElementById(id);
    const trigger = el.querySelector('.custom-select-trigger span');
    const hiddenInput = el.querySelector('input[type="hidden"]');
    
    trigger.innerHTML = label;
    hiddenInput.value = value;
    
    el.querySelectorAll('.custom-option').forEach(opt => {
        opt.classList.remove('selected');
        if (opt.getAttribute('data-value') === value) opt.classList.add('selected');
    });

    el.classList.remove('open');
    doConvert();
}

const symMap = {IDR:'Rp ', MYR:'RM ', SGD:'S$ ', THB:'฿', PHP:'₱ '};
const currencyMap = {
    'Rupiah': 'IDR',
    'Ringgit': 'MYR',
    'SGD': 'SGD',
    'Baht': 'THB',
    'Peso': 'PHP'
};

function formatNumber(n) {
    if(n>=1000) return n.toLocaleString('id-ID');
    if(n<0.01)  return n.toFixed(6);
    if(n<1)     return n.toFixed(4);
    return n.toLocaleString('id-ID',{maximumFractionDigits:2});
}

let detectedCurrency = null;

window.doConvert = async function() {
    if (!detectedCurrency) return;

    const from = document.getElementById('from-cur').value;
    const to = document.getElementById('to-cur').value;
    const amount = detectedCurrency.nominal;

    const resultEl = document.getElementById('convert-result');
    const subEl = document.getElementById('convert-sub');
    const infoEl = document.getElementById('live-rate-info');
    const rateValEl = document.getElementById('rate-val');
    const rateTimeEl = document.getElementById('rate-time');
    const rateFromLabel = document.getElementById('rate-from-label');
    const rateToLabel = document.getElementById('rate-to-label');

    resultEl.textContent = '⏳ ...';
    subEl.textContent = 'Mengambil kurs live...';
    infoEl.style.display = 'none';

    try {
        const response = await fetch(`/api/convert?from=${from}&to=${to}&amount=${amount}`);
        const data = await response.json();

        if (data.error) throw new Error(data.error);

        const sym = symMap[to] || '';
        const convertedAmount = data.result;
        const rate = data.rate || (convertedAmount / amount);
        const source = data.source || 'api';
        const time = new Date().toLocaleTimeString();

        resultEl.textContent = sym + formatNumber(convertedAmount);
        subEl.textContent = `${formatNumber(amount)} ${from} ≈`;
        
        rateValEl.textContent = formatNumber(rate);
        rateFromLabel.textContent = from;
        rateToLabel.textContent = to;
        rateTimeEl.textContent = (source === 'static_fallback' ? 'Offline Rate' : 'Live Rate');
        infoEl.style.display = 'block';

        // Update ticker style if offline
        const pill = infoEl.querySelector('.rate-pill');
        if (source === 'static_fallback') {
            pill.style.background = 'rgba(239, 68, 68, 0.1)';
            pill.style.borderColor = 'rgba(239, 68, 68, 0.2)';
            pill.querySelector('.pulse-dot').style.background = '#f87171';
            pill.querySelector('.pulse-dot').style.boxShadow = 'none';
        } else {
            pill.style.background = '';
            pill.style.borderColor = '';
            pill.querySelector('.pulse-dot').style.background = '';
            pill.querySelector('.pulse-dot').style.boxShadow = '';
        }

    } catch (error) {
        console.error('Conversion failed:', error);
        resultEl.textContent = '⚠️ Offline';
        subEl.textContent = 'Gagal mengambil kurs live.';
    }
}

window.syncCustomSelect = function(selectId, value) {
    const el = document.getElementById(selectId);
    if (!el) return;
    const options = el.querySelectorAll('.custom-option');
    options.forEach(opt => {
        if (opt.getAttribute('data-value') === value) {
            const label = opt.innerHTML;
            const trigger = el.querySelector('.custom-select-trigger span');
            trigger.innerHTML = label;
            opt.classList.add('selected');
            el.querySelector('input[type="hidden"]').value = value;
        } else {
            opt.classList.remove('selected');
        }
    });
}

window.swapCurrency = function() {
    const f = document.getElementById('from-cur'), t = document.getElementById('to-cur');
    const valF = f.value, valT = t.value;
    syncCustomSelect('from-select', valT);
    syncCustomSelect('to-select', valF);
    doConvert();
}

// --- Detection Logic ---
let selectedFile = null;

window.handleFileSelect = function(event) {
    const file = event.target.files[0];
    if (!file) return;
    selectedFile = file;
    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.getElementById('image-preview');
        preview.src = e.target.result;
        preview.style.display = 'block';
        document.getElementById('upload-placeholder').style.display = 'none';
        document.getElementById('action-buttons').style.display = 'flex';
    }
    reader.readAsDataURL(file);
}

window.startDetection = async function() {
    if (!selectedFile) return;

    const btn = document.getElementById('btn-detect');
    btn.disabled = true;
    btn.innerHTML = '⏳ Sedang Mendeteksi...';

    // UI Reset
    document.getElementById('detection-results-container').style.display = 'none';
    document.getElementById('detection-empty-state').style.display = 'block';
    document.getElementById('acc-val').textContent = '...';
    document.getElementById('time-val').textContent = '...';
    document.getElementById('acc-bar').style.width = '0%';

    const formData = new FormData();
    formData.append('image', selectedFile);
    
    // 🛠️ DEBUG LOGS
    console.log("📤 STARTING DETECTION...");
    console.log("📂 File Name:", selectedFile.name);
    console.log("📏 File Size:", (selectedFile.size / 1024).toFixed(2), "KB");
    console.log("🧪 FormData Entry:", formData.get('image'));

    const startTime = Date.now();

    try {
        const response = await fetch(window.APP_CONFIG.predictRoute, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': window.APP_CONFIG.csrfToken,
                'Accept': 'application/json'
                // Note: Do NOT set Content-Type, browser will handle boundary for FormData
            },
            body: formData
        });

        const data = await response.json();
        const elapsed = ((Date.now() - startTime) / 1000).toFixed(2);

        // 🛠️ DEBUG LOGS
        console.log("📥 RAW API RESPONSE:", data);

        if (data.error) {
            console.error("❌ API ERROR:", data.error);
            alert('Error: ' + data.error);
            resetDetection();
            return;
        }

        // 🎯 FIX: Backend uses 'detections', frontend was using 'result'
        const results = data.detections || [];
        console.log("📊 Parsed Detections:", results);

        if (results.length > 0) {
            document.getElementById('detection-results-container').style.display = 'block';
            document.getElementById('detection-empty-state').style.display = 'none';
            document.getElementById('object-count').textContent = data.total_object;

            // Handle Totals
            const totalDisplay = document.getElementById('total-nominal-display');
            const warning = document.getElementById('multi-currency-warning');
            
            const currencyCount = Object.keys(data.totals_by_currency || {}).length;
            if (currencyCount > 1) {
                totalDisplay.innerHTML = Object.entries(data.totals_by_currency)
                    .map(([curr, amt]) => `<span style="font-size: 1.2rem;">${curr}</span> ${formatNumber(amt)}`)
                    .join('<br>');
                warning.style.display = 'block';
            } else if (currencyCount === 1) {
                const [curr, amt] = Object.entries(data.totals_by_currency)[0];
                totalDisplay.innerHTML = `<span style="font-size: 1.2rem;">${curr}</span> ${formatNumber(amt)}`;
                warning.style.display = 'none';
            } else {
                totalDisplay.textContent = 'Tidak Ada';
            }

            // Handle List
            const listContainer = document.getElementById('detection-list');
            listContainer.innerHTML = '';
            
            results.forEach(item => {
                const div = document.createElement('div');
                div.style = 'background: rgba(255,255,255,.03); border: 1px solid rgba(255,255,255,.05); border-radius: 10px; padding: 10px 14px; display: flex; align-items: center; gap: 12px;';
                div.innerHTML = `
                    <div style="font-size: 1.2rem;">💰</div>
                    <div style="flex: 1;">
                        <div style="font-size: .85rem; font-weight: 600;">${item.readable || item.label}</div>
                        <div style="font-size: .7rem; color: var(--muted);">Confidence ${ (item.confidence * 100).toFixed(1) }%</div>
                    </div>
                    <div style="font-family: 'Syne', sans-serif; font-weight: 700; color: var(--accent);">${formatNumber(item.nominal || 0)}</div>
                `;
                listContainer.appendChild(div);
            });

            // Set primary for converter
            const primary = results[0];
            detectedCurrency = { 
                nominal: data.totals_by_currency[primary.currency] || 0, 
                code: primary.currency, 
                name: primary.readable || primary.label 
            };
            
            const avgConf = (results.reduce((a, b) => a + b.confidence, 0) / results.length * 100).toFixed(1);
            document.getElementById('acc-val').textContent = avgConf + '%';
            document.getElementById('acc-bar').style.width = avgConf + '%';
            document.getElementById('time-val').textContent = elapsed + 's';

            syncCustomSelect('from-select', primary.currency);
            doConvert();
        } else {
            console.log("ℹ️ No detections found by the model.");
            document.getElementById('detection-results-container').style.display = 'block';
            document.getElementById('detection-empty-state').style.display = 'none';
            document.getElementById('total-nominal-display').textContent = 'Tidak Ada';
            document.getElementById('detection-list').innerHTML = '<p style="text-align:center; padding: 20px; color: var(--muted);">Kamera tidak menemukan uang kertas yang jelas.</p>';
        }

    } catch (error) {
        console.error('Detection failed:', error);
        alert('Gagal menghubungi server backend.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '🚀 Mulai Deteksi';
    }
}

window.resetDetection = function() {
    selectedFile = null;
    detectedCurrency = null;
    document.getElementById('image-input').value = '';
    document.getElementById('image-preview').style.display = 'none';
    document.getElementById('upload-placeholder').style.display = 'block';
    document.getElementById('action-buttons').style.display = 'none';
    
    document.getElementById('detection-results-container').style.display = 'none';
    document.getElementById('detection-empty-state').style.display = 'block';
    
    document.getElementById('convert-result').textContent = '—';
    document.getElementById('convert-sub').textContent = 'Upload foto uang untuk melihat hasil konversi';
    document.getElementById('live-rate-info').style.display = 'none';
    document.getElementById('acc-val').textContent = '—';
    document.getElementById('time-val').textContent = '—';
    document.getElementById('acc-bar').style.width = '0%';
}
