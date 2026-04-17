<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';

defineProps({
    personnels: Array
});

// Fungsi untuk konfirmasi keamanan sebelum menghapus personel
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
                                <th>Actions</th> </tr>
                        </thead>
                        <tbody>
                            <tr v-for="person in personnels" :key="person.id">
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
                            <tr v-if="personnels.length === 0">
                                <td colspan="6" class="text-center py-4 text-muted">No personnel deployed yet.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>