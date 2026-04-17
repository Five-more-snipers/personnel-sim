<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    factions: Array,
    ranks: Array,
    unitClasses: Array,
    weapons: Array,
});

const form = useForm({
    name: '',
    faction_id: '',
    rank_id: '',
    unit_class_id: '',
    weapon_id: '',
});

const submit = () => {
    form.post('/personnel');
};
</script>

<template>
    <AppLayout>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-dark text-white">
                            <h4 class="mb-0">Deploy New Personnel</h4>
                        </div>
                        
                        <div class="card-body bg-light">
                            <form @submit.prevent="submit">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Callsign / Name</label>
                                    <input v-model="form.name" type="text" class="form-control form-control-lg" placeholder="Enter personnel callsign" required>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Faction</label>
                                        <select v-model="form.faction_id" class="form-select" required>
                                            <option disabled value="">Select Faction...</option>
                                            <option v-for="faction in factions" :key="faction.id" :value="faction.id">
                                                {{ faction.name }}
                                            </option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Rank</label>
                                        <select v-model="form.rank_id" class="form-select" required>
                                            <option disabled value="">Select Rank...</option>
                                            <option v-for="rank in ranks" :key="rank.id" :value="rank.id">
                                                {{ rank.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Unit Class</label>
                                        <select v-model="form.unit_class_id" class="form-select" required>
                                            <option disabled value="">Select Class...</option>
                                            <option v-for="uClass in unitClasses" :key="uClass.id" :value="uClass.id">
                                                {{ uClass.name }}
                                            </option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Primary Weapon</label>
                                        <select v-model="form.weapon_id" class="form-select" required>
                                            <option disabled value="">Select Weapon...</option>
                                            <option v-for="weapon in weapons" :key="weapon.id" :value="weapon.id">
                                                {{ weapon.name }} ({{ weapon.type }})
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="d-grid mt-4">
                                    <button type="submit" :disabled="form.processing" class="btn btn-primary btn-lg">
                                        Initialize & Deploy Personnel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>