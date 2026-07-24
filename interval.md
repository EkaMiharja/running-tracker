# Running Tracker - Fitur Interval Training

## 1. Start Screen (Pre-Run)

**Layout:**
- Tombol besar: `START RUNNING →`
- Tombol menu di sebelah kanan: `☰` (tiga garis)

**Klik ☰** → Bottom Sheet muncul:
- Free Run
- **Interval Training**
- (bisa ditambah opsi lain di kemudian hari)

---

## 2. Interval Builder Screen

**Isi Form:**
- **Nama Workout** (contoh: Interval 800m)
- **Warm-up** (opsional): Durasi atau Jarak
- **Jumlah Interval**: Stepper (misal 8x)
- **High Intensity**:
  - Jarak atau Durasi
  - **Target Pace** (wajib diisi)
- **Recovery**:
  - Jarak atau Durasi
  - Tipe: Jog / Walk / Stand
- **Cool-down** (opsional)

**Tombol Bawah:**
- Simpan sebagai Template
- **MULAI INTERVAL** (disabled jika Target Pace kosong)

---

## 3. Running Screen - Interval Mode

**Layout dari atas ke bawah:**

### Header Atas (di tengah)
- `Interval 3 / 8`

### Status Interval (besar & tebal)
- `HIGH INTENSITY` (warna #fc5200)
- atau `RECOVERY` (warna Hijau)

### Map Tracker
- Area tengah besar
- Menampilkan rute real-time + lokasi pelari

### Panel Informasi Bawah (2x2 Grid)
- Timer Countdown Interval
- Pace Saat Ini
- Jarak
- Total Waktu

### Bottom Control Bar (sudah ada)
[ PAUSE ]   [ 🔒 ]   [ FINISH ]


**Behavior Lock:**
- Tombol **FINISH** default terkunci (`disabled`)
- Harus klik icon kunci (`🔒`) terlebih dahulu baru tombol Finish aktif

---

## Warna Status
- **High Intensity**: `#fc5200` (Orange)
- **Recovery**: Hijau ( `#10B981` atau sejenis)

---

## Catatan Penting Teknis

**Perhitungan Pace & Jarak:**
- **Samakan sepenuhnya** dengan logic yang sudah ada di mode Free Run / Real-time Tracking saat ini.
- Tidak perlu mengubah algoritma pembacaan GPS, perhitungan jarak (Haversine), atau perhitungan pace.
- Fitur Interval hanya menambahkan **layer logika pengaturan sesi** (High / Recovery) di atas sistem tracking yang sudah ada.

**Yang Tetap Digunakan:**
- Fungsi `watchPosition`
- Perhitungan total distance
- Logika pace saat ini
- Distance markers (jika sudah diimplementasikan)
- Penyimpanan route_path

## untuk database sesuaikan

## Catatan Tambahan
- Map tetap terlihat jelas meski ada status interval
- Countdown otomatis ganti sesi (High ↔ Recovery)
- Notifikasi suara / getar saat pergantian interval (bisa ditambahkan belakangan)
