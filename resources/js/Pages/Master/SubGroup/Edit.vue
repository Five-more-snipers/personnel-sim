<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed } from 'vue';
import markdownit from 'markdown-it';

const md = markdownit();

const props = defineProps({
    subGroup: Object,
    factions: Array,
});

const form = useForm({
    name: props.subGroup.name,
    faction_id: props.subGroup.faction_id,
    description: props.subGroup.description || '',
});
const showPreview = ref(false);

const previewDescription = computed(() => {
    if (!form.description) return '';
    return md.render(form.description);
});

const insertFormat = (before, after = '', placeholder = '') => {
    const textarea = document.getElementById('description-textarea');
    if (!textarea) return;
    
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;
    const selectedText = text.substring(start, end) || placeholder;
    
    const newText = text.substring(0, start) + before + selectedText + after + text.substring(end);
    form.description = newText;
};

const addHeading = (level) => {
    const prefix = '#'.repeat(level) + ' ';
    insertFormat(prefix, '', 'Heading');
};

const submit = () => {
    form.put(`/sub-groups/${props.subGroup.id}`);
};
</script>

<template>
    <AppLayout>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-warning text-dark">
                            <h4 class="mb-0">Edit SubGroup</h4>
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
                                        <option v-for="faction in factions" :key="faction.id" :value="faction.id">
                                            {{ faction.name }}
                                        </option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Description (Markdown supported)</label>
                                    <div class="mb-2 d-flex gap-2 align-items-center">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-secondary" @click="insertFormat('**', '**', 'bold text')">
                                                <strong>B</strong>
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" @click="insertFormat('*', '*', 'italic text')">
                                                <em>I</em>
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" @click="insertFormat('~~', '~~', 'strikethrough')">
                                                <s>S</s>
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" @click="addHeading(1)">
                                                H1
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" @click="addHeading(2)">
                                                H2
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" @click="insertFormat('\n- ', '', 'list item')">
                                                <i class="bi bi-list-ul"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" @click="insertFormat('[', '](url)', 'link text')">
                                                <i class="bi bi-link"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" @click="insertFormat('`', '`', 'code')">
                                                &lt;/&gt;
                                            </button>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" @click="showPreview = !showPreview">
                                            {{ showPreview ? 'Edit' : 'Preview' }}
                                        </button>
                                    </div>
                                  
                                    <textarea 
                                        v-if="!showPreview"
                                        id="description-textarea"
                                        v-model="form.description" 
                                        class="form-control" 
                                        rows="10" 
                                        placeholder="Enter sub-group description in Markdown..."></textarea>
                                  
                                    <div 
                                        v-else
                                        class="border p-3 rounded bg-white" 
                                        style="min-height: 250px;"
                                        v-html="previewDescription">
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-4">
                                    <Link href="/sub-groups" class="btn btn-secondary">Cancel</Link>
                                    <button type="submit" :disabled="form.processing" class="btn btn-success">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>