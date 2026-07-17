# Task Breakdown: Simple Run Tracker — Aplikasi Pelacakan Lari Sederhana

## Context

Dokumen ini mendefinisikan urutan implementasi pembuatan Simple Run Tracker sesuai PRD.

Tujuan utama adalah membangun aplikasi secara bertahap, terstruktur, dan dapat ditinjau di setiap tahap.

Setiap task harus diselesaikan dan direview sebelum melanjutkan ke task berikutnya.

Selalu merujuk ke:
- `PRD.md`
- `GUIDELINE.md`

sebelum melakukan implementasi.

## Build Rules

- Ikuti PRD secara tepat
- Ikuti Guideline (Style Guide) secara tepat
- Jangan menambahkan fitur di luar PRD
- Gunakan PHP Native + MySQL
- Frontend: HTML + Tailwind CSS + JavaScript vanilla
- Bangun komponen/shared logic sebelum halaman spesifik
- Kerjakan secara berurutan
- Validasi setiap task sebelum lanjut

## Phase 1 — Project Foundation

### Task 1.1: Setup Project Structure

**Requirements:**
- Buat struktur folder yang rapi
- Buat file konfigurasi database
- Setup koneksi PDO
- Buat file utama (index.php, login.php, dll)

**Deliverables:**
- Folder project yang terorganisir

**Success Criteria:**
- Semua folder dan file dasar sudah ada

### Task 1.2: Database Implementation

**Requirements:**
- Import schema dari PRD
- Buat file config database
- Buat helper function (query, sanitize, dll)

**Success Criteria:**
- Koneksi database berhasil
- Tabel users dan activities sudah terbuat

### Task 1.3: Authentication System

**Requirements:**
- Halaman Register
- Halaman Login
- Session management
- Logout
- Proteksi halaman dengan session

**Success Criteria:**
- User dapat register, login, dan logout dengan aman

## Phase 2 — Core Layout & Navigation

### Task 2.1: Main Layout

**Requirements:**
- Header / Navbar
- Footer (minimal)
- Responsive layout (mobile-first)

**Success Criteria:**
- Layout konsisten di semua halaman

### Task 2.2: Dashboard Layout

**Requirements:**
- Sidebar atau Bottom Navigation (mobile)
- Area konten utama

**Success Criteria:**
- Navigasi antar halaman berfungsi

## Phase 3 — Dashboard Overview

### Task 3.1: Dashboard Widgets

**Requirements:**
- Total Jarak
- Total Durasi
- Rata-rata Pace
- Jumlah Aktivitas

**Success Criteria:**
- Widget menampilkan data dengan benar

### Task 3.2: Trend Chart

**Requirements:**
- Line Chart menggunakan Chart.js (jarak harian/mingguan)

**Success Criteria:**
- Grafik tren berfungsi dan responsif

### Task 3.3: Recent Activities

**Requirements:**
- Daftar aktivitas terbaru (card atau tabel)

**Success Criteria:**
- Bisa klik menuju halaman detail

## Phase 4 — Activity Management

### Task 4.1: Manual Entry Form

**Requirements:**
- Form input: tanggal, jarak, durasi (jam, menit, detik)
- Validasi dan perhitungan pace otomatis

**Success Criteria:**
- Data tersimpan ke database dengan benar

### Task 4.2: Real-time GPS Tracking

**Requirements:**
- Halaman tracking dengan Leaflet.js
- Tombol Mulai, Jeda, Selesai
- Stopwatch
- Live distance & pace
- Penyimpanan route_path (JSON)

**Success Criteria:**
- Pelacakan GPS berjalan lancar di browser mobile

### Task 4.3: Activity History

**Requirements:**
- Daftar semua aktivitas pengguna
- Filter berdasarkan tanggal

**Success Criteria:**
- Riwayat ditampilkan dengan baik

## Phase 5 — Activity Detail

### Task 5.1: Activity Detail Page

**Requirements:**
- Informasi lengkap aktivitas
- Tampilan peta statis (jika ada route_path)
- Metrik detail (pace, jarak, durasi)

**Success Criteria:**
- Halaman detail berfungsi untuk aktivitas manual dan GPS

## Phase 6 — Polish & Finalization

### Task 6.1: Responsive & Mobile Optimization

**Requirements:**
- Pastikan semua halaman nyaman di mobile
- Tombol tracking mudah diakses

**Success Criteria:**
- Aplikasi responsif sempurna

### Task 6.2: Error Handling & Validation

**Requirements:**
- Pesan error yang jelas
- Validasi input
- Keamanan dasar (SQL Injection, XSS)

**Success Criteria:**
- Aplikasi stabil

### Task 6.3: Final Testing & Documentation

**Requirements:**
- Test semua fitur utama
- Update README.md
- Siapkan demo user

**Success Criteria:**
- Semua fitur sesuai PRD

## Final Review

Sebelum menandai proyek selesai:

- Semua route/halaman dapat diakses
- Autentikasi berfungsi dengan baik
- Data tersimpan dan ditampilkan benar
- Real-time GPS berjalan
- Desain sesuai GUIDELINE.md
- Tidak ada error di console
- Aplikasi ringan dan cepat

## Definition of Done

Proyek dianggap selesai ketika:

- Semua fitur di PRD sudah diimplementasikan
- Aplikasi dapat digunakan end-to-end (register → tracking → lihat statistik)
- Mengikuti Guideline desain
- Kode bersih dan terstruktur
- Berfungsi dengan baik di browser mobile
- Data pengguna aman dan privat