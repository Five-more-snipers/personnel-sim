<template>
  <AppLayout>
    <div class="container">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Ranks</h2>
        <Link href="/ranks/create" class="btn btn-primary">+ Add Rank</Link>
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
                <th class="text-start">Level</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="rank in ranks" :key="rank.id">
                <td class="text-start">{{ rank.id }}</td>
                <td class="text-start">{{ rank.name }}</td>
                <td class="text-start">{{ rank.level }}</td>
                <td>
                  <Link :href="`/ranks/${rank.id}`" class="btn btn-sm btn-outline-info me-2">View</Link>
                  <Link :href="`/ranks/${rank.id}/edit`" class="btn btn-sm btn-outline-secondary me-2">Edit</Link>
                  <button @click="deleteRank(rank.id)" class="btn btn-sm btn-outline-danger">Delete</button>
                </td>
              </tr>
              <tr v-if="ranks.length === 0">
                <td colspan="4" class="text-center py-4 text-muted">No ranks found.</td>
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

defineProps({ ranks: Array, error: String })

const deleteRank = (id) => {
  if (confirm('Delete this rank?')) {
    router.delete(`/ranks/${id}`)
  }
}
</script>