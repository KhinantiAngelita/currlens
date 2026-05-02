# CurrencyLens — Backend Deteksi Mata Uang AI

Proyek ini adalah sistem deteksi mata uang berbasis YOLOv8 yang dirancang untuk mengenali berbagai mata uang negara secara akurat. Repositori ini telah direstrukturisasi agar bersih, profesional, dan siap untuk kolaborasi tim di GitHub.

## 📌 Strategi Proyek (Critical)
*   **Training per Negara**: Dataset tidak dicampur antar negara saat training. Setiap negara (IDR, MYR, SGD, dll) memiliki modelnya sendiri.
*   **Inference Terpadu**: Model-model tersebut digabungkan (ensemble/multi-model) hanya pada saat inference di API (`app.py`).
*   **Keamanan Data**: Folder `runs/` dan file `best.pt` adalah aset kritis dan tidak boleh dihapus atau dimodifikasi tanpa backup.

## 📂 Struktur Folder

```text
backend/
├── app.py                # Flask API (Inference & Multi-model Logic)
├── training/             # Skrip dan konfigurasi training
│   ├── train.py          # CLI untuk memulai training
│   ├── test_model.py     # Skrip evaluasi model
│   └── multi_currency.yml # Konfigurasi dataset YOLO
├── data/                 # Penyimpanan Dataset (Terorganisir per Negara)
│   ├── IDR/              # Dataset Rupiah (Indonesia)
│   ├── MYR/              # Dataset Ringgit (Malaysia)
│   ├── SGD/              # Dataset Singapore Dollar
│   ├── THB/              # Dataset Thai Baht
│   └── ...               
├── tools/                # Utility & Preprocessing Scripts
│   ├── labeling/         # Perbaikan & relabeling (fix_label.py, relabel.py)
│   ├── cleaning/         # Pembersihan & standarisasi (clean.py, standardize_v3.py)
│   ├── auditing/         # Analisis distribusi data (audit_dataset.py, scan.py)
│   └── utils/            # Fungsi pembantu (rename.py)
├── models/               # Model dasar (yolov8n.pt, yolov8s.pt)
├── experiments/          # Skrip riset & analisis lama (analyze_matrix.py)
└── runs/                 # [DILARANG HAPUS] Hasil training, logs, & weights
```

## 🚀 Panduan Penggunaan

### 1. Persiapan Lingkungan
```bash
pip install ultralytics flask flask-cors opencv-python pandas matplotlib
```

### 2. Menjalankan Backend (Inference)
API ini akan memuat model terbaik dari folder `runs/` secara otomatis.
```bash
python app.py
```
**Test API**: Gunakan alat seperti Postman atau cURL untuk mengirim gambar ke endpoint `/predict`.

### 3. Training Model Baru
Gunakan CLI yang sudah disediakan untuk menjaga konsistensi:
```bash
# Training model YOLOv8s (Disarankan)
python training/train.py --train-s
```
*Catatan: Pastikan `data.yaml` di dalam folder `data/` masing-masing negara sudah dikonfigurasi dengan benar sebelum memulai.*

## 🛠️ Penjelasan Tools
*   **labeling/**: Digunakan jika ada kesalahan koordinat atau nama class pada label.
*   **cleaning/**: Untuk menghapus gambar corrupt dan memastikan ukuran gambar seragam (standardisasi).
*   **auditing/**: Sangat penting untuk mengecek apakah jumlah data tiap class sudah seimbang (balance).

## ⚠️ Peringatan Penting
1.  **DILARANG** menghapus folder `runs/` karena berisi sejarah training dan model `best.pt` yang sudah jadi.
2.  **JANGAN** mencampur gambar antar negara dalam satu proses training untuk menjaga akurasi spesifik negara.
3.  Pastikan `.gitignore` aktif untuk mencegah upload file model besar ke GitHub.

---

