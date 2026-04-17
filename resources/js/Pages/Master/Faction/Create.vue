<template>
  <AppLayout>
    <div class="container">
      <h2 class="fw-bold mb-4">Create Faction</h2>
      <form @submit.prevent="submit" class="card shadow-sm border-0 p-4" style="max-width: 500px;">
        <div class="mb-3">
          <label class="form-label">Name</label>
          <input v-model="form.name" type="text" class="form-control" required />
          <span v-if="errors.name" class="text-danger small">{{ errors.name }}</span>
        </div>
        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary">Save</button>
          <Link href="/factions" class="btn btn-secondary">Cancel</Link>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const form = useForm({ name: '' })
const errors = {}

const submit = () => {
  form.post('/factions', {
    onSuccess: () => form.reset()
  })
}
</script>