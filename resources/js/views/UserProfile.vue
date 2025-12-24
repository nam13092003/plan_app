<template>
  <div>
    <!-- Loading -->
    <div v-if="loading" class="text-center py-10">
      Loading...
    </div>

    <!-- Error -->
    <div v-else-if="error" class="text-red-500 text-center py-10">
      {{ error }}
    </div>

    <!-- Content -->
    <div v-else-if="user" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left -->
      <div class="lg:col-span-1 space-y-6">
        <ProfileCard :user="user" />

        <button
          class="w-full bg-blue-600 text-white py-2 rounded"
          @click="showEdit = true"
        >
          Edit Profile
        </button>
      </div>

      <!-- Right -->
      <div class="lg:col-span-2 space-y-6">
        <StatsGauge
          :hours="user.total_logged_in_hours"
          :max-hours="100"
        />
      </div>
    </div>

    <!-- Edit Modal -->
    <EditProfileModal
      v-if="showEdit"
      :user="user"
      @close="closeEdit"
      @updated="reloadUser"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api'

import ProfileCard from '../components/Profile/ProfileCard.vue'
import StatsGauge from '../components/Profile/StatsGauge.vue'
import EditProfileModal from '../components/Profile/EditProfileModal.vue'

const user = ref(null)
const loading = ref(false)
const error = ref(null)
const showEdit = ref(false)

const fetchUser = async () => {
  loading.value = true
  error.value = null

  try {
    const res = await api.get('/users/me')
    user.value = res.data.data
  } catch (err) {
    error.value = 'Failed to load user profile.'
  } finally {
    loading.value = false
  }
}

const closeEdit = () => {
  showEdit.value = false
}

const reloadUser = async () => {
  showEdit.value = false
  await fetchUser()
}

onMounted(fetchUser)
</script>
