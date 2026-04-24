<template>
  <AppLayout>
    <div class="container">
      <h2 class="fw-bold mb-4">Create Weapon</h2>
      <form @submit.prevent="submit" class="card shadow-sm border-0 p-4" style="max-width: 600px;">
        <div class="mb-3">
          <label class="form-label">Name</label>
          <input v-model="form.name" type="text" class="form-control" required />
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Category</label>
          <input v-model="form.category" type="text" class="form-control" placeholder="e.g., Assault Rifle, Sniper, Pistol">
        </div>
        
        <div class="mb-3">
          <label class="form-label">Description (Markdown supported)</label>
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
            placeholder="Enter weapon description in Markdown..."></textarea>
          
          <div 
            v-else
            class="border p-3 rounded bg-white" 
            style="min-height: 250px;"
            v-html="previewDescription">
          </div>
        </div>
        
        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary" :disabled="form.processing">Save</button>
          <Link href="/weapons" class="btn btn-secondary">Cancel</Link>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, computed } from 'vue'
import markdownit from 'markdown-it'

const md = markdownit()

const form = useForm({ name: '', category: '', description: '' })
const showPreview = ref(false)

const previewDescription = computed(() => {
  if (!form.description) return ''
  return md.render(form.description)
})

const insertFormat = (before, after = '', placeholder = '') => {
  const textarea = document.getElementById('description-textarea')
  if (!textarea) return
  
  const start = textarea.selectionStart
  const end = textarea.selectionEnd
  const text = textarea.value
  const selectedText = text.substring(start, end) || placeholder
  
  const newText = text.substring(0, start) + before + selectedText + after + text.substring(end)
  form.description = newText
}

const addHeading = (level) => {
  const prefix = '#'.repeat(level) + ' '
  insertFormat(prefix, '', 'Heading')
}

const submit = () => {
  form.post('/weapons', {
    onSuccess: () => form.reset()
  })
}
</script>