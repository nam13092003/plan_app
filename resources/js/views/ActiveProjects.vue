<template>
  <div class="p-6">
    <h1 class="text-2xl font-semibold mb-6">
      Active Projects
    </h1>

    <div v-if="loading">Loading...</div>

    <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
      <div
        v-for="project in projects"
        :key="project.id"
        class="bg-white p-4 rounded shadow cursor-pointer hover:ring"
        @click="goToProject(project.id)"
      >
        <h3 class="font-semibold text-lg">{{ project.name }}</h3>

        <p class="text-sm text-gray-500">
          Owner: {{ project.owner?.name }}
        </p>

        <p class="text-sm">
          Tickets: {{ project.tickets_count }}
        </p>

        <span
          class="inline-block mt-2 px-2 py-1 text-xs rounded text-white"
          :style="{ backgroundColor: project.status?.color }"
        >
          {{ project.status?.name }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'

const router = useRouter()

const projects = ref([])
const loading = ref(false)

onMounted(async () => {
  loading.value = true
  try {
    const res = await api.get('/projects/active')
    projects.value = res.data.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})

const goToProject = (id) => {
  router.push(`/projects/${id}`)
}
</script>
