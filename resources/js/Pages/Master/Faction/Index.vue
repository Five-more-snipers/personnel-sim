<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    factions: Object,
    filters: Object,
    error: String
});

const searchTimeout = ref(null);

const perPage = ref(props.filters.perPage || 10);
watch(perPage, (newPerPage) => {
    router.get('/factions', { search: search.value, perPage: newPerPage }, { preserveState: true });
});

const search = ref(props.filters.search || '');
watch(search, (newValue) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        performSearch();
    }, 500);
});

const performSearch = () => {
    router.get('/factions', { perPage: perPage.value, search: search.value }, { preserveState: true });
};

const deleteFaction = (id) => {
    if (confirm('Are you sure you want to delete this faction?')) {
        router.delete(`/factions/${id}`);
    }
};
</script>

<template>
  <AppLayout>
    <div class="container">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Factions</h2>
        <Link href="/factions/create" class="btn btn-primary">+ Add Faction</Link>
      </div>

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
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="faction in factions.data" :key="faction.id">
                <td class="text-start">{{ faction.id }}</td>
                <td class="text-start">{{ faction.name }}</td>
                <td>
                  <Link :href="`/factions/${faction.id}`" class="btn btn-sm btn-outline-info me-2">View</Link>
                  <Link :href="`/factions/${faction.id}/edit`" class="btn btn-sm btn-outline-secondary me-2">Edit</Link>
                  <button @click="deleteFaction(faction.id)" class="btn btn-sm btn-outline-danger">Delete</button>
                </td>
              </tr>
              <tr v-if="factions.data.length === 0">
                <td colspan="3" class="text-center py-4 text-muted">No factions found.</td>
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
                Showing {{ factions.from || 0 }}-{{ factions.to || 0 }} of {{ factions.total }}
              </span>
            </div>
            <div>
              <button
                @click="router.get(factions.prev_page_url, {search: search, perPage}, {preserveState: true})"
                :disabled="!factions.prev_page_url"
                class="btn btn-sm btn-outline-secondary">
                Previous
              </button>
              <span class="mx-2">
                Page {{ factions.current_page }} of {{ factions.last_page }}
              </span>
              <button
                @click="router.get(factions.next_page_url, {search: search, perPage}, {preserveState: true})"
                :disabled="!factions.next_page_url"
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