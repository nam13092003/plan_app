<template>
  <div class="fixed inset-0 z-50 bg-black/40 flex items-start justify-center pt-24">
    <div class="bg-white w-full max-w-lg rounded-xl shadow-lg">

      <!-- Header -->
      <div class="px-4 py-3 border-b flex justify-between items-center">
        <h3 class="font-semibold text-lg">Your Projects</h3>
        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">✕</button>
      </div>

      <!-- Search -->
      <div class="p-4 border-b">
        <input
          v-model="search"
          type="text"
          placeholder="Search project by name..."
          class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:ring-blue-200 text-black"
        />
      </div>

      <!-- Project list -->
      <div class="max-h-80 overflow-y-auto divide-y">
        <button
          v-for="project in filteredProjects"
          :key="project.id"
          @click="toggleShortcut(project)"
          class="w-full text-left px-4 py-3 transition flex justify-between items-center"
          :class="isShortcut(project) ? 'bg-gray-200 text-gray-400' : 'hover:bg-gray-100'"
        >
          <!-- Chỉ hiển thị tên project, truncate nếu dài -->
          <span class="font-medium text-gray-900 truncate block max-w-full">
            {{ project.name }}
          </span>

          <span class="text-sm font-semibold" :class="isShortcut(project) ? 'text-gray-400' : 'text-blue-600'">
            {{ isShortcut(project) ? 'Added' : 'Add' }}
          </span>
        </button>

        <div
          v-if="!filteredProjects.length"
          class="px-4 py-6 text-center text-gray-500 text-sm"
        >
          No project found
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useProjectsStore } from '@/stores/projects'
import { useAuthStore } from '@/stores/index'
import api from '@/services/api'

const emit = defineEmits(['close'])
const projectsStore = useProjectsStore()
const authStore = useAuthStore()

const projects = ref([])
const search = ref('')

// Lấy tất cả project mà user sở hữu hoặc tham gia
const fetchProjects = async () => {
  try {
    const res = await api.get('/projects', { params: { per_page: -1 } })
    const allProjects = res.data.data || res.data
    const userId = authStore.user?.id

    projects.value = allProjects.filter(p =>
      p.owner_id === userId || (p.users?.some(u => u.id === userId))
    )
  } catch (err) {
    console.error(err)
  }
}

onMounted(fetchProjects)

const filteredProjects = computed(() => {
  if (!search.value) return projects.value
  return projects.value.filter(p =>
    p.name.toLowerCase().includes(search.value.toLowerCase())
  )
})

// Kiểm tra project đã add vào shortcut chưa
const isShortcut = (project) => {
  return projectsStore.shortcutProjects.some(p => p.id === project.id)
}

// Toggle: nếu đã add thì remove, nếu chưa add thì add
const toggleShortcut = (project) => {
  if (isShortcut(project)) {
    projectsStore.removeShortcut(project.id)
  } else {
    projectsStore.addShortcut(project)
  }
}
</script>
