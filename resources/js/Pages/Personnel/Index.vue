<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    personnels: Object,
    filters: Object,
});

const searchTimeout = ref(null);

// Initialize filters from props (using text filter keys, not ID)
const search = ref(props.filters.search || '');
const factionFilter = ref(props.filters.faction || '');
const unitClassFilter = ref(props.filters.unit_class || '');
const rankFilter = ref(props.filters.rank || '');
const perPage = ref(props.filters.perPage || 10);

// Debounced search for name field
watch(search, (newValue) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 500);
});

// Watch all text filters with debounce
watch([factionFilter, unitClassFilter, rankFilter], () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 500);
});

const applyFilters = () => {
    router.get('/', {
        search: search.value,
        faction: factionFilter.value,
        unit_class: unitClassFilter.value,
        rank: rankFilter.value,
        perPage: perPage.value
    }, { preserveState: true });
};

watch(perPage, () => {
    applyFilters();
});

const dischargePersonnel = (id) => {
    if (confirm('Are you sure you want to discharge this personnel? This action is irreversible.')) {
        router.delete(`/personnel/${id}`);
    }
};
</script>

<template>
    <AppLayout>
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">Active Personnel Roster</h2>
                <Link href="/personnel/create" class="btn btn-primary">+ Deploy New</Link>
            </div>
            <!-- Filter Section: 4 text inputs + Apply button -->
            <div class="row g-2 mb-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Name</label>
                    <input
                        v-model="search"
                        type="text"
                        class="form-control"
                        placeholder="Search name..."
                    />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Faction</label>
                    <input
                        v-model="factionFilter"
                        type="text"
                        class="form-control"
                        placeholder="Filter by faction..."
                    />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Unit Class</label>
                    <input
                        v-model="unitClassFilter"
                        type="text"
                        class="form-control"
                        placeholder="Filter by class..."
                    />
                </div>
                <div class="col-md-2">
                    <label class="form-label">Rank</label>
                    <input
                        v-model="rankFilter"
                        type="text"
                        class="form-control"
                        placeholder="Filter by rank..."
                    />
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button @click="applyFilters" class="btn btn-primary w-100">
                        <i class="bi bi-funnel"></i> Apply
                    </button>
                </div>
            </div>
            
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <table class="table table-hover table-striped mb-0 text-center align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-start">Callsign</th>
                                <th>Faction</th>
                                <th>Rank</th>
                                <th>Class</th>
                                <th>Primary Weapon</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="person in personnels.data" :key="person.id">
                                <td class="fw-bold text-start">{{ person.name }}</td>
                                <td>{{ person.faction?.name }}</td>
                                <td>{{ person.rank?.name }}</td>
                                <td>{{ person.unit_class?.name }}</td>
                                <td>{{ person.weapon?.name }}</td>
                                <td>
                                    <Link :href="`/personnel/${person.id}/edit`" class="btn btn-sm btn-outline-secondary me-2">
                                        Edit
                                    </Link>
                                    <button @click="dischargePersonnel(person.id)" class="btn btn-sm btn-outline-danger">
                                        Discharge
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="personnels.data.length === 0">
                                <td colspan="6" class="text-center py-4 text-muted">No personnel deployed yet.</td>
                            </tr>
                        </tbody>
                    </table> <!-- CLOSE TABLE HERE -->
                    
                    <!-- Pagination Controls -->
                    <div class="d-flex justify-content-between align-items-center mt-3 px-3 py-2 bg-light">
                        <div>
                            <label class="me-2">Rows per page:</label>
                            <select v-model="perPage" class="form-select form-select-sm" style="width: auto; display: inline-block;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                            <span class="ms-2 text-muted">
                                Showing {{ personnels.from || 0 }}-{{ personnels.to || 0 }} of {{ personnels.total }}
                            </span>
                        </div>
                        <div>
                            <button
                                @click="router.get(personnels.prev_page_url, {search: search, perPage}, {preserveState: true})"
                                :disabled="!personnels.prev_page_url"
                                class="btn btn-sm btn-outline-secondary">
                                Previous
                            </button>
                            <span class="mx-2">
                                Page {{ personnels.current_page }} of {{ personnels.last_page }}
                            </span>
                            <button
                                @click="router.get(personnels.next_page_url, {search: search, perPage}, {preserveState: true})"
                                :disabled="!personnels.next_page_url"
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