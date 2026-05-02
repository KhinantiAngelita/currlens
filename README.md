# ₿ CurrLens — Deteksi & Konversi Mata Uang AI

CurrLens adalah platform berbasis AI yang mampu mendeteksi uang kertas dari berbagai negara melalui foto dan langsung mengonversinya ke nilai mata uang pilihan Anda. Proyek ini menggabungkan kekuatan **YOLOv8** untuk pengenalan visual dan **Flask** untuk API backend yang cepat.

## 📝 Deskripsi Proyek
Proyek ini dikembangkan untuk memudahkan traveler atau pengguna internasional dalam mengenali nominal uang asing secara instan. Sistem ini dilatih secara khusus per negara untuk menjaga akurasi yang tinggi sebelum akhirnya digabungkan dalam satu sistem cerdas.

## 📂 Struktur Folder
Repositori ini telah diatur agar mudah dipahami oleh seluruh anggota tim:

- **`backend/`**: Folder utama logika Python (Flask API).
- **`frontend/`**: Kode antarmuka pengguna (Web UI).
- **`training/`**: Skrip khusus untuk melatih model YOLO baru.
- **`models/`**: Tempat menyimpan model dasar (weights) YOLOv8.
- **`tools/`**: Koleksi skrip pembantu (cleaning data, audit dataset, relabeling).
- **`data/`**: (Lokal) Folder untuk menyimpan gambar dataset.
- **`runs/`**: (Lokal) Folder hasil training (logs & model jadi).

## ⚙️ Persiapan (WAJIB)
Sebelum menjalankan proyek, pastikan Anda sudah menginstal hal-hal berikut:

1.  **Python**: Versi 3.8 atau yang lebih baru.
2.  **Dependencies**: Buka terminal di folder root dan jalankan:
    ```bash
    pip install -r requirements.txt
    ```
    *Jika belum ada file requirements.txt, Anda bisa menginstal manual:*
    ```bash
    pip install ultralytics flask flask-cors opencv-python pandas matplotlib
    ```

## 📥 Download Model
Karena file model (`best.pt`) berukuran besar, kami tidak menyimpannya di GitHub. Anda harus mengunduhnya secara manual:

1.  **Download link**: [Klik di sini untuk Download best.pt (Google Drive)](https://drive.google.com/your-placeholder-link)
2.  **Penyimpanan**: Masukkan file `best.pt` ke dalam folder:
    `backend/runs/detect/currency_lens_v3_s/weights/`

## 🚀 Cara Menjalankan (INFERENCE)
Ikuti langkah ini untuk menjalankan aplikasi di komputer Anda:

1.  **Jalankan Backend**:
    ```bash
    cd backend
    python app.py
    ```
    Backend akan berjalan di `http://127.0.0.1:5000`.

2.  **Jalankan Frontend**:
    Buka folder `frontend` di terminal baru, lalu jalankan server web (misalnya menggunakan Live Server atau `npm run dev` jika menggunakan framework).

3.  **Uji Coba**:
    Buka aplikasi di browser, upload foto uang kertas, dan lihat hasil deteksinya.

## 🧠 Cara Training Model
Jika Anda ingin melatih model baru dengan dataset tambahan:

1.  **Siapkan Dataset**: Masukkan gambar di `data/[Nama_Negara]/images` dan label di `data/[Nama_Negara]/labels`.
2.  **Jalankan Training**:
    ```bash
    python training/train.py --train-s
    ```
3.  **Catatan**: Training dilakukan **per negara** (IDR sendiri, MYR sendiri). Jangan mencampur dataset antar negara agar model tidak bingung.

## ⚠️ Catatan Penting
*   **Dataset Tidak di GitHub**: Jangan kaget jika folder `data/` kosong. Dataset disimpan di cloud eksternal demi efisiensi repo.
*   **Keamanan Model**: Jangan menghapus folder `runs/` jika Anda memiliki model yang sudah bagus di sana.
*   **Path Absolut**: Jika menggunakan Windows, pastikan jalur file di `data.yaml` sudah sesuai dengan lokasi folder di komputer Anda.

## 🛠️ Troubleshooting (Masalah Sering Muncul)
*   **Model tidak ditemukan**: Pastikan file `best.pt` sudah ditaruh di folder yang benar (cek bagian Download Model).
*   **Hasil 0 Detection**: Coba turunkan `CONF_THRESHOLD` di `app.py` atau pastikan pencahayaan foto cukup terang.
*   **Path Error**: Gunakan garis miring miring (`/`) atau double backslash (`\\`) pada path di Windows.

---
*Dibuat dengan ❤️ untuk tim CurrencyLens.*
