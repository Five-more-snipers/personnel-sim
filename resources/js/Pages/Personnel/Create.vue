<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed, ref, watch } from 'vue';
import markdownit from 'markdown-it';

const md = markdownit();

const props = defineProps({
    factions: Array,
    subGroups: Array,
    ranks: Array,
    unitClasses: Array,
    weapons: Array,
});

const form = useForm({
    name: '',
    biography: '',
    faction_id: '',
    sub_group_id: '',
    rank_id: '',
    unit_class_id: '',
    weapon_id: '',
});

const showPreview = ref(false);

const previewBiography = computed(() => {
    if (!form.biography) return '';
    return md.render(form.biography);
});

const insertFormat = (before, after = '', placeholder = '') => {
    const textarea = document.getElementById('biography-textarea');
    if (!textarea) return;
    
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;
    const selectedText = text.substring(start, end) || placeholder;
    
    const newText = text.substring(0, start) + before + selectedText + after + text.substring(end);
    form.biography = newText;
};

const addHeading = (level) => {
    const prefix = '#'.repeat(level) + ' ';
    insertFormat(prefix, '', 'Heading');
};

const submit = () => {
    form.post('/personnel');
};

const subGroups = computed(() => props.subGroups || []);
const filteredSubGroups = computed(() => {
    if (!form.faction_id) return [];
    return subGroups.value.filter(subGroup => subGroup.faction_id === form.faction_id);
});
watch(() => form.faction_id, () => {
    form.sub_group_id = '';
}); 

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
                                    
                                    <div class="col-md-6">  <!-- TAMBAHIN INI -->
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
                                               {{ weapon.name }} {{ weapon.inspiration_source ? '(' + weapon.inspiration_source + ')' : '' }}
                                             </option>
                                         </select>
                                     </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">Biography (Markdown supported)</label>
                                    <div class="mb-2 d-flex gap-2 align-items-center">
                                        <div class="btn-group btn-group-sm">
                                            <button 
                                                type="button" 
                                                class="btn btn-outline-secondary" 
                                                title="Bold"
                                                @click="insertFormat('**', '**', 'bold text')">
                                                <strong>B</strong>
                                            </button>
                                            <button 
                                                type="button" 
                                                class="btn btn-outline-secondary" 
                                                title="Italic"
                                                @click="insertFormat('*', '*', 'italic text')">
                                                <em>I</em>
                                            </button>
                                            <button 
                                                type="button" 
                                                class="btn btn-outline-secondary" 
                                                title="Strikethrough"
                                                @click="insertFormat('~~', '~~', 'strikethrough')">
                                                <s>S</s>
                                            </button>
                                            <button 
                                                type="button" 
                                                class="btn btn-outline-secondary" 
                                                title="Heading 1"
                                                @click="addHeading(1)">
                                                H1
                                            </button>
                                            <button 
                                                type="button" 
                                                class="btn btn-outline-secondary" 
                                                title="Heading 2"
                                                @click="addHeading(2)">
                                                H2
                                            </button>
                                            <button 
                                                type="button" 
                                                class="btn btn-outline-secondary" 
                                                title="List"
                                                @click="insertFormat('\n- ', '', 'list item')">
                                                <i class="bi bi-list-ul"></i>
                                            </button>
                                            <button 
                                                type="button" 
                                                class="btn btn-outline-secondary" 
                                                title="Link"
                                                @click="insertFormat('[', '](url)', 'link text')">
                                                <i class="bi bi-link"></i>
                                            </button>
                                            <button 
                                                type="button" 
                                                class="btn btn-outline-secondary" 
                                                title="Code"
                                                @click="insertFormat('`', '`', 'code')">
                                                &lt;/&gt;
                                            </button>
                                        </div>
                                        <button 
                                            type="button" 
                                            @click="showPreview = !showPreview"
                                            class="btn btn-sm btn-outline-secondary">
                                            {{ showPreview ? 'Edit' : 'Preview' }}
                                        </button>
                                    </div>
                                    
                                    <textarea 
                                        v-if="!showPreview"
                                        id="biography-textarea"
                                        v-model="form.biography" 
                                        class="form-control" 
                                        rows="10" 
                                        placeholder="Enter personnel biography in Markdown..."></textarea>
                                    
                                    <div 
                                        v-else
                                        class="border p-3 rounded bg-white" 
                                        style="min-height: 250px;"
                                        v-html="previewBiography">
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