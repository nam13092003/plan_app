<template>
  <div class="bg-gray-100 rounded-lg w-80 flex-shrink-0 flex flex-col">
    <!-- Column Header -->
    <div class="flex items-center justify-between p-4 border-b">
      <div class="flex items-center">
        <span class="h-3 w-3 rounded-full mr-2" :style="{ backgroundColor: column.color }"></span>
        <h3 class="font-semibold text-gray-800">{{ column.title }}</h3>
        <span class="ml-2 text-sm text-gray-500 bg-gray-200 rounded-full px-2 py-0.5">{{ tasks.length }}</span>
      </div>
    </div>

    <!-- Draggable Task List -->
    <div class="p-4 space-y-4 overflow-y-auto flex-grow" style="max-height: calc(100vh - 260px);">
      <draggable
        v-model="tasks"
        group="tasks"
        item-key="id"
        class="space-y-4 min-h-[100px]"
        @change="onDragChange"
      >
        <template #item="{ element }">
          <TaskCard 
            :ticket="element" 
            @view-task="$emit('view-task', $event)" 
          />
        </template>
      </draggable>
    </div>

    <!-- Add New Task Form -->
    <div class="p-4 border-t mt-auto">
      <div v-if="showNewTaskForm">
        <textarea
          v-model="newTaskName"
          class="form-textarea w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
          placeholder="Enter a title for this card..."
          rows="3"
        ></textarea>
        <div class="mt-2 flex items-center justify-end space-x-2">
          <button @click="resetForm" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 rounded-md">
            Cancel
          </button>
          <button 
            @click="handleAddNewTask" 
            :disabled="!newTaskName.trim() || isSubmitting"
            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md disabled:bg-blue-300"
          >
            {{ isSubmitting ? 'Adding...' : 'Add Card' }}
          </button>
        </div>
      </div>
      <button 
        v-else
        @click="showNewTaskForm = true" 
        class="w-full flex items-center justify-start p-2 text-sm font-medium text-gray-600 hover:bg-gray-200 hover:text-gray-800 rounded-md"
      >
        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
        Add a card
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import draggable from 'vuedraggable';
import TaskCard from './TaskCard.vue';
import { useKanbanStore } from '@/stores/kanban';

const props = defineProps({
  column: {
    type: Object,
    required: true
  },
  projectId: {
    type: Number,
    required: true
  }
});

const emit = defineEmits(['task-moved', 'view-task']);
const kanbanStore = useKanbanStore();

// --- Local State for UI ---
const tasks = ref([...props.column.tasks]);
const showNewTaskForm = ref(false);
const newTaskName = ref('');
const isSubmitting = ref(false);

// Watch for changes in the prop and update the local ref
watch(() => props.column.tasks, (newTasks) => {
  tasks.value = [...newTasks];
});

const onDragChange = (event) => {
  if (event.added) {
    const { element: task } = event.added;
    emit('task-moved', {
      taskId: task.id,
      newColumnId: props.column.id,
    });
  }
};

const resetForm = () => {
  showNewTaskForm.value = false;
  newTaskName.value = '';
  isSubmitting.value = false;
};

const handleAddNewTask = async () => {
  if (!newTaskName.value.trim()) return;

  isSubmitting.value = true;
  try {
    await kanbanStore.createTask({
      name: newTaskName.value,
      project_id: props.projectId,
      status_id: props.column.id,
    });
    resetForm();
  } catch (error) {
    alert('Failed to create the new task. Please try again.');
    isSubmitting.value = false;
  }
};
</script>
