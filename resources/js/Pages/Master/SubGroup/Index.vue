<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    subGroups: Object,
    filters: Object,
    error: String
});

const searchTimeout = ref(null);

const perPage = ref(props.filters.perPage || 10);
watch(perPage, (newPerPage) => {
    router.get('/sub-groups', { search: search.value, faction: factionFilter.value, perPage: newPerPage }, { preserveState: true });
});

const search = ref(props.filters.search || '');
const factionFilter = ref(props.filters.faction || '');

watch(search, (newValue) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        performSearch();
    }, 500);
});

watch(factionFilter, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        performSearch();
    }, 500);
});

const performSearch = () => {
    router.get('/sub-groups', { perPage: perPage.value, search: search.value, faction: factionFilter.value }, { preserveState: true });
};

const deleteSubGroup = (id) => {
    if (confirm('Are you sure you want to delete this sub-group?')) {
        router.delete(`/sub-groups/${id}`);
    }
};
</script>

<template>
  <AppLayout>
    <div class="container">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">SubGroups</h2>
        <Link href="/sub-groups/create" class="btn btn-primary">+ Add SubGroup</Link>
      </div>

      <div v-if="error" class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ error }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>

      <div class="row g-2 mb-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Name</label>
            <input v-model="search" type="text" class="form-control" placeholder="Search name..." />
        </div>
        <div class="col-md-4">
            <label class="form-label">Faction</label>
            <input v-model="factionFilter" type="text" class="form-control" placeholder="Filter by faction..." />
        </div>
        <div class="col-md-2">
            <button @click="performSearch" class="btn btn-primary w-100">Filter</button>
        </div>
      </div>

      <div class="card shadow-sm border-0">
        <div class="card-body p-0">
          <table class="table table-hover table-striped mb-0 text-center align-middle">
            <thead class="table-dark">
              <tr>
                <th class="text-start">ID</th>
                <th class="text-start">Name</th>
                <th>Faction</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="subGroup in subGroups.data" :key="subGroup.id">
                <td class="text-start">{{ subGroup.id }}</td>
                <td class="text-start">{{ subGroup.name }}</td>
                <td>{{ subGroup.faction?.name }}</td>
                <td>
                  <Link :href="`/sub-groups/${subGroup.id}/edit`" class="btn btn-sm btn-outline-secondary me-2">Edit</Link>
                  <button @click="deleteSubGroup(subGroup.id)" class="btn btn-sm btn-outline-danger">Delete</button>
                </td>
              </tr>
              <tr v-if="subGroups.data.length === 0">
                <td colspan="4" class="text-center py-4 text-muted">No sub-groups found.</td>
              </tr>
            </tbody>
          </table>

          <div class="d-flex justify-content-between align-items-center mt-3 px-3 py-2 bg-light">
            <div>
              <label class="me-2">Rows per page:</label>
              <select v-model="perPage" class="form-select form-select-sm" style="width: auto; display: inline-block;">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
              </select>
              <span class="ms-2 text-muted">
                Showing {{ subGroups.from || 0 }}-{{ subGroups.to || 0 }} of {{ subGroups.total }}
              </span>
            </div>
            <div>
              <button @click="router.get(subGroups.prev_page_url, {search: search, faction: factionFilter, perPage}, {preserveState: true})" :disabled="!subGroups.prev_page_url" class="btn btn-sm btn-outline-secondary">Previous</button>
              <span class="mx-2">Page {{ subGroups.current_page }} of {{ subGroups.last_page }}</span>
              <button @click="router.get(subGroups.next_page_url, {search: search, faction: factionFilter, perPage}, {preserveState: true})" :disabled="!subGroups.next_page_url" class="btn btn-sm btn-outline-secondary">Next</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>