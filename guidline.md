# Guideline: Simple Run Tracker — Aplikasi Pelacakan Lari Sederhana

## Design Philosophy

Simple Run Tracker harus terasa ringan, bersih, dan fokus pada fungsi utama.

Pengalaman harus memprioritaskan:

- Kesederhanaan di atas segalanya
- Kecepatan di atas fitur mewah
- Kejelasan data di atas dekorasi
- Privasi dan minimalisme
- Kemudahan penggunaan di mobile

Pengguna harus merasa aplikasi ini "hanya untuk lari" tanpa gangguan yang tidak perlu.

Interface harus menyampaikan kesan:
- Ringan
- Fokus
- Praktis
- Personal
- Reliable

## Design References

**Primary References:**
- Strava (hanya untuk inspirasi fungsi, bukan tampilan)
- Nike Run Club (minimalis)
- Apple Health / Fitness
- Runkeeper (versi lama yang lebih sederhana)

**Secondary References:**
- Notion (clean interface)
- Linear (minimal & fast)
- Duolingo (gamification ringan — hanya jika relevan)

Gunakan referensi ini hanya untuk inspirasi layout, hierarchy, dan UX flow. Jangan tiru tampilan secara langsung.

## Visual Personality

Aplikasi harus terasa:
- Minimalis
- Modern
- Sporty tapi tidak norak
- Clean dan fresh
- Mudah dibaca saat bergerak (running)

Hindari:
- Terlalu ramai / crowded
- Warna-warna neon berlebihan
- Elemen gaming berat
- Banyak animasi yang membebani

## Color System

### Primary
**Emerald Green**  
Hex: `#10B981`  
Usage: Tombol utama, indikator aktif, progress lari, accent utama

### Secondary
**Sky Blue**  
Hex: `#0EA5E9`  
Usage: Peta, elemen GPS, link, secondary button

### Neutral
- Dark Background: `#111827` (dark mode utama)
- Surface: `#1F2937`
- Card: `#374151`
- Text Primary: `#F3F4F6`
- Text Secondary: `#9CA3AF`
- Border: `#4B5563`

### Status
- Success: `#10B981` (pace bagus)
- Warning: `#F59E0B`
- Danger: `#EF4444` (pace lambat)

## Typography

**Font Family Utama:**  
`Inter` (sans-serif)

**Fallback:**  
system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif

**Hierarchy:**
- **Display/Heading 1**: Bold, 24–32px (judul halaman, metrik besar)
- **Heading 2**: Semibold, 18–22px
- **Body**: Regular, 16px
- **Caption/Small**: 14px (metadata, waktu, keterangan)

## Layout System

- **Mobile First** (karena banyak digunakan saat lari)
- **Container Width**: Maksimal 1280px di desktop
- **Spacing System**: 4px base (4, 8, 12, 16, 24, 32, 48)
- **Grid**: 12 kolom di desktop, stack di mobile

## Border Radius

- Button: 12px
- Card: 16px
- Input & Form: 10px
- Map Container: 12px

## Shadows

Gunakan shadow halus:
- `shadow-sm` untuk card
- Hindari shadow besar yang membuat tampilan berat

## Navigation

- **Bottom Navigation** di mobile (Home, History, New Activity, Profile)
- **Sidebar** di desktop (opsional)
- Top bar: Logo + nama user + logout

## Cards & Widgets

Setiap card harus:
- Judul jelas
- Metrik besar
- Keterangan kecil (tanggal, durasi, dll)
- Warna accent jika ada progress

## Dashboard Components

- KPI Widgets (Total Distance, Total Time, Avg Pace)
- Line Chart tren bulanan
- Recent Activities (list card)

## Real-time Tracking Page

- Full height map di atas
- Panel metrik mengambang di bawah (jarak, pace, waktu)
- Tombol besar (Start / Pause / Finish)

## Forms

- Label jelas
- Input besar dan mudah disentuh di mobile
- Validasi real-time
- Tombol utama berwarna emerald

## Empty States

Setiap halaman kosong harus memiliki:
- Ilustrasi sederhana (opsional)
- Pesan yang membantu
- Tombol aksi utama (contoh: "Mulai Lari Pertama")

## Loading States

- Gunakan skeleton loader untuk card dan tabel
- Spinner kecil untuk aksi real-time

## Responsive Rules

- **Mobile** (< 768px): Prioritas utama
- **Tablet** (768px – 1024px)
- **Desktop** (> 1024px)

Pastikan tombol tracking mudah dijangkau dengan satu tangan.

## Accessibility

- Kontras warna yang baik (terutama teks di atas background gelap)
- Ukuran tombol minimal 48px untuk sentuhan
- Semantic HTML
- Support keyboard navigation

## Development Rules

- Gunakan Tailwind CSS atau Bootstrap (sesuai kesepakatan awal)
- PHP Native + PDO
- JavaScript vanilla + Leaflet.js + Chart.js
- Kode harus bersih, terdokumentasi, dan mudah dimaintain
- Ikuti struktur folder yang sudah disepakati

## Success Criteria

Guideline ini dianggap berhasil jika:

- Aplikasi terasa ringan dan cepat
- Pengguna dapat memulai tracking dalam 2 klik
- Tampilan tetap nyaman dibaca saat outdoor
- Desain konsisten di semua halaman
- Pengguna merasa aplikasi ini "milik mereka" dan tidak berlebihan          