<template>
  <div>
    <!-- Loading / Error States -->
    <div v-if="kanbanStore.loading" class="text-center py-10">Loading Board...</div>
    <div v-else-if="kanbanStore.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
      Error: {{ kanbanStore.error }}
    </div>

    <!-- Kanban Board -->
    <div v-else class="flex space-x-6 overflow-x-auto pb-4">
      <KanbanColumn 
        v-for="column in kanbanStore.board.columns" 
        :key="column.id" 
        :column="column"
        :project-id="project.id"
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
import { ref, watch } from 'vue';
import { useKanbanStore } from '@/stores/kanban';
import KanbanColumn from '../Kanban/KanbanColumn.vue';
import TaskDetailModal from '../Tasks/TaskDetailModal.vue';

const props = defineProps({
  project: {
    type: Object,
    required: true,
  },
});

const kanbanStore = useKanbanStore();

// Watch for changes in the project prop and fetch the board
watch(() => props.project.id, (newId) => {
  if (newId) {
    // Truyền cả ID và Type của project vào hàm fetchBoard
    kanbanStore.fetchBoard(newId, props.project.type);
  }
}, { immediate: true });


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
  } catch (error) {
    alert('Failed to update task status. Reverting board state.');
    if (props.project.id) {
      kanbanStore.fetchBoard(props.project.id);
    }
  }
};
</script>
