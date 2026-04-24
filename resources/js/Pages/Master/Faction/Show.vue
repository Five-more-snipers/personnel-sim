<template>
  <AppLayout>
    <div class="container mt-4">
      <div class="row">
        <div class="col-md-8">
          <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
              <h4 class="mb-0">{{ faction.name }}</h4>
              <div class="d-flex gap-2">
                <Link :href="`/factions/${faction.id}/edit`" class="btn btn-sm btn-light">
                  <i class="bi bi-pencil"></i> Edit
                </Link>
                <Link href="/factions" class="btn btn-sm btn-outline-light">
                  ← Back
                </Link>
              </div>
            </div>
            <div class="card-body">
              <div v-if="faction.description" class="biography-content" v-html="renderedDescription">
              </div>
              <div v-else class="text-center text-muted py-5">
                <i class="bi bi-file-text display-4"></i>
                <p class="mt-2">No description yet.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card shadow-sm border-0">
            <div class="card-header bg-secondary text-white">
              <h5 class="mb-0">Statistics</h5>
            </div>
            <div class="card-body">
              <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                  <span>Total Personnel</span>
                  <span class="fw-bold">{{ faction.personnels_count || 0 }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                  <span>Total SubGroups</span>
                  <span class="fw-bold">{{ faction.sub_groups_count || 0 }}</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import markdownit from 'markdown-it'

const md = markdownit()

const props = defineProps({
  faction: Object,
})

const renderedDescription = computed(() => {
  if (!props.faction.description) return ''
  return md.render(props.faction.description)
})
</script>

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
