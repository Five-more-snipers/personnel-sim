<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    factions: Array,
});

const form = useForm({
    name: '',
    faction_id: '',
});

const submit = () => {
    form.post('/sub-groups');
};
</script>

<template>
    <AppLayout>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Create SubGroup</h4>
                        </div>
                        <div class="card-body bg-light">
                            <form @submit.prevent="submit">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Name</label>
                                    <input v-model="form.name" type="text" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Faction</label>
                                    <select v-model="form.faction_id" class="form-select" required>
                                        <option value="" disabled>Select Faction</option>
                                        <option v-for="faction in factions" :key="faction.id" :value="faction.id">
                                            {{ faction.name }}
                                        </option>
                                    </select>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <Link href="/sub-groups" class="btn btn-secondary">Cancel</Link>
                                    <button type="submit" :disabled="form.processing" class="btn btn-success">Create</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>