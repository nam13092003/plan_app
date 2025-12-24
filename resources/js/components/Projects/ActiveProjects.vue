<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Active Projects</h1>

    <div v-if="loading" class="text-center py-10">Loading...</div>

    <div v-else-if="projects.length === 0" class="text-gray-500">
      No active projects found.
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div
        v-for="project in projects"
        :key="project.id"
        class="bg-white p-4 rounded shadow cursor-pointer hover:shadow-lg"
        @click="openProject(project.id)"
      >
        <h3 class="font-semibold text-lg">{{ project.name }}</h3>

        <p class="text-sm text-gray-500 mt-1">
          {{ project.description || 'No description' }}
        </p>

        <div class="flex justify-between mt-4 text-sm text-gray-600">
          <span>👤 {{ project.owner.name }}</span>
          <span>🎫 {{ project.tickets_count }}</span>
        </div>

        <span
          class="inline-block mt-3 px-3 py-1 text-xs text-white rounded"
          :style="{ backgroundColor: project.status.color }"
        >
          {{ project.status.name }}
        </span>
      </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6 flex gap-2">
      <button
        v-for="page in pagination.totalPages"
        :key="page"
        @click="changePage(page)"
        :class="[
          'px-3 py-1 border rounded',
          page === pagination.currentPage ? 'bg-blue-600 text-white' : ''
        ]"
      >
        {{ page }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useProjectStore } from '@/stores/projects';

const router = useRouter();
const store = useProjectStore();

const projects = computed(() => store.projects);
const pagination = computed(() => store.pagination);
const loading = computed(() => store.loading);

onMounted(() => {
  store.fetchActiveProjects();
});

function openProject(id) {
  router.push(`/projects/${id}`);
}

function changePage(page) {
  store.fetchActiveProjects(page);
}
</script>
