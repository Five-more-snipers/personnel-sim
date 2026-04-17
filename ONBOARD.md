# Onboarding untuk Junior Developer

Dokumen ini menjelaskan konsep-konsep dasar Laravel, Vue.js, dan PostgreSQL dengan menggunakan project Personnel-SIM sebagai contoh nyata.

---

## MEMAHAMI ARSITEKTUR PROJECT

### Bagaimana Data Mengalir dalam Project ini

```
User klik tombol/Link
       ↓
Browser mengirim request (URL)
       ↓
[ROUTES] Mencocokkan URL dengan fungsi di Controller
       ↓
[CONTROLLER] Menjalankan logic, mengambil data dari database
       ↓
[MODEL + ELOQUENT] Berkomunikasi dengan PostgreSQL
       ↓
[INERTIA] Mengirim data ke Vue.js dalam format JSON
       ↓
[VUE] Menampilkan halaman ke user
```

---

## 1. POSTGRESQL - DATABASE

### Apa itu PostgreSQL?
PostgreSQL adalah sistem database management (RDBMS) yang menyimpan data secara terstruktur dalam tabel-tabel.

### Konsep Dasar PostgreSQL

| Konsep | Penjelasan |
|-------|-----------|
| **Tabel** | Kumpulan data seperti spreadsheet. Setiap kolom = jenis data, setiap baris = 1 record |
| **Kolom/Field** | Jenis data tertentu (contoh: name, level) |
| **Baris/Record** | 1 data lengkap (contoh: 1 personel) |
| **Primary Key** | Kolom ID unik untuk mengidentifikasi setiap baris |
| **Foreign Key** | Kolom yang menghubungkan ke tabel lain (relasi) |

### Contoh Tabel dalam Project ini

**Tabel `personnels` (tabel utama)**
```sql
id         -- Primary Key (angka unik)
name       -- Nama prajurit
faction_id -- Foreign Key ke tabel factions
rank_id    -- Foreign Key ke tabel ranks
unit_class_id -- Foreign Key ke tabel unit_classes
weapon_id  -- Foreign Key ke tabel weapons
created_at -- Timestamp otomatis
updated_at -- Timestamp otomatis
```

**Tabel-tabelmaster (`factions`, `ranks`, dll)**
```sql
id         -- Primary Key
name       -- Nama jenisnya
level      -- (hanya di ranks) urutan pangkat
type       -- (hanya di weapons) kategori senjata
created_at -- Timestamp
updated_at -- Timestamp
```

### Kenapa Ada Tabel Master?
- **Menghindari duplikasi data** - Tidak perlu mengetik "Kopassus" berkali-kali
- **Mudah update** - Jika nama berubah, cukup ubah di tabel master
- **Data konsisten** - User memilih dari daftar yang sudah ada

---

## 2. LARAVEL - BACKEND FRAMEWORK

Laravel adalah framework PHP untuk membangun aplikasi web. Dalam project ini, Laravel menangani:
- Mengurus database (melalui Eloquent)
- Menerima request dari browser
- Mengirim response ke Vue.js

### A. MIGRATION - MEMBUAT TABLE

Migration adalah cara membuat tabel database dengan kode PHP (bukan SQL langsung).

```php
// database/migrations/2026_04_16_130523_create_personnels_table.php

Schema::create('personnels', function (Blueprint $table) {
    $table->id();                    // Primary Key auto-increment
    $table->string('name');           // Kolom text pendek
    $table->string('callsign')->nullable(); // Optional (bisa kosong)
    
    // foreignID membuat kolom + foreign key constraint
    $table->foreignID('faction_id')->constrained('factions')->onDelete('restrict');
    $table->foreignID('rank_id')->constrained('ranks')->onDelete('restrict');
    $table->foreignID('weapon_id')->constrained('weapons')->onDelete('restrict');
    $table->foreignID('unit_class_id')->constrained('unit_classes')->onDelete('restrict');
    
    $table->timestamps();              // created_at & updated_at otomatis
});
```

**Penjelasan:**
- `->constrained('factions')` - Laravel otomatis melihat tabel `factions` dan kolom `id`
- `->onDelete('restrict')` - Tidak bisa hapus faction jika masih ada personnel
- `$table->timestamps()` - Laravel otomatis mengisi kolom `created_at` dan `updated_at`

### B. MODEL - REPRESENTASI TABLE

Model adalah class PHP yang merepresentasikan 1 tabel. Model memungkinkan kita mengelola data dengan kode PHP (bukan SQL langsung).

