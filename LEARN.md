# Panduan Pembelajaran Laravel-Vue-Bootstrap

Dokumen ini berisi step-by-step dan konsep yang harus dipahami untukmereplikasi project personnel-sim.

---

## STACK TEKNOLOGI

| Komponen | Teknologi |
|----------|------------|
| Backend | Laravel 12 |
| Frontend | Vue.js 3 (Composition API) |
| Bridge | Inertia.js |
| Styling | Bootstrap 5 |
| Build Tool | Vite |

---

## STRUKTUR DATABASE

Project ini memiliki 5 tabel:

### Master Tables (tabel referensi)
1. **factions** - Faksi/personel group
2. **ranks** - Pangkat militer (memiliki field `level` untuk urutan)
3. **unit_classes** - Kelas unit
4. **weapons** - Senjata (memiliki field `type` untuk kategori)

### Main Table (tabel utama)
5. **personnels** - Data prajurit dengan relasi ke semua master tables

### Schema Tabel

```php
// factions
$table->id();
$table->string('name');
$table->timestamps();

// ranks
$table->id();
$table->string('name');
$table->integer('level');  // urutan pangkat (higher = more senior)
$table->timestamps();

// unit_classes
$table->id();
$table->string('name');
$table->timestamps();

// weapons
$table->id();
$table->string('name');
$table->string('type');    // kategori senjata
$table->timestamps();

// personnels
$table->id();
$table->string('name');
$table->foreignId('faction_id')->constrained();
$table->foreignId('rank_id')->constrained();
$table->foreignId('unit_class_id')->constrained();
$table->foreignId('weapon_id')->constrained();
$table->timestamps();
```

---

## KONSEP YANG HARUS DIPAHAMI

### 1. Laravel Basics
- **Route** - Cara mendefinisikan URL dan controller mana yang menangani
- **Controller** - Logic handling untuk request masuk
- **Model** - Representasi database table
- **Migration** - Schema database version control
- **Eloquent** - ORM Laravel untuk query database

### 2. Vue.js 3 (Composition API)
- **Component** - Building block UI
- **Props** - Data passing dari parent ke child
- **Reactive State** - Menggunakan `ref` dan `reactive`
- **Template Syntax** - `v-for`, `v-if`, `v-model`

### 3. Inertia.js
- Bridge antara Laravel dan Vue.js
- Server-side rendering dengan client-side navigation
- Tidak perlu membuat REST API terpisah

### 4. Bootstrap 5
- Grid system
- Component: buttons, forms, tables, cards
- Utility classes

---

## STEP-BY-STEP MEMBUAT CRUD

### Step 1: Setup Database

```bash
php artisan make:migration create_factions_table
```

```php
// database/migrations/xxxx_create_factions_table.php
Schema::create('factions', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->timestamps();
});
```

```bash
php artisan migrate
```

### Step 2: Buat Model

```php
// app/Models/Faction.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faction extends Model
{
    protected $fillable = ['name'];
    
    public function personnels()
    {
        return $this->hasMany(Personnel::class);
    }
}
```

**Catatan**: Untuk tabel dengan field khusus:
- Rank memiliki `level`, jadi tambahkan ke `$fillable`
- Weapon memiliki `type`, jadi tambahkan ke `$fillable`

```php
// app/Models/Rank.php
class Rank extends Model
{
    protected $fillable = ['name', 'level'];
}
```

### Step 3: Buat Controller

```php
// app/Http/Controllers/FactionController.php
namespace App\Http\Controllers;

use App\Models\Faction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FactionController extends Controller
{
    public function index()
    {
        return Inertia::render('Master/Faction/Index', [
            'factions' => Faction::all(),
            'error' => session('error')
        ]);
    }

    public function create()
    {
        return Inertia::render('Master/Faction/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate(['name' => 'required|string|max:255']);
        Faction::create($validated);
        return redirect()->route('factions.index');
    }

    public function edit(Faction $faction)
    {
        return Inertia::render('Master/Faction/Edit', ['faction' => $faction]);
    }

    public function update(Request $request, Faction $faction)
    {
        $validated = $request->validate(['name' => 'required|string|max:255']);
        $faction->update($validated);
        return redirect()->route('factions.index');
    }

    public function destroy(Faction $faction)
    {
        if ($faction->personnels()->exists()) {
            return redirect()->route('factions.index')->with('error', 'Cannot delete - has personnel');
        }
        $faction->delete();
        return redirect()->route('factions.index');
    }
}
```

