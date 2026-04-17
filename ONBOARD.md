# Onboarding - Personnel Simulation System

Dokumen ini menjelaskan arsitektur dan konsep dasar project Personnel-SIM untuk developer baru.

---

## Overview Sistem

Project ini adalah aplikasi web full-stack untuk mengelola data personel militer. User dapat:
- Melihat dan mencari daftar personnel
- Menambah/edit/hapus personnel
- Mengelola master data (faction, rank, unit class, weapon)

---

## Arsitektur Data Flow

```
User klik link/button
        ↓
Browser (GET/POST/PUT/DELETE request)
        ↓
[ROUTES] Cocokkan URL dengan Controller
        ↓
[CONTROLLER] Eksekusi logic, query database
        ↓
[MODEL + ELOQUENT] Komunikasi dengan PostgreSQL
        ↓
[INERTIA] Kirim data ke Vue dalam format JSON
        ↓
[VUE] Render halaman ke user
```

---

## 1. DATABASE - PostgreSQL

### Schema Tabel

**personnels (tabel utama)**
```php
// database/migrations/2026_04_16_130523_create_personnels_table.php
Schema::create('personnels', function (Blueprint $table) {
    $table->id();                    // Primary Key auto-increment
    $table->string('name');           // Nama personel
    $table->string('callsign')->nullable(); // Callsign (opsional)
    
    // Foreign Key dengan constraint
    $table->foreignID('faction_id')->constrained('factions')->onDelete('restrict');
    $table->foreignID('rank_id')->constrained('ranks')->onDelete('restrict');
    $table->foreignID('unit_class_id')->constrained('unit_classes')->onDelete('restrict');
    $table->foreignID('weapon_id')->constrained('weapons')->onDelete('restrict');
    
    $table->timestamps();             // created_at & updated_at otomatis
});
```

**Penjelasan:**
- `constrained('factions')` - Laravel otomatis lihat tabel dan primary key
- `onDelete('restrict')` - Tidak bisa hapus jika masih ada personnel

**Master Tables**
| Tabel | Kolom |
|-------|------|
| factions | id, name |
| ranks | id, name, level |
| unit_classes | id, name |
| weapons | id, name, type |

---

## 2. LARAVEL - Backend

### A. Model (app/Models/)

Model adalah class yang merepresentasikan 1 tabel.

```php
// app/Models/Personnel.php
class Personnel extends Model
{
    // $fillable: kolom yang boleh diisi via form
    protected $fillable = [
        'name',
        'faction_id',
        'rank_id',
        'unit_class_id',
        'weapon_id',
    ];
    
    // Relasi ke tabel master
    public function faction() {
        return $this->belongsTo(Faction::class);
    }
    
    public function rank() {
        return $this->belongsTo(Rank::class);
    }
    
    public function unitClass() {
        return $this->belongsTo(UnitClass::class);
    }
    
    public function weapon() {
        return $this->belongsTo(Weapon::class);
    }
}
```

### B. Eloquent Query Examples

| Kebutuhan | Kode Eloquent |
|-----------|--------------|
| Ambil semua | `Personnel::all()` |
| Ambil dengan relasi | `Personnel::with(['faction', 'rank'])->get()` |
| Terbaru dulu | `Personnel::latest()->get()` |
| Dengan pagination | `Personnel::paginate(10)` |
| Cari nama (case-insensitive) | `Personnel::where('name', 'ilike', '%test%')` |
| Filter relasi | `$personnels->whereHas('faction', fn($q) => $q->where('name', 'ilike', '%kopassus%'))` |
| Buat data | `Personnel::create($data)` |
| Update | `$personnel->update($data)` |
| Hapus | `$personnel->delete()` |

### C. Controller (app/Http/Controllers/)