```php
// app/Models/Personnel.php

class Personnel extends Model
{
    // $fillable: kolom mana yang boleh diisi lewat form
    protected $fillable = [
        'name',
        'faction_id',
        'rank_id',
        'unit_class_id',
        'weapon_id',
    ];
    
    // Relasi ke tabel lain
    public function faction()
    {
        return $this->belongsTo(Faction::class);
    }
    
    public function rank()
    {
        return $this->belongsTo(Rank::class);
    }
    
    public function unitClass()
    {
        return $this->belongsTo(UnitClass::class);
    }
    
    public function weapon()
    {
        return $this->belongsTo(Weapon::class);
    }
}
```

**Kenapa pakai `$fillable`?**
- Laravel melindungi dari mass assignment attack
- Hanya kolom yang ditulis di `$fillable` bisa diubah lewat form

### C. ELOQUENT - QUERY DATABASE

Eloquent adalah ORM (Object-Relational Mapping) bawaan Laravel. Jadi, daripada menulis SQL:

```sql
SELECT * FROM personnels WHERE id = 1
```

Kita bisa menulis PHP:
```php
Personnel::find(1);
```

**Contoh Eloquent dalam Project ini:**

| Kebutuhan | Kode Eloquent |
|-----------|---------------|
| Ambil semua data | `Personnel::all()` |
| Ambil dengan relasi | `Personnel::with(['faction', 'rank'])->get()` |
| Urutkan terbaru | `Personnel::latest()->get()` |
| Cari berdasarkan ID | `Personnel::find($id)` |
| Buat data baru | `Personnel::create($data)` |
| Update data | `$personnel->update($data)` |
| Hapus data | `$personnel->delete()` |

**Contoh nyata dari PersonnelController.php:**
```php
// Mengambil semua personnel beserta data faction, rank, dsb
$personnels = Personnel::with(['faction', 'rank', 'unitClass', 'weapon'])
                       ->latest()
                       ->get();

// Membuat personnel baru dari data form
Personnel::create($validated);
```

### D. CONTROLLER - LOGIC HANDLING

Controller menangani request dari user dan menentukan response apa yang dikembalikan.

```php
// app/Http/Controllers/PersonnelController.php

class PersonnelController extends Controller
{
    // 1. MENAMPILKAN DAFTAR
    public function index()
    {
        // Ambil data dari database
        $personnels = Personnel::with(['faction', 'rank', 'unitClass', 'weapon'])
                               ->latest()
                               ->get();
        
        // Kirim ke Vue page "Personnel/Index"
        return Inertia::render('Personnel/Index', [
            'personnels' => $personnels
        ]);
    }
    
    // 2. MENAMPILKAN FORM TAMBAH
    public function create()
    {
        // Ambil data untuk dropdown
        return Inertia::render('Personnel/Create', [
            'factions' => Faction::select('id', 'name')->get(),
            'ranks' => Rank::select('id', 'name', 'level')->orderBy('level')->get(),
            'unitClasses' => UnitClass::select('id', 'name')->get(),
            'weapons' => Weapon::select('id', 'name', 'type')->get(),
        ]);
    }
    
    // 3. SIMPAN DATA BARU
    public function store(Request $request)
    {
        // Validasi input dari form
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'faction_id' => 'required|exists:factions,id',
            'rank_id' => 'required|exists:ranks,id',
            'unit_class_id' => 'required|exists:unit_classes,id',
            'weapon_id' => 'required|exists:weapons,id',
        ]);
        
        // Simpan ke database
        Personnel::create($validated);
        
        // Redirect ke halaman utama
        return redirect()->route('personnel.index');
    }
    
    // 4. TAMPILKAN FORM EDIT
    public function edit(Personnel $personnel)
    {
        return Inertia::render('Personnel/Edit', [
            'personnel' => $personnel,
            'factions' => Faction::select('id', 'name')->get(),
            'ranks' => Rank::select('id', 'name', 'level')->get(),
            'unitClasses' => UnitClass::select('id', 'name')->get(),
            'weapons' => Weapon::select('id', 'name', 'type')->get(),
        ]);
    }
    
    // 5. UPDATE DATA
    public function update(Request $request, Personnel $personnel)
    {
        $validated = $request->validate([...]);
        $personnel->update($validated);
        return redirect()->route('personnel.index');
    }
    
    // 6. HAPUS DATA
    public function destroy(Personnel $personnel)
    {
        $personnel->delete();
        return redirect()->route('personnel.index');
    }
}
```

