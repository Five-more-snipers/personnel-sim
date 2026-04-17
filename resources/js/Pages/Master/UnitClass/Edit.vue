<template>
  <AppLayout>
    <div class="container">
      <h2 class="fw-bold mb-4">Edit Unit Class</h2>
      <form @submit.prevent="submit" class="card shadow-sm border-0 p-4" style="max-width: 500px;">
        <div class="mb-3">
          <label class="form-label">Name</label>
          <input v-model="form.name" type="text" class="form-control" required />
        </div>
        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary">Update</button>
          <Link href="/unit-classes" class="btn btn-secondary">Cancel</Link>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({ unitClass: Object })
const form = useForm({ name: props.unitClass.name })

const submit = () => {
  form.put(`/unit-classes/${props.unitClass.id}`)
}
</script>