# PRD: Simple Run Tracker — Aplikasi Pelacakan Lari Sederhana

## Product Overview

Simple Run Tracker adalah aplikasi web minimalis yang dirancang untuk membantu pelari mencatat, melacak, dan menganalisis aktivitas lari mereka dengan mudah. 

Aplikasi ini menyediakan dua metode pencatatan utama: pelacakan GPS real-time melalui browser dan input manual. Dengan tampilan yang ringan dan fokus pada esensi, Simple Run Tracker menjadi alternatif yang lebih sederhana dibandingkan aplikasi lari yang kompleks.

Tujuan utama adalah memberikan pengalaman pelacakan yang cepat, ringan, dan informatif tanpa fitur yang berlebihan.

## Problem Statement

Banyak pelari ingin mencatat aktivitas lari mereka secara rutin, namun menghadapi beberapa kendala:

- Aplikasi yang ada terlalu berat dan penuh fitur sosial yang tidak diinginkan
- Sulit mencatat lari di treadmill atau indoor tanpa GPS
- Ingin visualisasi data yang sederhana (jarak, pace, tren) tanpa harus upload ke platform besar
- Khawatir privasi data aktivitas pribadi
- Butuh solusi yang bisa digunakan langsung dari browser mobile

## Product Goals

Aplikasi ini dirancang untuk membantu pengguna:

- Mencatat aktivitas lari dengan cepat dan mudah
- Melihat perkembangan performa melalui grafik sederhana
- Melacak rute lari secara real-time menggunakan GPS
- Menjaga semua data tetap privat
- Memberikan pengalaman yang ringan dan responsif

## Success Metrics

Aplikasi dianggap berhasil apabila:

- Pengguna dapat menyelesaikan pencatatan aktivitas dalam waktu kurang dari 1 menit
- Halaman dashboard memuat di bawah 2 detik
- Pengguna mampu melihat tren performa mereka dalam satu tampilan
- Fitur GPS real-time berjalan lancar di browser mobile
- Pengguna kembali menggunakan aplikasi minimal 3 kali dalam seminggu

## Target Users

### Pelari Pemula

**Responsibilities:**
- Mencatat lari rutin
- Melihat kemajuan jarak dan pace

**Pain Points:**
- Bingung memilih aplikasi yang tidak terlalu rumit
- Sulit memahami data statistik yang kompleks

**Goals:**
- Mudah mencatat setiap sesi lari
- Melihat progress secara visual

### Pelari Rutin / Intermediate

**Responsibilities:**
- Melacak konsistensi latihan
- Meningkatkan pace dan jarak

**Pain Points:**
- Ingin data pribadi tanpa fitur sosial yang mengganggu
- Butuh cara cepat mencatat lari indoor dan outdoor

**Goals:**
- Melihat tren bulanan
- Membandingkan performa antar sesi

### Pelari yang Sering Latihan Indoor

**Responsibilities:**
- Mencatat treadmill running
- Menjaga konsistensi latihan

**Pain Points:**
- Aplikasi GPS tidak akurat di dalam ruangan

**Goals:**
- Input manual yang cepat dan akurat

## User Problems

Platform harus menyelesaikan masalah-masalah berikut:

### Problem 1
Pelari kesulitan mencatat aktivitas dengan cepat dan sederhana.

### Problem 2
Ingin pelacakan GPS real-time tanpa aplikasi berat.

### Problem 3
Data tersebar atau hilang karena bergantung pada platform eksternal.

### Problem 4
Sulit melihat tren performa dalam tampilan yang minimalis.

### Problem 5
Kekhawatiran privasi saat menggunakan aplikasi lari besar.

## Core Features

### Autentikasi Pengguna
- Registrasi dan Login
- Manajemen sesi

### Dashboard Utama
- Ringkasan statistik (total jarak, total durasi, rata-rata pace)
- Grafik tren (Chart.js)
- Riwayat aktivitas terbaru

### Pencatatan Aktivitas

**Real-time GPS Tracking**
- Peta interaktif (Leaflet.js)
- Tombol Mulai, Jeda, Selesai
- Stopwatch, jarak, pace real-time
- Penyimpanan rute sebagai JSON

**Input Manual**
- Form untuk jarak, durasi, dan tanggal

### Detail Aktivitas
- Informasi lengkap satu aktivitas
- Tampilan peta rute (jika ada data GPS)

## Information Architecture
    