<script setup>
  import AppLayout from '@/Layouts/AppLayout.vue';
  import { Link, router } from '@inertiajs/vue3';
  import { ref, watch } from 'vue';

  const props = defineProps({
      weapons: Object,
      filters: Object,
      error: String
  });

  const searchTimeout = ref(null);

  const perPage = ref(props.filters.perPage || 10);
  watch(perPage, (newPerPage) => {
      router.get('/weapons', { search: search.value, perPage: newPerPage }, { preserveState: true });
  });

  const search = ref(props.filters.search || '');
  const categoryFilter = ref(props.filters.category || '');

  watch(search, (newValue) => {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
          performSearch();
      }, 500);
  });

  watch(categoryFilter, () => {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
          performSearch();
      }, 500);
  });

  const performSearch = () => {
      router.get('/weapons', { search: search.value, category: categoryFilter.value, perPage: perPage.value }, { preserveState: true });
  };

  const deleteWeapon = (id) => {
      if (confirm('Are you sure you want to delete this weapon?')) {
          router.delete(`/weapons/${id}`);
      }
  };
</script>

<template>
  <AppLayout>
    <div class="container">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Weapons</h2>
        <Link href="/weapons/create" class="btn btn-primary">+ Add Weapon</Link>
      </div>
      <div class="mb-3">
          <div class="row g-2">
            <div class="col-md-4">
              <input
                  v-model="search"
                  type="text"
                  class="form-control"
                  placeholder="Search weapon name..."
              />
            </div>
            <div class="col-md-3">
                <input v-model="categoryFilter" type="text" class="form-control" placeholder="Filter by category..." />
            </div>
            <div class="col-md-2">
                <button @click="performSearch" class="btn btn-primary w-100" type="button">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
          </div>
      </div>
      <!-- Error Alert -->
      <div v-if="error" class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ error }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>

      <div class="card shadow-sm border-0">
        <div class="card-body p-0">
          <table class="table table-hover table-striped mb-0 text-center align-middle">
             <thead class="table-dark">
               <tr>
                 <th class="text-start">ID</th>
                 <th class="text-start">Name</th>
                 <th>Category</th>
                 <th>Inspiration/Source</th>
                 <th>Actions</th>
               </tr>
             </thead>
            <tbody>
               <tr v-for="weapon in weapons.data" :key="weapon.id">
                 <td class="text-start">{{ weapon.id }}</td>
                 <td class="text-start">{{ weapon.name }}</td>
                 <td>{{ weapon.category || '-' }}</td>
                 <td>{{ weapon.inspiration_source || '-' }}</td>
                 <td>
                   <Link :href="`/weapons/${weapon.id}`" class="btn btn-sm btn-outline-info me-2">View</Link>
                   <Link :href="`/weapons/${weapon.id}/edit`" class="btn btn-sm btn-outline-secondary me-2">Edit</Link>
                   <button @click="deleteWeapon(weapon.id)" class="btn btn-sm btn-outline-danger">Delete</button>
                 </td>
               </tr>
              <tr v-if="weapons.data.length === 0">
                <td colspan="4" class="text-center py-4 text-muted">No weapons found.</td>
              </tr>
            </tbody>
          </table>
          <!-- Pagination -->
          <div class="d-flex justify-content-between align-items-center mt-3 px-3 py-2 bg-light">
            <div>
              <label class="me-2">Rows per page:</label>
              <select v-model="perPage" class="form-select form-select-sm" style="width: auto; display: inline-block;">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
              </select>
              <span class="ms-2 text-muted">
                Showing {{ weapons.from || 0 }}-{{ weapons.to || 0 }} of {{ weapons.total }}
              </span>
            </div>
            <div>
              <button
                @click="router.get(weapons.prev_page_url, {search: search, perPage}, {preserveState: true})"
                :disabled="!weapons.prev_page_url"
                class="btn btn-sm btn-outline-secondary">
                Previous
              </button>
              <span class="mx-2">
                Page {{ weapons.current_page }} of {{ weapons.last_page }}
              </span>
              <button
                @click="router.get(weapons.next_page_url, {search: search, perPage}, {preserveState: true})"
                :disabled="!weapons.next_page_url"
                class="btn btn-sm btn-outline-secondary">
                Next
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
