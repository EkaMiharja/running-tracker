# Fitur Baru: Distance Markers & Pace Analysis

## Overview
Dua fitur baru yang akan ditambahkan untuk meningkatkan pengalaman analisis lari:

1. **Distance Markers** – Titik penanda setiap 1 km di peta (mirip Adidas Running).
2. **Pace per Kilometer Bar Chart** – Diagram batang yang menampilkan pace di setiap kilometer di halaman detail aktivitas.

## 1. Distance Markers (Peta)

### Deskripsi
Setiap kali pengguna menempuh jarak kelipatan 1 km selama sesi lari real-time, muncul marker di peta dengan angka kilometer tersebut.

### Spesifikasi
- Muncul otomatis pada 1 km, 2 km, 3 km, dst.
- Marker berupa lingkaran dengan angka di tengah.
- Warna: Emerald Green (#10B981) atau Orange.
- Marker tetap terlihat sampai sesi selesai.
- Data marker disimpan bersama route_path.

### User Flow
1. User mulai lari.
2. Saat mencapai 1 km → marker "1" muncul di peta.
3. Saat mencapai 2 km → marker "2" muncul, dan seterusnya.
4. Selesai lari → semua marker tersimpan.

## 2. Pace per Kilometer (Bar Chart)

### Deskripsi
Di halaman **Detail Aktivitas**, tampilkan **diagram batang** yang menunjukkan pace rata-rata setiap kilometer.

### Spesifikasi
- Sumbu X: Kilometer (1, 2, 3, ...)
- Sumbu Y: Pace dalam menit/km
- Gunakan Chart.js Bar Chart
- Warna batang: Hijau untuk pace bagus, merah/orange untuk pace lambat
- Data disimpan dalam format JSON (`pace_per_km`)

### User Flow
1. User selesai lari dan menyimpan aktivitas.
2. Buka halaman detail aktivitas.
3. Lihat peta dengan distance markers.
4. Scroll ke bawah → melihat **"Pace per Kilometer"** berupa bar chart.

## Data Structure (Database)

Tambahkan atau perluas kolom di tabel `activities`:

```sql
pace_per_km JSON NULL COMMENT 'Contoh: [{"km":1,"pace":"5:12"}, {"km":2,"pace":"5:08"}]'

Technical Tasks

 [x] Modifikasi logic real-time tracking untuk:
   - Menghitung distance markers
   - Menghitung pace per km
 [x] Update tabel activities (tambah field `pace_per_km`)
 [x] Implementasi Distance Markers menggunakan Leaflet.js
 [x] Implementasi Bar Chart dengan Chart.js di halaman detail
 [ ] Testing di kondisi real (jalan kaki, lari, motor)
    
Prioritas
Tinggi – Fitur ini sangat meningkatkan nilai analisis aplikasi.
Status
Development → Testing