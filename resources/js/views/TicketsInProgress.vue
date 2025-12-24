<template>
  <div class="p-6">
    <h1 class="text-2xl font-semibold mb-6">Tickets In Progress</h1>

    <div v-if="loading">Loading...</div>

    <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">

      <div
        v-for="ticket in tickets"
        :key="ticket.id"
        class="bg-white p-4 rounded shadow hover:ring"
      >

        <h3 class="font-semibold text-lg">{{ ticket.name }}</h3>

        <p class="text-sm text-gray-500">
          Project: {{ ticket.project?.name }}
        </p>

        <span
          class="inline-block mt-2 px-2 py-1 text-xs rounded text-white"
          :style="{ backgroundColor: ticket.status?.color }"
        >
          {{ ticket.status?.name }}
        </span>

        <span
          class="ml-2 inline-block px-2 py-1 text-xs rounded text-white"
          :style="{ backgroundColor: ticket.priority?.color }"
        >
          {{ ticket.priority?.name }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'

const router = useRouter()
const tickets = ref([])
const loading = ref(false)

onMounted(async () => {
  loading.value = true
  try {
    const res = await api.get('/tickets/in-progress')
    tickets.value = res.data.data
  } finally {
    loading.value = false
  }
})

// const goToTicket = (id) => {
//   router.push(`/app/tickets/${id}`)
// }
</script>