### Step 4: Setup Routes

**Dua cara setup routes**:

#### Cara 1: Route Resource (untuk Master Tables)
```php
// routes/web.php
Route::resource('factions', FactionController::class);
Route::resource('ranks', RankController::class);
Route::resource('unit-classes', UnitClassController::class);
Route::resource('weapons', WeaponController::class);
```

#### Cara 2: Custom Routes (untuk Personnel)
```php
// routes/web.php
// Personnel menggunakan root route karena halaman utama ada di /
Route::get('/', [PersonnelController::class, 'index'])->name('personnel.index');
Route::get('/personnel/create', [PersonnelController::class, 'create'])->name('personnel.create');
Route::post('/personnel', [PersonnelController::class, 'store'])->name('personnel.store');
Route::get('/personnel/{personnel}/edit', [PersonnelController::class, 'edit'])->name('personnel.edit');
Route::put('/personnel/{personnel}', [PersonnelController::class, 'update'])->name('personnel.update');
Route::delete('/personnel/{personnel}', [PersonnelController::class, 'destroy'])->name('personnel.destroy');
```

**Route Resource** auto generate 7 routes:
| Method | Action |
|--------|--------|
| GET | index |
| GET | create |
| POST | store |
| GET | show |
| GET | edit |
| PUT | update |
| DELETE | destroy |

### Step 5: Buat Vue Pages

Struktur folder Vue pages:
```
resources/js/Pages/
├── Master/
│   ├── Faction/
│   │   ├── Index.vue
│   │   ├── Create.vue
│   │   └── Edit.vue
│   ├── Rank/
│   │   ├── Index.vue
│   │   ├── Create.vue
│   │   └── Edit.vue
│   ├── UnitClass/
│   │   ├── Index.vue
│   │   ├── Create.vue
│   │   └── Edit.vue
│   └── Weapon/
│       ├── Index.vue
│       ├── Create.vue
│       └── Edit.vue
└── Personnel/
    ├── Index.vue
    ├── Create.vue
    └── Edit.vue
```

#### Index.vue (Master Table)
```vue
<template>
  <AppLayout>
    <div class="container">
      <div class="d-flex justify-content-between mb-4">
        <h2>Factions</h2>
        <Link href="/factions/create" class="btn btn-primary">+ Add</Link>
      </div>
      
      <div v-if="error" class="alert alert-danger">{{ error }}</div>
      
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Name</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="faction in factions" :key="faction.id">
            <td>{{ faction.name }}</td>
            <td>
              <Link :href="`/factions/${faction.id}/edit`" class="btn btn-sm btn-outline-primary">Edit</Link>
              <button @click="deleteFaction(faction.id)" class="btn btn-sm btn-outline-danger">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({ factions: Array, error: String })

const deleteFaction = (id) => {
  if (confirm('Delete this faction?')) router.delete(`/factions/${id}`)
}
</script>
```

#### Create.vue (Master Table)
```vue
<template>
  <AppLayout>
    <div class="card p-4">
      <h3>Add Faction</h3>
      <form @submit.prevent="submit">
        <div class="mb-3">
          <label class="form-label">Name</label>
          <input v-model="form.name" class="form-control" required />
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
        <Link href="/factions" class="btn btn-secondary ms-2">Cancel</Link>
      </form>
    </div>
  </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const form = useForm({ name: '' })
const submit = () => form.post('/factions')
</script>
```

#### Edit.vue (Master Table)
```vue
<template>
  <AppLayout>
    <div class="card p-4">
      <h3>Edit Faction</h3>
      <form @submit.prevent="submit">
        <div class="mb-3">
          <label class="form-label">Name</label>
          <input v-model="form.name" class="form-control" required />
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <Link href="/factions" class="btn btn-secondary ms-2">Cancel</Link>
      </form>
    </div>
  </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({ faction: Object })
const form = useForm({ name: props.faction.name })
const submit = () => form.put(`/factions/${props.faction.id}`)
</script>
```

