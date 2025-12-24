<template>
  <div class="space-y-6">
    <!-- Header với nút Create -->
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-bold">Sprints</h2>
      <button 
        @click="openCreateModal" 
        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
      >
        + Create Sprint
      </button>
    </div>

    <!-- Sprint Status Tabs -->
    <div class="border-b border-gray-200">
      <nav class="-mb-px flex space-x-8">
        <button
          v-for="tab in tabs"
          :key="tab.key"
          @click="activeTab = tab.key"
          :class="[
            activeTab === tab.key
              ? 'border-blue-500 text-blue-600'
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
            'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
          ]"
        >
          {{ tab.label }} ({{ getSprintsByStatus(tab.key).length }})
        </button>
      </nav>
    </div>

    <!-- Error Message -->
    <div v-if="projectsStore.error" class="rounded-md bg-red-50 p-4">
      <p class="text-sm font-medium text-red-800">{{ projectsStore.error }}</p>
    </div>

    <!-- Sprint Cards -->
    <div v-if="loading" class="text-center py-10">Loading sprints...</div>
    <div v-else-if="filteredSprints.length === 0" class="text-center py-10 text-gray-500">
      No sprints found. Create your first sprint to get started!
    </div>
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div
        v-for="sprint in filteredSprints"
        :key="sprint.id"
        class="bg-white rounded-lg shadow-md p-6 border-l-4 transition-shadow hover:shadow-lg"
        :class="getSprintBorderColor(sprint)"
      >
        <!-- Sprint Header -->
        <div class="flex justify-between items-start mb-4">
          <div class="flex-1">
            <h3 class="text-lg font-semibold text-gray-900">{{ sprint.name }}</h3>
            <p class="text-sm text-gray-500 mt-1">
              {{ formatDateRange(sprint.starts_at, sprint.ends_at) }}
            </p>
          </div>
          <span
            class="px-2 py-1 text-xs font-semibold rounded-full whitespace-nowrap ml-2"
            :class="getStatusBadgeClass(sprint)"
          >
            {{ getSprintStatus(sprint) }}
          </span>
        </div>

        <!-- Progress Bar (only for active sprints) -->
        <div v-if="sprint.started_at && !sprint.ended_at" class="mb-4">
          <div class="flex justify-between text-xs text-gray-600 mb-1">
            <span>Progress</span>
            <span>{{ getProgressPercentage(sprint) }}%</span>
          </div>
          <div class="w-full bg-gray-200 rounded-full h-2">
            <div
              class="bg-blue-600 h-2 rounded-full transition-all"
              :style="{ width: getProgressPercentage(sprint) + '%' }"
            ></div>
          </div>
          <p class="text-xs text-gray-500 mt-1">
            {{ getRemainingDays(sprint) }} days remaining
          </p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 gap-4 mb-4">
          <div>
            <p class="text-2xl font-bold text-gray-900">{{ sprint.tickets_count || 0 }}</p>
            <p class="text-xs text-gray-500">Tickets</p>
          </div>
          <div>
            <p class="text-2xl font-bold text-gray-900">{{ getCompletedTickets(sprint) }}</p>
            <p class="text-xs text-gray-500">Completed</p>
          </div>
        </div>

        <!-- Description -->
        <p v-if="sprint.description" class="text-sm text-gray-600 mb-4 line-clamp-2">
          {{ sprint.description }}
        </p>

        <!-- Actions -->
        <div class="flex flex-wrap gap-2">
          <button
            v-if="!sprint.started_at"
            @click="startSprint(sprint.id)"
            class="flex-1 px-3 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700"
          >
            Start Sprint
          </button>
          <button
            v-else-if="!sprint.ended_at"
            @click="stopSprint(sprint.id)"
            class="flex-1 px-3 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700"
          >
            Stop Sprint
          </button>
          <button
            v-if="project?.type === 'scrum'"
            @click="openManageTicketsModal(sprint)"
            class="px-3 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700"
          >
            Manage Tickets
          </button>
          <button
            @click="openEditModal(sprint)"
            class="px-3 py-2 bg-gray-200 text-gray-700 text-sm rounded-md hover:bg-gray-300"
          >
            Edit
          </button>
          <button
            @click="confirmDelete(sprint)"
            class="px-3 py-2 bg-red-100 text-red-700 text-sm rounded-md hover:bg-red-200"
          >
            Delete
          </button>
        </div>
      </div>
    </div>

    <!-- Sprint Form Modal -->
    <SprintFormModal
      v-if="isModalOpen"
      :sprint="selectedSprint"
      :project-id="project.id"
      @close="closeModal"
      @save="handleSave"
    />

    <!-- Sprint Tickets Modal (only for scrum projects) -->
    <SprintTicketsModal
      v-if="ticketsModalOpen && project?.type === 'scrum'"
      :sprint="selectedSprintForTickets"
      :project="project"
      @close="closeTicketsModal"
      @saved="handleTicketsSaved"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useProjectsStore } from '@/stores/projects';
import SprintFormModal from './SprintFormModal.vue';
import SprintTicketsModal from './SprintTicketsModal.vue';

const props = defineProps({
  project: {
    type: Object,
    required: true,
  },
});

const projectsStore = useProjectsStore();
const sprints = ref([]);
const loading = ref(false);
const activeTab = ref('all');
const isModalOpen = ref(false);
const selectedSprint = ref(null);
const ticketsModalOpen = ref(false);
const selectedSprintForTickets = ref(null);

const tabs = [
  { key: 'all', label: 'All' },
  { key: 'planned', label: 'Planned' },
  { key: 'active', label: 'Active' },
  { key: 'completed', label: 'Completed' },
];

// Computed
const filteredSprints = computed(() => {
  if (activeTab.value === 'all') return sprints.value;
  return getSprintsByStatus(activeTab.value);
});

