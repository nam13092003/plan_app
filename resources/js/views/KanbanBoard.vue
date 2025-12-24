<template>
  <div>
    <!-- Header: Title and Project Selector -->
    <div class="flex justify-end items-center mb-6">
      <div v-if="projectsStore.projects.length > 0">
        <label for="project-select" class="sr-only">Select Project</label>
        <select 
          id="project-select" 
          v-model="selectedProjectId"
          class="form-select rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
        >
          <option v-for="project in projectsStore.projects" :key="project.id" :value="project.id">
            {{ project.name }}
          </option>
        </select>
      </div>
    </div>

    <!-- Loading / Error States -->
    <div v-if="kanbanStore.loading || projectsStore.loading" class="text-center py-10">Loading Board...</div>
    <div v-else-if="kanbanStore.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
      Error: {{ kanbanStore.error }}
    </div>
    <div v-else-if="projectsStore.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
      Error: {{ projectsStore.error }}
    </div>
    <div v-else-if="!selectedProjectId" class="text-center py-10 text-gray-500">
      No projects found. Please create a project first.
    </div>

    <!-- Kanban Board -->
    <div v-else class="flex space-x-6 overflow-x-auto pb-4">
      <KanbanColumn 
        v-for="column in kanbanStore.board.columns" 
        :key="column.id" 
        :column="column"
        :project-id="selectedProjectId"
        @task-moved="handleTaskMoved"
        @view-task="openTaskModal"
      />
    </div>

    <!-- Task Detail Modal -->
    <transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <TaskDetailModal 
        v-if="isModalOpen" 
        :task="selectedTask" 
        :show="isModalOpen"
        @close="closeTaskModal" 
      />
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useKanbanStore } from '@/stores/kanban';
import { useProjectsStore } from '@/stores/projects';
import KanbanColumn from '../components/Kanban/KanbanColumn.vue';
import TaskDetailModal from '../components/Tasks/TaskDetailModal.vue';

const kanbanStore = useKanbanStore();
const projectsStore = useProjectsStore();

const selectedProjectId = ref(null);

// Fetch initial data
onMounted(async () => {
  // Fetch the list of projects first
  await projectsStore.fetchProjects();
  
  // If projects are found, select the first one and fetch its board
  if (projectsStore.projects.length > 0) {
    selectedProjectId.value = projectsStore.projects[0].id;
    // The watcher below will automatically fetch the board data
  }
});

// Watch for changes in the selected project ID and fetch the new board
watch(selectedProjectId, (newId) => {
  if (newId) {
    kanbanStore.fetchBoard(newId);
  }
});


// --- Modal State & Logic ---
const isModalOpen = ref(false);
const selectedTask = ref(null);

const openTaskModal = (task) => {
  const column = kanbanStore.board.columns.find(col => col.tasks.some(t => t.id === task.id));
  selectedTask.value = {
    ...task,
    description: task.content || 'No description provided.', // Use content as description
    status: column?.title || 'Unknown'
  };
  isModalOpen.value = true;
};

const closeTaskModal = () => {
  isModalOpen.value = false;
  selectedTask.value = null;
};

// --- Drag & Drop Logic ---
const handleTaskMoved = async ({ taskId, newColumnId }) => {
  try {
    await kanbanStore.moveTask(taskId, newColumnId);
    // Optionally, you can show a success notification here
  } catch (error) {
    // If the API call fails, the store would have an error.
    // We should refresh the board to revert the UI state to match the server.
    alert('Failed to update task status. Reverting board state.');
    if (selectedProjectId.value) {
      kanbanStore.fetchBoard(selectedProjectId.value); // Re-fetch with the current project ID
    }
  }
};
</script>