#### Create.vue (Personnel - with dropdowns)
```vue
<template>
  <AppLayout>
    <div class="card p-4">
      <h3>Add Personnel</h3>
      <form @submit.prevent="submit">
        <div class="mb-3">
          <label class="form-label">Name</label>
          <input v-model="form.name" class="form-control" required />
        </div>
        <div class="mb-3">
          <label class="form-label">Faction</label>
          <select v-model="form.faction_id" class="form-select" required>
            <option value="">Select Faction</option>
            <option v-for="f in factions" :key="f.id" :value="f.id">{{ f.name }}</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Rank</label>
          <select v-model="form.rank_id" class="form-select" required>
            <option value="">Select Rank</option>
            <option v-for="r in ranks" :key="r.id" :value="r.id">{{ r.name }}</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Unit Class</label>
          <select v-model="form.unit_class_id" class="form-select" required>
            <option value="">Select Unit Class</option>
            <option v-for="u in unitClasses" :key="u.id" :value="u.id">{{ u.name }}</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Weapon</label>
          <select v-model="form.weapon_id" class="form-select" required>
            <option value="">Select Weapon</option>
            <option v-for="w in weapons" :key="w.id" :value="w.id">{{ w.name }}</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
        <Link href="/" class="btn btn-secondary ms-2">Cancel</Link>
      </form>
    </div>
  </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({
  factions: Array,
  ranks: Array,
  unitClasses: Array,
  weapons: Array
})

const form = useForm({
  name: '',
  faction_id: '',
  rank_id: '',
  unit_class_id: '',
  weapon_id: ''
})

const submit = () => form.post('/personnel')
</script>
```

### Step 6: Setup Layout

```vue
<!-- resources/js/Layouts/AppLayout.vue -->
<template>
  <div>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container">
        <Link href="/" class="navbar-brand">Personnel SIM</Link>
        <div class="navbar-nav">
          <Link href="/" class="nav-link">Personnel</Link>
          <Link href="/factions" class="nav-link">Factions</Link>
          <Link href="/ranks" class="nav-link">Ranks</Link>
          <Link href="/unit-classes" class="nav-link">Unit Classes</Link>
          <Link href="/weapons" class="nav-link">Weapons</Link>
        </div>
      </div>
    </nav>
    <main class="container py-4">
      <slot />
    </main>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
</script>
```

---

## KONSEP PENTING LAINNYA

### Model Relationships
```php
// belongsTo - Relasi one-to-one / many-to-one
public function faction()
{
    return $this->belongsTo(Faction::class);
}

// hasMany - Relasi one-to-many
public function personnels()
{
    return $this->hasMany(Personnel::class);
}
```

### Personnel Model (dengan semua relasi)
```php
// app/Models/Personnel.php
class Personnel extends Model
{
    protected $fillable = ['name', 'faction_id', 'rank_id', 'unit_class_id', 'weapon_id'];
    
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

### Route Resource
Auto generate 7 routes untuk master tables.

### Error Handling
- Validation: `$request->validate()`
- Flash messages: `->with('error', 'message')`
- Pass to view: `'error' => session('error')`

### Vue Props
- `defineProps()` - Terima data dari controller
- `defineEmits()` - Emit events ke parent

---

## TOOLS PENTING

```bash
# Laravel
php artisan make:model ModelName
php artisan make:controller ControllerName
php artisan make:migration create_table_name
php artisan route:list
php artisan tinker

# Vite/npm
npm run dev    # Development
npm run build  # Production
```

---

## FILE PENTING REFERENCE

| Purpose | Path |
|---------|------|
| Routes | routes/web.php |
| Controllers | app/Http/Controllers/*.php |
| Models | app/Models/*.php |
| Migrations | database/migrations/*.php |
| Vue Pages | resources/js/Pages/**/*.vue |
| Vue Layout | resources/js/Layouts/AppLayout.vue |
| JS Entry | resources/js/app.js |
| Blade Entry | resources/views/app.blade.php |

---

## SELANJUTNYA

Untuk memperdalam, coba:
1. Tambah field baru ke migration (misal: description, status)
2. Tambah validasi baru (unique, min, max)
3. Tambah fitur search/filter
4. Tambah pagination
5. Ubah styling dengan CSS custom