```php
// app/Http/Controllers/PersonnelController.php
class PersonnelController extends Controller
{
    // 1. INDEX - Tampilkan daftar
    public function index(Request $request)
    {
        // Parse query params untuk filtering
        $search = $request->query('search');
        $perPage = $request->query('perPage', 10);
        
        $personnels = Personnel::with(['faction', 'rank', 'unitClass', 'weapon']);
        
        if ($search) {
            $personnels->where('name', 'ilike', '%' . $search . '%');
        }
        
        $personnels = $personnels->latest()->paginate($perPage);
        
        // Kirim ke Vue dengan Inertia
        return Inertia::render('Personnel/Index', [
            'personnels' => $personnels,
            'filters' => $request->query(),
        ]);
    }
    
    // 2. CREATE - Form tambah
    public function create()
    {
        return Inertia::render('Personnel/Create', [
            'factions' => Faction::select('id', 'name')->get(),
            'ranks' => Rank::select('id', 'name', 'level')->orderBy('level')->get(),
            // ...
        ]);
    }
    
    // 3. STORE - Simpan data baru
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'faction_id' => 'required|exists:factions,id',
            // ...
        ]);
        
        Personnel::create($validated);
        
        return redirect()->route('personnel.index');
    }
    
    // 4. EDIT - Form edit
    public function edit(Personnel $personnel)
    {
        return Inertia::render('Personnel/Edit', [
            'personnel' => $personnel,
            // data dropdown...
        ]);
    }
    
    // 5. UPDATE - Update data
    public function update(Request $request, Personnel $personnel)
    {
        $validated = $request->validate([...]);
        $personnel->update($validated);
        return redirect()->route('personnel.index');
    }
    
    // 6. DESTROY - Hapus data
    public function destroy(Personnel $personnel)
    {
        $personnel->delete();
        return redirect()->route('personnel.index');
    }
}
```

### D. Routes (routes/web.php)

```php
// Master tables - otomatis生成 7 route per resource
Route::resource('factions', FactionController::class);
Route::resource('ranks', RankController::class);
Route::resource('unit-classes', UnitClassController::class);
Route::resource('weapons', WeaponController::class);

// Personnel - manual routes (karena tidak menggunakan /{id} untuk index)
Route::get('/', [PersonnelController::class, 'index'])->name('personnel.index');
Route::get('/personnel/create', [PersonnelController::class, 'create'])->name('personnel.create');
Route::post('/personnel', [PersonnelController::class, 'store'])->name('personnel.store');
Route::get('/personnel/{personnel}/edit', [PersonnelController::class, 'edit'])->name('personnel.edit');
Route::put('/personnel/{personnel}', [PersonnelController::class, 'update'])->name('personnel.update');
Route::delete('/personnel/{personnel}', [PersonnelController::class, 'destroy'])->name('personnel.destroy');
```

**Route::resource generates 7 routes:**
| Method | URL | Function |
|--------|-----|----------|
| GET | /factions | index() |
| GET | /factions/create | create() |
| POST | /factions | store() |
| GET | /factions/{id} | show() |
| GET | /factions/{id}/edit | edit() |
| PUT | /factions/{id} | update() |
| DELETE | /factions/{id} | destroy() |

---

## 3. INERTIA.JS - Bridge Laravel-Vue

Inertia.js menyederhanakan full-stack开发:
- Controller return Vue component, bukan JSON
- Vue terima data sebagai props
- Tidak perlu buat REST API terpisah

```php
// Controller
return Inertia::render('Personnel/Index', [
    'personnels' => $personnels
]);

// Vue (menerima sebagai props)
defineProps({ personnels: Object })
```

---

## 4. VUE.JS 3 - Frontend

### A. Composition API

```javascript
<script setup>
import { ref, computed, watch } from 'vue'

// Reactive state
const search = ref('')

// Computed property
const filteredData = computed(() => ...)

// Watch perubahan
watch(search, (newVal) => { ... })
</script>
```

### B. Vue Directives

| Directive | Usage | Penjelasan |
|----------|-------|-----------|
| v-for | `<tr v-for="p in personnels">` | Loop array |
| v-if | `<div v-if="show">` | Kondisi tampil |
| v-model | `<input v-model="name">` | Two-way binding |
| @click | `@click="submit()"` | Event handler |

### C. Inertia Components

