# Bug Report & Improvement - Real-time Tracking Simple Run Tracker

## Deskripsi Masalah

Pada fitur **Real-time GPS Tracking**:

- Titik di peta bergerak normal (GPS berhasil mendapatkan koordinat).
- Namun **total kilometer tidak bertambah** saat user berjalan atau lari.
- **Pace tidak muncul atau tidak terhitung**.
- Masalah ini **tidak terjadi** saat user menggunakan motor/kendaraan (karena kecepatan lebih tinggi).

## Penyebab Utama

1. Perhitungan jarak antar titik GPS terlalu sensitif atau terlalu kasar.
2. Tidak ada akumulasi jarak yang benar saat pergerakan lambat (jalan/lari).
3. `coords.speed` dari browser kurang reliable untuk kecepatan rendah.
4. Belum menggunakan rumus jarak yang tepat (Haversine Formula).

## Expected Behavior

- Jarak harus bertambah secara real-time meski user hanya berjalan atau lari pelan.
- Pace harus terhitung dan terupdate berdasarkan jarak dan waktu.
- Metrik (jarak, pace, waktu) harus akurat dan sinkron dengan pergerakan di peta.

## Solusi yang Direkomendasikan

### 1. Perbaikan Perhitungan Jarak
Gunakan rumus **Haversine** untuk menghitung jarak antar dua titik koordinat.

### 2. Logika Update yang Lebih Baik
- Simpan posisi sebelumnya (`lastPosition`).
- Hitung jarak tambahan setiap kali dapat koordinat baru.
- Update total jarak dan pace secara berkala.
- Tambahkan minimum threshold agar tidak terlalu sensitif terhadap noise GPS.

### 3. Perbaikan Pace
Hitung pace berdasarkan **total jarak** dan **total waktu**, bukan hanya dari `coords.speed`.

## Task Perbaikan

- [x] Perbaiki fungsi `watchPosition` dengan Haversine formula.
- [x] Tambahkan logika akumulasi jarak yang benar.
- [x] Perbaiki perhitungan pace (menit/km).
- [x] Tambahkan debounce / smoothing untuk akurasi yang lebih baik.
- [ ] Test di kondisi jalan kaki, lari, dan motor.

## Catatan Tambahan

- Prioritas: **Tinggi** (fitur inti tracking tidak berfungsi dengan baik).
- Tech Stack: Leaflet.js + JavaScript vanilla.
- Setelah ini selesai, lanjut ke implementasi **Carousel Chart** di dashboard (Tren Jarak & Tren Pace).
