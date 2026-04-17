# Personnel Simulation System

Aplikasi web untuk simulasi pengelolaan data personel militer dengan operasi CRUD lengkap untuk master data dan data utama.

## Fitur

- **Master Data**: Factions, Ranks, Unit Classes, Weapons
- **Personnel Management**: Kelola data personel dengan relasi ke master data
- **Filtering & Search**: Pencarian nama, filter faction/unit class/rank
- **Pagination**: Konfigurasi jumlah data per halaman (10/25/50)
- **Data Integrity Protection**: Pencegahan hapus master data yang masih digunakan
- **Responsive UI**: Tampilan dengan Bootstrap 5

## Tech Stack

| Komponen | Teknologi |
|----------|-----------|
| Backend | Laravel 11 (PHP 8.x) |
| Frontend | Vue.js 3 + Inertia.js |
| Database | PostgreSQL |
| Styling | Bootstrap 5 |
| Build Tool | Vite |

## Keunikan

1. **Laravel + Inertia.js + Vue 3** - Full-stack SPA tanpa REST API terpisah
2. **Eloquent ORM** - Query database dengan syntax PHP (bukan SQL langsung)
3. **Route Resource** - Otomatis生成 7 route untuk master tables
4. **Delete Protection** - Pengecekan relasi sebelum hapus data master

---

## Cara Instalasi

```bash
# Clone repository
git clone <repo-url> personnel-sim
cd personnel-sim

# Install dependencies PHP
composer install

# Install dependencies JS
npm install

# Copy environment file
cp .env.example .env

# Setup database di .env:
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=personnel_sim
# DB_USERNAME=postgres
# DB_PASSWORD=your_password

# Create database
createdb personnel_sim

# Run migrations
php artisan migrate

# Seed master data (opsional)
php artisan db:seed

# Jalankan server
php artisan serve    # Terminal 1
npm run dev          # Terminal 2
```

## Access Aplikasi

Buka http://localhost:8000

## Menu Aplikasi

| Menu | URL | Keterangan |
|------|-----|-------------|
| Personnel | `/` | Daftar seluruh personnel |
| Deploy | `/personnel/create` | Form tambah personnel |
| Factions | `/factions` | Master data faction |
| Ranks | `/ranks` | Master data pangkat |
| Unit Classes | `/unit-classes` | Master data kelas |
| Weapons | `/weapons` | Master data senjata |

## Database Schema

### Tabel Utama: `personnels`
| Kolom | Tipe | Keterangan |
|------|------|----------|
| id | bigint | Primary Key |
| name | varchar(255) | Nama personel |
| callsign | varchar(255) | Callsign (nullable) |
| faction_id | bigint | Foreign Key ke factions |
| rank_id | bigint | Foreign Key ke ranks |
| unit_class_id | bigint | Foreign Key ke unit_classes |
| weapon_id | bigint | Foreign Key ke weapons |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diubah |

### Tabel Master

| Tabel | Kolom | Keterangan |
|-------|-------|-------------|
| factions | id, name | Nama faction/satuan |
| ranks | id, name, level | Pangkat (level untuk urutan) |
| unit_classes | id, name | Kelas/unit |
| weapons | id, name, type | Senjata dan jenisnya |

---

## Struktur Folder Penting

```
app/Http/Controllers/     # Controller (PersonnelController, FactionController, dll)
app/Models/              # Model Eloquent (Personnel, Faction, Rank, dll)
resources/js/Pages/      # Vue pages (Personnel/Index.vue, Master/Faction/Index.vue, dll)
resources/js/Layouts/     # Vue layouts (AppLayout.vue)
routes/web.php           # Route definitions
database/migrations/  # Schema tabel
```

## Lisensi

MIT