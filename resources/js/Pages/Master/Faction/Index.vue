<template>
  <AppLayout>
    <div class="container">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Factions</h2>
        <Link href="/factions/create" class="btn btn-primary">+ Add Faction</Link>
      </div>

      <div v-if="error" class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ error }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>

      <div class="card shadow-sm border-0">
        <div class="card-body p-0">
          <table class="table table-hover table-striped mb-0 text-center align-middle">
            <thead class="table-dark">
              <tr>
                <th class="text-start">ID</th>
                <th class="text-start">Name</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="faction in factions" :key="faction.id">
                <td class="text-start">{{ faction.id }}</td>
                <td class="text-start">{{ faction.name }}</td>
                <td>
                  <Link :href="`/factions/${faction.id}/edit`" class="btn btn-sm btn-outline-secondary me-2">Edit</Link>
                  <button @click="deleteFaction(faction.id)" class="btn btn-sm btn-outline-danger">Delete</button>
                </td>
              </tr>
              <tr v-if="factions.length === 0">
                <td colspan="3" class="text-center py-4 text-muted">No factions found.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({ factions: Array, error: String })

const deleteFaction = (id) => {
  if (confirm('Delete this faction?')) {
    router.delete(`/factions/${id}`)
  }
}
</script>