```javascript
import { Link, router } from '@inertiajs/vue3'

// Link untuk SPA navigation
<Link href="/personnel/create">Tambah</Link>

// Router untuk programmatic navigation
router.get('/personnel', { search: 'john' })
router.post('/personnel', form)
router.put('/personnel/1', form)
router.delete('/personnel/1')

// useForm untuk form handling
import { useForm } from '@inertiajs/vue3'
const form = useForm({ name: '', faction_id: '' })
form.post('/personnel')
```

### D. Contoh Vue Page

**resources/js/Pages/Personnel/Index.vue**
```vue
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, router } from '@inertiajs/vue3'

defineProps({
    personnels: Object,  // Data dari controller
    filters: Object,
})

const search = ref('')

const applyFilters = () => {
    router.get('/', { search: search.value }, { preserveState: true })
}
</script>

<template>
    <AppLayout>
        <div class="container">
            <h2>Active Personnel Roster</h2>
            
            <!-- Search -->
            <input v-model="search" @input="applyFilters" placeholder="Search..." />
            
            <!-- Table -->
            <table class="table">
                <tr v-for="person in personnels.data" :key="person.id">
                    <td>{{ person.name }}</td>
                    <td>{{ person.faction?.name }}</td>  <!-- Dari relasi -->
                    <td>
                        <Link :href="`/personnel/${person.id}/edit`">Edit</Link>
                    </td>
                </tr>
            </table>
        </div>
    </AppLayout>
</template>
```

---

## 5. BOOTSTRAP 5 - Styling

### Class Penting

```html
<!-- Container -->
<div class="container py-4">

<!-- Buttons -->
<button class="btn btn-primary">Primary</button>
<button class="btn btn-secondary">Secondary</button>
<button class="btn btn-outline-danger">Outline Danger</button>

<!-- Form -->
<input class="form-control" />
<select class="form-select" />

<!-- Table -->
<table class="table table-hover table-striped">
    <thead class="table-dark">...</thead>
</table>

<!-- Layout -->
<div class="row">
    <div class="col-md-3">...</div>
</div>

<!-- Flex -->
<div class="d-flex justify-content-between">
```

---

## 6. Validation

### Laravel Validation Rules

```php
$request->validate([
    'name' => 'required|string|max:255',
    'faction_id' => 'required|exists:factions,id',
    'rank_id' => 'required|exists:ranks,id',
    'weapon_id' => 'required|exists:weapons,id',
]);
```

**Common Rules:**
| Rule | Penjelasan |
|------|-----------|
| required | Wajib isi |
| string | Harus text |
| integer | Harus angka |
| max:255 | Maksimal 255 karakter |
| exists:tabel,kolom | Nilai harus ada di tabel lain |
| unique:tabel,kolom | Tidak boleh duplikat |

---

## Delete Protection

Setiap master table controller mengecekrelasi sebelum hapus:

```php
// FactionController.php
public function destroy(Faction $faction)
{
    // Cek apakah masih ada personnel yang pakai faction ini
    if ($faction->personnels()->exists()) {
        return redirect()->route('factions.index')
            ->with('error', 'Cannot delete this faction because it is still being used by personnel.');
    }
    $faction->delete();
    return redirect()->route('factions.index');
}
```

---

## File Penting untuk Dipelajari

| Lokasi | Tujuan |
|--------|--------|
| `routes/web.php` | Semua URL routing |
| `app/Http/Controllers/PersonnelController.php` | Logic utama personnel |
| `app/Models/Personnel.php` | Model dan relasi |
| `database/migrations/*` | Schema database |
| `resources/js/Pages/Personnel/Index.vue` | Halaman utama |
| `resources/js/Layouts/AppLayout.vue` | Layout utama |

---

## Commands useful

```bash
# Laravel
php artisan serve          # Jalankan server
php artisan migrate     # Buat/update tabel
php artisan route:list   # Lihat semua routes
php artisan db:seed      # Seed database

# Vue/Vite
npm run dev            # Dev server
npm run build          # Build production
```