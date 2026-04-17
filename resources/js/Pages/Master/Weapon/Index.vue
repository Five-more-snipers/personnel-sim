<template>
  <AppLayout>
    <div class="container">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Weapons</h2>
        <Link href="/weapons/create" class="btn btn-primary">+ Add Weapon</Link>
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
                <th class="text-start">Type</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="weapon in weapons" :key="weapon.id">
                <td class="text-start">{{ weapon.id }}</td>
                <td class="text-start">{{ weapon.name }}</td>
                <td class="text-start">{{ weapon.type }}</td>
                <td>
                  <Link :href="`/weapons/${weapon.id}/edit`" class="btn btn-sm btn-outline-secondary me-2">Edit</Link>
                  <button @click="deleteWeapon(weapon.id)" class="btn btn-sm btn-outline-danger">Delete</button>
                </td>
              </tr>
              <tr v-if="weapons.length === 0">
                <td colspan="4" class="text-center py-4 text-muted">No weapons found.</td>
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

defineProps({ weapons: Array, error: String })

const deleteWeapon = (id) => {
  if (confirm('Delete this weapon?')) {
    router.delete(`/weapons/${id}`)
  }
}
</script>