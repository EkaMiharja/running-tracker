# Fitur: Circular Countdown Timer (View 2) - Interval Training

## Deskripsi
Di layar Running Interval Mode, tambahkan **View 2** berupa stopwatch hitung mundur berbentuk lingkaran besar di tengah halaman. User bisa berganti antara View Map dan View Countdown ini.

---

## Layout View 2 (Circular Countdown)

**Posisi:**
- Mengisi area tengah utama (menggantikan peta saat dipilih)

**Elemen Visual:**
- **Lingkaran Besar** (Circular Progress)
  - Progress ring yang menyusut searah jarum jam sesuai waktu tersisa
  - Warna mengikuti status interval:
    - High Intensity → `#fc5200` (Orange)
    - Recovery → `#10B981` (Hijau)
- **Angka Countdown** di tengah lingkaran (sangat besar)
  - Format: `MM:SS`
- **Teks Status** di bawah angka
  - `HIGH INTENSITY` atau `RECOVERY`
- **Label kecil** di bawah: "Sisa waktu interval ini"

---

## Cara Berpindah View
- Geser horizontal (swipe) di area tengah untuk berpindah antara:
  - View 1 → Map Tracker
  - View 2 → Circular Countdown
- Atau gunakan tab kecil di bawah header: `MAP` | `TIMER`

---

## Behavior

- Countdown berjalan otomatis setiap detik
- Saat waktu habis → otomatis pindah ke interval berikutnya
- Notifikasi suara / getar saat pergantian interval
- Progress ring direset setiap kali masuk interval baru
- Tetap menampilkan data dasar (Pace Saat Ini, Jarak, dll) di panel bawah

---

## Catatan Teknis

- Gunakan Canvas atau library circular progress (bisa pakai Chart.js dengan tipe Doughnut + trick, atau pure CSS + JS)
- Ukuran lingkaran harus besar dan mudah dibaca saat berlari
- Tetap gunakan logic perhitungan jarak & pace yang sudah ada (tidak diubah)
- Sinkron dengan timer interval High / Recovery

---

## Status
Fokus Pengembangan: View 2 - Circular Countdown Timer  
**Tanggal**: 24 Juli 2026