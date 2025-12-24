<template>
  <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-md rounded-lg p-6">
      <h3 class="text-lg font-semibold mb-4">Edit Profile</h3>

      <form @submit.prevent="submit" class="space-y-4">
        <!-- Name -->
        <div>
          <label class="block text-sm font-medium mb-1">Name</label>
          <input v-model="form.name" class="input" required />
        </div>

        <!-- Email -->
        <div>
          <label class="block text-sm font-medium mb-1">Email</label>
          <input v-model="form.email" type="email" class="input" required />
        </div>

        <hr />

        <!-- Current password -->
        <div>
          <label class="block text-sm font-medium mb-1">Current password</label>
          <input v-model="form.current_password" type="password" class="input" />
        </div>

        <!-- New password -->
        <div>
          <label class="block text-sm font-medium mb-1">New password</label>
          <input v-model="form.password" type="password" class="input" />
        </div>

        <!-- Confirm -->
        <div>
          <label class="block text-sm font-medium mb-1">Confirm password</label>
          <input
            v-model="form.password_confirmation"
            type="password"
            class="input"
          />
        </div>

        <div class="flex justify-end gap-2 pt-2">
          <button type="button" @click="$emit('close')">
            Cancel
          </button>
          <button
            type="submit"
            class="bg-blue-600 text-white px-4 py-2 rounded"
            :disabled="loading"
          >
            Save
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import api from '../../services/api'

const props = defineProps({
  user: Object
})

const emit = defineEmits(['close', 'updated'])

const loading = ref(false)

const form = ref({
  name: props.user.name,
  email: props.user.email,

  current_password: '',
  password: '',
  password_confirmation: '',
})

const submit = async () => {
  loading.value = true
  try {
    const res = await api.put('/users/me', form.value)
    emit('updated', res.data.data)
    emit('close')
  } catch (e) {
    alert(e.response?.data?.message || 'Update failed')
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.input {
  @apply w-full border rounded px-3 py-2;
}
</style>