### E. ROUTES - URL MAPPING

Routes mencocokkan URL dengan fungsi di Controller.

```php
// routes/web.php

// Route::resource - Buat 7 route sekaligus untuk master tables
Route::resource('factions', FactionController::class);
Route::resource('ranks', RankController::class);
Route::resource('unit-classes', UnitClassController::class);
Route::resource('weapons', WeaponController::class);

// Custom routes - Personnel tidak pakai root URL (/) tapi /personnel
Route::get('/', [PersonnelController::class, 'index'])->name('personnel.index');
Route::get('/personnel/create', [PersonnelController::class, 'create'])->name('personnel.create');
Route::post('/personnel', [PersonnelController::class, 'store'])->name('personnel.store');
Route::get('/personnel/{personnel}/edit', [PersonnelController::class, 'edit'])->name('personnel.edit');
Route::put('/personnel/{personnel}', [PersonnelController::class, 'update'])->name('personnel.update');
Route::delete('/personnel/{personnel}', [PersonnelController::class, 'destroy'])->name('personnel.destroy');
```

**Apa itu Route Resource?**
Laravel punya helper `Route::resource` yang membuat 7 route sekaligus:

| HTTP Method | URL | Fungsi Controller | Kegunaan |
|-------------|-----|-------------------|----------|
| GET | /factions | index() | Tampilkan daftar |
| GET | /factions/create | create() | Tampilkan form tambah |
| POST | /factions | store() | Simpan data baru |
| GET | /factions/{id} | show() | Tampilkan 1 data |
| GET | /factions/{id}/edit | edit() | Tampilkan form edit |
| PUT | /factions/{id} | update() | Update data |
| DELETE | /factions/{id} | destroy() | Hapus data |

**HTTP Methods:**
- **GET** - Mengambil/menampilkan data
- **POST** - Mengirim data baru
- **PUT** - Mengupdate data yang ada
- **DELETE** - Menghapus data

---

## 3. INERTIA.JS - BRIDGE LARAVEL-VUE

### Kenapa Pakai Inertia.js?
Biasanya, Laravel menyediakan API (JSON) lalu Vue mengambil data dengan fetch/axios. twice the work!

Inertia.js menyederhanakan:
1. Controller mengembalikan HTML tapi pakai Vue components
2. Vue menerima data props langsung dari Controller
3. Tidak perlu buat REST API terpisah

```php
// Tanpa Inertia: return harus JSON
return response()->json(['personnels' => $personnels]);

// Dengan Inertia: return Vue page + data
return Inertia::render('Personnel/Index', [
    'personnels' => $personnels
]);
```

---

## 4. VUE.JS 3 - FRONTEND

Vue.js adalah JavaScript framework untuk membuat interactive UI.

### A. COMPOSITION API

Vue 3 punya 2 cara tulis component: Options API dan Composition API. Project ini pakai Composition API.

```javascript
// Setup script - cara baru (Composition API)
<script setup>
import { ref, computed } from 'vue'

// Reactive state - bisa perubahan
const count = ref(0)

// Computed - hasil kalkulasi otomatis
const doubleCount = computed(() => count.value * 2)
</script>
```

### B. TEMPLATE SYNTAX

Vue punya directive khusus untuk HTML:

| Directive | Penjelasan | Contoh |
|------------|------------|--------|
| `v-for` | Loop array | `<tr v-for="p in personnels">` |
| `v-if` | Kondisi tampil | `<div v-if="show">` |
| `v-model` | Two-way binding | `<input v-model="name">` |
| `@click` | Event click | `<button @click="save()">` |

### C. CONTOH VUE PAGE

**Index.vue (Daftar Personel):**
```vue
<template>
  <AppLayout>
    <table>
      <tr v-for="person in personnels" :key="person.id">
        <td>{{ person.name }}</td>
        <td>{{ person.faction.name }}</td>  <!-- Dari relasi -->
        <td>{{ person.rank.name }}</td>
        <td>
          <Link :href="`/personnel/${person.id}/edit`">Edit</Link>
        </td>
      </tr>
    </table>
  </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// Terima data dari Laravel Controller
defineProps({ personnels: Array })
</script>
```

**Create.vue (Form Tambah):**
```vue
<template>
  <form @submit.prevent="submit">
    <input v-model="form.name" class="form-control" />
    <select v-model="form.faction_id">
      <option v-for="f in factions" :value="f.id">{{ f.name }}</option>
    </select>
    <button type="submit">Save</button>
  </form>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({ factions: Array, ranks: Array, ... })

// useForm - otomatis jadi reactive + handle submit
const form = useForm({
  name: '',
  faction_id: '',
  rank_id: '',
  ...
})

const submit = () => form.post('/personnel')
</script>
```

