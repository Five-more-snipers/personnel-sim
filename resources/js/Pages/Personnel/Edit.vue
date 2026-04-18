<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    personnel: Object, // Menerima data prajurit lama
    factions: Array,
    ranks: Array,
    unitClasses: Array,
    weapons: Array,
    subGroups: Array, // Tambahkan subGroups ke props
});

// useForm otomatis diisi dengan data prajurit yang akan di-edit
const form = useForm({
    name: props.personnel.name,
    biography: props.personnel.biography || '', // Pastikan biography tidak null
    faction_id: props.personnel.faction_id,
    rank_id: props.personnel.rank_id,
    unit_class_id: props.personnel.unit_class_id,
    weapon_id: props.personnel.weapon_id,
    sub_group_id: props.personnel.sub_group_id, // Tambahkan sub_group_id ke form
});

const submit = () => {
    // Menggunakan method PUT untuk proses Update
    form.put(`/personnel/${props.personnel.id}`);
};

const subGroups = computed(() => props.subGroups || []);

const filteredSubGroups = computed(() => {
    if (!form.faction_id) return subGroups.value;
    return subGroups.value.filter(sg => sg.faction_id === form.faction_id);
});

watch(() => form.faction_id, () => {
    form.sub_group_id = null;
});
</script>

<template>
    <AppLayout>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-secondary text-white">
                            <h4 class="mb-0">Reassign Personnel: {{ personnel.name }}</h4>
                        </div>
                        
                        <div class="card-body bg-light">
                            <form @submit.prevent="submit">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Callsign / Name</label>
                                    <input v-model="form.name" type="text" class="form-control form-control-lg" required>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Faction</label>
                                        <select v-model="form.faction_id" class="form-select" required>
                                            <option v-for="faction in factions" :key="faction.id" :value="faction.id">
                                                {{ faction.name }}
                                            </option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">SubGroup</label>
                                        <select v-model="form.sub_group_id" class="form-select">
                                            <option :value="null">None</option>
                                            <option v-for="sg in filteredSubGroups" :key="sg.id" :value="sg.id">
                                                {{ sg.name }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Rank</label>
                                        <select v-model="form.rank_id" class="form-select" required>
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
                                            <option v-for="uClass in unitClasses" :key="uClass.id" :value="uClass.id">
                                                {{ uClass.name }}
                                            </option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Primary Weapon</label>
                                        <select v-model="form.weapon_id" class="form-select" required>
                                            <option v-for="weapon in weapons" :key="weapon.id" :value="weapon.id">
                                                {{ weapon.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">Biography</label>
                                    <textarea v-model="form.biography" class="form-control" rows="5" 
                                    placeholder="Enter personnel biography..."></textarea>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="submit" :disabled="form.processing" class="btn btn-success btn-lg px-5">
                                        Save Changes
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