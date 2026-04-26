<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import markdownit from 'markdown-it';

const md = markdownit();

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

const renderedBiography = computed(() => {
    if (!props.personnel.biography) return '';
    return md.render(props.personnel.biography);
});
</script>

<template>
    <AppLayout>
        <div class="container mt-4">
            <div class="row">
                <!-- Left Column: Biography -->
                <div class="col-md-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Biography & Background</h5>
                            <div class="d-flex gap-2 align-items-center">
                                <small class="text-white-50">{{ formatDate(personnel.updated_at) }}</small>
                                <Link :href="`/personnel/${personnel.id}/edit`" class="btn btn-sm btn-light">
                                    <i class="bi bi-pencil"></i> Edit Bio
                                </Link>
                                <Link href="/" class="btn btn-sm btn-outline-light">
                                    ← Back
                                </Link>
                            </div>
                        </div>
                        <div class="card-body">
                            <div v-if="personnel.biography" class="biography-content" v-html="renderedBiography">
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

                <!-- Right Column: Personnel Info & Statistics -->
                <div class="col-md-4">
                    <!-- Personnel Info Card -->
                    <div class="card shadow-sm border-0 mb-3">
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
                                 <li class="list-group-item d-flex justify-content-between">
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
                                      <span class="fw-bold">
                                        {{ personnel.weapon?.name }} {{ personnel.weapon?.inspiration_source ? '[' + personnel.weapon?.inspiration_source + ']' : '' }}
                                      </span>
                                  </li>
                             </ul>
                         </div>
                         <div class="card-footer d-flex gap-2">
                             <Link :href="`/personnel/${personnel.id}/edit`" class="btn btn-primary flex-grow-1">
                                 Edit Personnel
                             </Link>
                         </div>
                     </div>

                     <!-- Statistics Card -->
                     <div class="card shadow-sm border-0">
                         <div class="card-header bg-secondary text-white">
                             <h5 class="mb-0">Statistics</h5>
                         </div>
                         <div class="card-body">
                             <ul class="list-group list-group-flush">
                                 <li class="list-group-item d-flex justify-content-between">
                                     <span>Personnel ID</span>
                                     <span class="fw-bold">#{{ personnel.id }}</span>
                                 </li>
                                 <li class="list-group-item d-flex justify-content-between">
                                     <span>Created</span>
                                     <span class="fw-bold">{{ formatDate(personnel.created_at) }}</span>
                                 </li>
                                 <li class="list-group-item d-flex justify-content-between">
                                     <span>Last Updated</span>
                                     <span class="fw-bold">{{ formatDate(personnel.updated_at) }}</span>
                                 </li>
                             </ul>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </AppLayout>
 </template>

<style>
.biography-content h1, .biography-content h2, .biography-content h3 {
    margin-top: 1rem;
    margin-bottom: 0.5rem;
    font-weight: bold;
}
.biography-content h1 { font-size: 1.5rem; }
.biography-content h2 { font-size: 1.25rem; }
.biography-content h3 { font-size: 1.1rem; }
.biography-content p {
    margin-bottom: 1rem;
    line-height: 1.6;
}
.biography-content ul, .biography-content ol {
    margin-left: 1.5rem;
    margin-bottom: 1rem;
}
.biography-content li {
    margin-bottom: 0.25rem;
}
.biography-content strong {
    font-weight: bold;
}
.biography-content em {
    font-style: italic;
}
.biography-content code {
    background-color: #f4f4f4;
    padding: 0.1rem 0.3rem;
    border-radius: 3px;
    font-family: monospace;
}
.biography-content pre {
    background-color: #f4f4f4;
    padding: 1rem;
    border-radius: 5px;
    overflow-x: auto;
}
.biography-content blockquote {
    border-left: 3px solid #ccc;
    padding-left: 1rem;
    margin-left: 0;
    font-style: italic;
    color: #666;
}
</style>