### D. INERTIA.HJS SPECIAL COMPONENTS

Inertia menyediakan component khusus:

| Component | Penjelasan |
|-----------|-------------|
| `<Link>` | Anchor yang SPA-friendly (tidak reload page) |
| `router` | Programmatic navigation |
| `useForm()` | Form handling dengan validation |

---

## 5. BOOTSTRAP 5 - STYLING

Bootstrap adalah CSS framework untuk cepat styling.

### CONTOH CLASSES

```html
<!-- Layout -->
<div class="container py-4">

<!-- Button -->
<button class="btn btn-primary">Simpan</button>
<button class="btn btn-secondary">Batal</button>

<!-- Form -->
<input class="form-control" />
<select class="form-select" />

<!-- Table -->
<table class="table table-hover">
  <thead><tr>...</tr></thead>
  <tbody><tr>...</tr></tbody>
</table>

<!-- Alert -->
<div class="alert alert-danger">Error message</div>

<!-- Flex -->
<div class="d-flex justify-content-between">
```

---

## TAMBAHAN: VALIDATION

Laravel punya validation built-in:

```php
$request->validate([
    'name' => 'required|string|max:255',      // Wajib, text, max 255 char
    'faction_id' => 'required|exists:factions,id', // Wajib, harus ada di tabel factions
    'rank_id' => 'required|exists:ranks,id',
]);
```

**Rule validation yang sering dipakai:**
- `required` - Wajib isi
- `string` - Harus text
- `integer` - Harus angka
- `max:255` - Maksimal 255 karakter
- `exists:tabel,kolom` - Nilai harus ada di tabel lain
- `unique:tabel,kolom` - Tidak boleh sama dengan yang sudah ada

---

## RINGKASAN ALUR CRUD

### 1. Tampilkan Daftar (READ)
```
GET / atau /factions
  → Route: index()
  → Controller: Personnel::with([...])->get()
  → Model: Eloquent query ke PostgreSQL
  → Inertia: render('Personnel/Index', ['personnels' => $personnels])
  → Vue: v-for loop tampilkan tabel
```

### 2. Tambah Data (CREATE)
```
GET /personnel/create
  → Route: create()  
  → Controller: Ambil data untuk dropdown
  → Inertia: render('Personnel/Create', [...])
  → Vue: Form dengan v-model

POST /personnel
  → Route: store()
  → Controller: $request->validate() → Model::create()
  → Redirect ke index
```

### 3. Edit Data (UPDATE)
```
GET /personnel/{id}/edit
  → Route: edit()
  → Controller: Ambil data + dropdown
  → Inertia: render('Personnel/Edit', [...])
  → Vue: Form dengan data pre-filled

PUT /personnel/{id}
  → Route: update()
  → Controller: validate() → $personnel->update()
  → Redirect ke index
```

### 4. Hapus Data (DELETE)
```
DELETE /personnel/{id}
  → Route: destroy()
  → Controller: $personnel->delete()
  → Redirect ke index
```

---

## TOOLS PENTING

```bash
# Laravel
php artisan serve          # Jalankan server lokal
php artisan migrate       # Buat/update tabel
php artisan make:model NamaModel
php artisan make:controller NamaController
php artisan make:migration create_nama_table
php artisan route:list    # Lihat semua routes

# npm (Vue/Vite)
npm install              # Install dependencies
npm run dev            # Development server
npm run build          # Build untuk production
```

---

## FILE PENTING UNTUK DIPELAJARI

| File | Tujuan |
|------|---------|
| `routes/web.php` | Semua URL dan mapping ke controller |
| `app/Http/Controllers/*` | Logic handling |
| `app/Models/*` | Definisi model dan relasi |
| `database/migrations/*` | Schema tabel |
| `resources/js/Pages/*` | Vue pages |
| `resources/js/Layouts/AppLayout.vue` | Layout utama |
| `resources/js/app.js` | Entry point Vue |

---

## LANGKAH BELAJAR LANJUTAN

1. **Buat fitur baru**: Coba tambah field `phone` ke tabel personnel
2. **Tambah validasi**: Coba tambahkan validasi unique untuk name
3. **Search**: Coba tambahkan fitur search di Index.vue
4. **Pagination**: Jangan tampilkan semua, tapi per halaman