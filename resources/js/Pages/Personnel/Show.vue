<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    personnel: Object,
});

const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};
</script>

<template>
    <AppLayout>
        <div class="container mt-4">
            <!-- Back Button -->
            <Link href="/" class="btn btn-outline-secondary mb-4">
                ← Back to Roster
            </Link>

            <div class="row">
                <!-- Left Column: Personnel Info Card -->
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-dark text-white">
                            <h4 class="mb-0">{{ personnel.name }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center" 
                                     style="width: 100px; height: 100px;">
                                    <span class="text-white fs-1">{{ personnel.name?.charAt(0) }}</span>
                                </div>
                            </div>
                            
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Faction</span>
                                    <span class="fw-bold">{{ personnel.faction?.name }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">  <!-- TAMBAHIN -->
                                    <span class="text-muted">SubGroup</span>
                                    <span class="fw-bold">{{ personnel.sub_group?.name || '-' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Rank</span>
                                    <span class="fw-bold">{{ personnel.rank?.name }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Unit Class</span>
                                    <span class="fw-bold">{{ personnel.unit_class?.name }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Primary Weapon</span>
                                    <span class="fw-bold">{{ personnel.weapon?.name }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="card-footer d-flex gap-2">
                            <Link :href="`/personnel/${personnel.id}/edit`" class="btn btn-primary flex-grow-1">
                                Edit Personnel
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Biography -->
                <div class="col-md-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Biography & Background</h5>
                            <small class="text-white-50">Last updated: {{ formatDate(personnel.updated_at) }}</small>
                        </div>
                        <div class="card-body">
                            <div v-if="personnel.biography" class="biography-content">
                                <p class="mb-0" style="white-space: pre-wrap; line-height: 1.8;">
                                    {{ personnel.biography }}
                                </p>
                            </div>
                            <div v-else class="text-center text-muted py-5">
                                <i class="bi bi-file-text display-4"></i>
                                <p class="mt-2">No biography recorded yet.</p>
                                <Link :href="`/personnel/${personnel.id}/edit`" class="btn btn-outline-primary">
                                    Add Biography
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>