// Methods
const loadSprints = async () => {
  if (!props.project?.id) return;
  loading.value = true;
  try {
    sprints.value = await projectsStore.fetchSprints(props.project.id);
  } finally {
    loading.value = false;
  }
};

const getSprintsByStatus = (status) => {
  return sprints.value.filter(s => getSprintStatus(s) === status);
};

const getSprintStatus = (sprint) => {
  if (sprint.ended_at) return 'completed';
  if (sprint.started_at) return 'active';
  return 'planned';
};

const getStatusBadgeClass = (sprint) => {
  const status = getSprintStatus(sprint);
  const classes = {
    planned: 'bg-gray-100 text-gray-800',
    active: 'bg-green-100 text-green-800',
    completed: 'bg-blue-100 text-blue-800',
  };
  return classes[status] || classes.planned;
};

const getSprintBorderColor = (sprint) => {
  const status = getSprintStatus(sprint);
  const colors = {
    planned: 'border-gray-300',
    active: 'border-green-500',
    completed: 'border-blue-500',
  };
  return colors[status] || colors.planned;
};

const formatDateRange = (start, end) => {
  if (!start || !end) return 'N/A';
  const startDate = new Date(start).toLocaleDateString('en-US', { 
    month: 'short', 
    day: 'numeric' 
  });
  const endDate = new Date(end).toLocaleDateString('en-US', { 
    month: 'short', 
    day: 'numeric', 
    year: 'numeric' 
  });
  return `${startDate} - ${endDate}`;
};

const getProgressPercentage = (sprint) => {
  if (!sprint.started_at || sprint.ended_at) return 0;
  if (!sprint.starts_at || !sprint.ends_at) return 0;
  
  const start = new Date(sprint.starts_at).getTime();
  const end = new Date(sprint.ends_at).getTime();
  const now = Date.now();
  const started = new Date(sprint.started_at).getTime();
  
  const total = end - start;
  const elapsed = now - started;
  
  if (total <= 0) return 0;
  const percentage = Math.min(100, Math.max(0, Math.round((elapsed / total) * 100)));
  return percentage;
};

const getRemainingDays = (sprint) => {
  if (!sprint.ends_at) return 0;
  const end = new Date(sprint.ends_at);
  const now = new Date();
  const diff = Math.ceil((end - now) / (1000 * 60 * 60 * 24));
  return Math.max(0, diff);
};

const getCompletedTickets = (sprint) => {
  if (!sprint.tickets || !Array.isArray(sprint.tickets)) return 0;
  return sprint.tickets.filter(t => {
    const statusName = t.status?.name?.toLowerCase() || '';
    return statusName === 'done' || statusName === 'completed' || statusName === 'archived';
  }).length;
};

const startSprint = async (sprintId) => {
  if (!confirm('Are you sure you want to start this sprint? This will end any other active sprints in this project.')) {
    return;
  }
  try {
    await projectsStore.startSprint(sprintId, props.project.id);
    await loadSprints();
  } catch (err) {
    // Error is already set in store
  }
};

const stopSprint = async (sprintId) => {
  if (!confirm('Are you sure you want to stop this sprint?')) return;
  try {
    await projectsStore.stopSprint(sprintId, props.project.id);
    await loadSprints();
  } catch (err) {
    // Error is already set in store
  }
};

const openCreateModal = () => {
  selectedSprint.value = null;
  isModalOpen.value = true;
};

const openEditModal = (sprint) => {
  selectedSprint.value = { ...sprint };
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
  selectedSprint.value = null;
  projectsStore.error = null;
};

const handleSave = async (sprintData) => {
  console.log('ProjectSprintsTab: handleSave called', sprintData, 'Project ID:', props.project?.id);
  try {
    if (!props.project?.id) {
      alert('Project ID is missing. Please refresh the page.');
      return;
    }

    if (sprintData.id) {
      console.log('Updating sprint:', sprintData.id);
      await projectsStore.updateSprint(sprintData.id, sprintData);
    } else {
      console.log('Creating sprint for project:', props.project.id);
      await projectsStore.createSprint(props.project.id, sprintData);
    }
    
    console.log('Sprint saved successfully, error:', projectsStore.error);
    
    // Only close modal and reload if no error
    if (!projectsStore.error) {
      await loadSprints();
      closeModal();
    } else {
      console.error('Error after save:', projectsStore.error);
    }
  } catch (err) {
    console.error('Error saving sprint:', err);
    console.error('Error response:', err.response);
    // Error is already set in store, but show alert for user feedback
    if (err.response?.data?.message) {
      alert(err.response.data.message);
    } else if (projectsStore.error) {
      alert(projectsStore.error);
    } else {
      alert('An unexpected error occurred. Please check the console for details.');
    }
  }
};

const confirmDelete = async (sprint) => {
  if (!confirm(`Are you sure you want to delete "${sprint.name}"? This action cannot be undone.`)) {
    return;
  }
  try {
    await projectsStore.deleteSprint(sprint.id, props.project.id);
    await loadSprints();
  } catch (err) {
    // Error is already set in store
  }
};

const openManageTicketsModal = (sprint) => {
  if (props.project?.type !== 'scrum') return;
  selectedSprintForTickets.value = sprint;
  ticketsModalOpen.value = true;
};

const closeTicketsModal = () => {
  ticketsModalOpen.value = false;
  selectedSprintForTickets.value = null;
};

const handleTicketsSaved = async () => {
  await loadSprints();
};

watch(() => props.project?.id, (newId) => {
  if (newId) {
    loadSprints();
  }
}, { immediate: true });

onMounted(() => {
  if (props.project?.id) {
    loadSprints();
  }
});
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
