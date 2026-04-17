# Personnel Simulation System

## Apa Ini?

Aplikasi web untuk simulasi pengelolaan data personel militer dengan operasi CRUD lengkap untuk master data dan data utama.

## Fitur

- **Master Data**: Factions, Ranks, Unit Classes, Weapons
- **Personnel Management**: Kelola data personel dengan relasi ke master data
- **Data Integrity Protection**: Mencegah penghapusan master data yang masih digunakan
- **Responsive UI**: Tampilan yang didukung Bootstrap 5

## Keunikan

1. **Laravel + Inertia.js + Vue 3** - Full-stack SPA tanpa kompleksitas API terpisah
2. **PostgreSQL** - Database relasional yang robust
3. **Resourceful Routing** - Semua route CRUD dibuat otomatis
4. **Soft Delete Protection** - Pengecekan relasi sebelum hapus data

## Tech Stack

- Laravel 11 (PHP 8.x)
- Vue.js 3 + Inertia.js
- PostgreSQL
- Bootstrap 5
- Vite

---

## Cara Pull dari GitHub

```bash
# Clone repository
git clone <repo-url> personnel-sim

# Masuk ke folder
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

# Jalankan server
php artisan serve    # Terminal 1
npm run dev          # Terminal 2
```

## Cara Operasi

### Akses Aplikasi
Buka http://localhost:8000

### Menu Aplikasi
- **Dashboard** - Halaman utama personnel
- **Deploy Personnel** - Form tambah personel
- **Master Data** - Dropdown untuk:
  - Factions
  - Ranks
  - Unit Classes
  - Weapons

### Operasional CRUD

| Action | URL | Method |
|--------|-----|--------|
| List | /factions | GET |
| Create Form | /factions/create | GET |
| Save | /factions | POST |
| Edit Form | /factions/{id}/edit | GET |
| Update | /factions/{id} | PUT |
| Delete | /factions/{id} | DELETE |

---

## Struktur Folder Penting

```
app/Http/Controllers/     # Semua controller
app/Models/              # Semua model
resources/js/Pages/      # Vue pages
resources/js/Layouts/    # Vue layouts
routes/web.php           # Route definitions
```

---

## Lisensi

MIT
