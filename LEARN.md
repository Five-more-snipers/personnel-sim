# Panduan Pembelajaran Laravel-Vue-Bootstrap

Dokumen ini berisi step-by-step dan konsep yang harus dipahami untuk mereplikasi project ini.

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
# Buat migration
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
            return redirect()->route('factions.index')->with('error', 'Cannot delete');
        }
        $faction->delete();
        return redirect()->route('factions.index');
    }
}
```

### Step 4: Setup Routes

```php
// routes/web.php
Route::resource('factions', FactionController::class);
```

### Step 5: Buat Vue Pages

Buat folder: `resources/js/Pages/Master/Faction/`

#### Index.vue
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
        <tr v-for="faction in factions" :key="faction.id">
          <td>{{ faction.name }}</td>
          <td>
            <Link :href="`/factions/${faction.id}/edit`">Edit</Link>
            <button @click="deleteFaction(faction.id)">Delete</button>
          </td>
        </tr>
      </table>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({ factions: Array, error: String })

const deleteFaction = (id) => {
  if (confirm('Delete?')) router.delete(`/factions/${id}`)
}
</script>
```

#### Create.vue
```vue
<template>
  <AppLayout>
    <form @submit.prevent="submit" class="card p-4">
      <input v-model="form.name" class="form-control" required />
      <button type="submit" class="btn btn-primary">Save</button>
    </form>
  </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
const form = useForm({ name: '' })
const submit = () => form.post('/factions')
</script>
```

#### Edit.vue
```vue
<template>
  <AppLayout>
    <form @submit.prevent="submit" class="card p-4">
      <input v-model="form.name" class="form-control" required />
      <button type="submit" class="btn btn-primary">Update</button>
    </form>
  </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
const props = defineProps({ faction: Object })
const form = useForm({ name: props.faction.name })
const submit = () => form.put(`/factions/${props.faction.id}`)
</script>
```

### Step 6: Setup Layout

```vue
<!-- resources/js/Layouts/AppLayout.vue -->
<template>
  <div>
    <nav>...</nav>
    <main><slot /></main>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
</script>
```

---

## KONSEP PENTING LAINNYA

### Model Relationships
- `belongsTo` - Relasi one-to-one / many-to-one
- `hasMany` - Relasi one-to-many

### Route Resource
Auto generate 7 routes:
| Method | Action |
|--------|--------|
| GET | index |
| GET | create |
| POST | store |
| GET | show |
| GET | edit |
| PUT | update |
| DELETE | destroy |

### Error Handling
- Validation: `$request->validate()`
- Flash messages: `->with('error', 'message')`
- Pass to view: `'error' => session('error')`

### Vue Props
- `defineProps()` - Terima data dari controller
- `defineEmits()` - Emit events ke parent
- `provide/inject` - Share data globally

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