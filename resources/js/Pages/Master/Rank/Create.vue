<template>
  <AppLayout>
    <div class="container mt-5">
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white">
              <h4 class="mb-0">Create Rank</h4>
            </div>
            <div class="card-body bg-light">
              <form @submit.prevent="submit">
                <div class="mb-3">
                  <label class="form-label fw-bold">Name</label>
                  <input v-model="form.name" type="text" class="form-control form-control-lg" required />
                </div>
                <div class="mb-3">
                  <label class="form-label fw-bold">Level</label>
                  <input v-model="form.level" type="number" class="form-control form-control-lg" required min="1" />
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
                    class="form-control form-control-lg" 
                    rows="12" 
                    placeholder="Enter rank description in Markdown..."></textarea>
                  
                  <div 
                    v-else
                    class="border p-3 rounded bg-white" 
                    style="min-height: 280px;"
                    v-html="previewDescription">
                  </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                  <Link href="/ranks" class="btn btn-secondary btn-lg">Cancel</Link>
                  <button type="submit" class="btn btn-primary btn-lg" :disabled="form.processing">Save Rank</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, computed } from 'vue'
import markdownit from 'markdown-it'

const md = markdownit()

const form = useForm({ name: '', level: 1, description: '' })
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
  form.post('/ranks', {
    onSuccess: () => form.reset()
  })
}
</script>