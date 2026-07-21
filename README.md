# Simple Run Tracker

Aplikasi web minimalis untuk mencatat, melacak, dan menganalisis aktivitas lari. Dibangun dengan PHP Native, MySQL, Tailwind CSS, Leaflet.js, dan Chart.js.

# Link
run-tracker.page.gd

## Fitur

- **Autentikasi** — Registrasi, login, dan manajemen sesi pengguna
- **Dashboard** — Ringkasan statistik (total jarak, durasi, rata-rata pace) dan grafik tren bulanan
- **Real-time GPS Tracking** — Lacak lari langsung dari browser dengan peta Leaflet, stopwatch, dan pace real-time
- **Input Manual** — Catat aktivitas lari indoor/treadmill tanpa GPS
- **Riwayat Aktivitas** — Lihat dan filter semua aktivitas yang pernah dicatat
- **Detail Aktivitas** — Informasi lengkap tiap sesi, termasuk rute peta jika tersedia

## Tech Stack

- **Backend:** PHP Native (PDO)
- **Database:** MySQL
- **Frontend:** Tailwind CSS (CDN), JavaScript Vanilla
- **Map:** Leaflet.js
- **Chart:** Chart.js
- **Font:** Inter

## Struktur Database

### `users`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | INT (PK) | |
| username | VARCHAR(50) | Unique |
| email | VARCHAR(100) | Unique |
| password | VARCHAR(255) | Bcrypt hash |
| created_at | TIMESTAMP | |

### `activities`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | INT (PK) | |
| user_id | INT (FK) | Relasi ke users |
| date | DATE | Tanggal lari |
| distance | DECIMAL(8,2) | Jarak dalam km |
| duration | INT | Durasi dalam detik |
| pace | DECIMAL(5,2) | Pace dalam menit/km |
| notes | TEXT | Catatan opsional |
| route_path | JSON | Data rute GPS (jika ada) |
| type | ENUM('manual','gps') | Metode input |
| created_at | TIMESTAMP | |

## Instalasi

1. Clone repositori ke folder web server (Laragon / XAMPP / dll):
   ```bash
   git clone https://github.com/username/run-tracker.git
   ```

2. Import database:
   ```bash
   mysql -u root < sql/schema.sql
   ```

3. Sesuaikan koneksi database di `config/database.php` jika perlu.

4. Akses aplikasi melalui browser:
   ```
   http://localhost/run-tracker
   ```

## Struktur Folder

```
run-tracker/
├── activity/         # Halaman aktivitas (create, detail, history, track)
├── api/              # Endpoint API (save_activity)
├── assets/
│   ├── css/          # Stylesheet
│   └── js/           # JavaScript (app, chart, tracker)
├── config/           # Konfigurasi database
├── includes/         # Komponen reusable (header, navbar, auth, dll)
├── sql/              # Skema database
├── dashboard.php     # Halaman utama setelah login
├── index.php         # Entry point
├── login.php         # Halaman login
├── logout.php        # Logout
├── profile.php       # Halaman profil
└── register.php      # Halaman registrasi
```

## Desain

- **Mobile-first**, dioptimalkan untuk penggunaan saat berlari
- Navigasi bottom bar di mobile, sidebar di desktop
- Tombol besar (min 48px) untuk kemudahan sentuhan
