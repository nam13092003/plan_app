<template>
  <!-- Sidebar -->
  <aside class="fixed left-0 top-0 h-full w-64 flex-shrink-0 bg-gray-800 text-white flex flex-col z-20">
    <!-- Logo -->
    <div class="h-16 flex items-center justify-center text-2xl font-bold border-b border-gray-700 flex-shrink-0">
      <span class="text-blue-400">PLANORA</span>
    </div>
    
    <!-- Menu -->
    <nav class="flex-1 px-2 py-4 space-y-1">
      <router-link :to="{ name: 'Dashboard' }" class="flex items-center px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-700" active-class="bg-gray-900">
        <span>Dashboard</span>
      </router-link>
      <router-link :to="{ name: 'Projects' }" class="flex items-center px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-700" active-class="bg-gray-900">
        <span>Projects</span>
      </router-link>
      <router-link :to="{ name: 'MyTasks' }" class="flex items-center px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-700" active-class="bg-gray-900">
        <span>My Tasks</span>
      </router-link>

      <!-- Shortcut Projects -->
      <div v-if="projectsStore.shortcutProjects.length" class="mt-4">
        <h4 class="px-3 py-1 text-xs font-semibold text-gray-400 uppercase">Shortcuts</h4>
        <div v-for="project in projectsStore.shortcutProjects" :key="project.id">
          <router-link
            :to="{ name: 'ProjectDetail', params: { id: project.id } }"
            class="flex items-center px-3 py-2 rounded-md text-sm hover:bg-gray-700"
          >
            <span class="truncate">{{ project.name }}</span>
          </router-link>
        </div>
      </div>
    </nav>

    <button
      @click="showProjectModal = true"
      class="w-full flex items-center justify-center gap-2
             px-3 py-2 rounded-md text-sm font-semibold
             bg-blue-600 hover:bg-blue-700 transition"
    >
      <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M12 4v16m8-8H4" />
      </svg>
      Add Shortcut Project
    </button>

    <ProjectQuickSwitchModal
      v-if="showProjectModal"
      @close="showProjectModal = false"
    />
  </aside>
</template>

<script setup>
import { ref, watchEffect, computed} from 'vue'
import ProjectQuickSwitchModal from '@/components/Projects/ProjectQuickSwitchModal.vue'
import { useProjectsStore } from '@/stores/projects'
import { useAuthStore } from '@/stores/index'

const showProjectModal = ref(false)
const projectsStore = useProjectsStore()
const authStore = useAuthStore()

const userKey = computed(() => `shortcutProjects_${authStore.user?.id}`)

// Lưu shortcutProjects vào localStorage riêng từng user
watchEffect(() => {
  if (authStore.user?.id) {
    localStorage.setItem(userKey.value, JSON.stringify(projectsStore.shortcutProjects))
  }
})

// Khi load sidebar, lấy lại shortcutProjects từ localStorage của user
try {
  const storedShortcuts = localStorage.getItem(userKey.value)
  if (storedShortcuts) {
    projectsStore.shortcutProjects = JSON.parse(storedShortcuts)
  }
} catch (err) {
  console.error('Failed to load shortcuts from localStorage', err)
}
</script>
