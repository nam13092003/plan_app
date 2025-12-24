<template>
  <div>
    <div class="flex justify-between items-center mb-2">
      <h3 class="font-semibold text-gray-700">Subtasks</h3>
      <span class="text-sm text-gray-500">{{ completedCount }} / {{ subtasks.length }}</span>
    </div>
    <!-- Progress Bar -->
    <div class="w-full bg-gray-200 rounded-full h-2 mb-3">
      <div class="bg-blue-600 h-2 rounded-full" :style="{ width: progressPercentage + '%' }"></div>
    </div>
    <!-- Subtask List -->
    <div class="space-y-2">
      <div v-for="task in subtasks" :key="task.id" class="flex items-center p-2 rounded-md hover:bg-gray-100">
        <input 
          type="checkbox" 
          :checked="task.is_completed"
          class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
        >
        <span class="ml-3 text-sm text-gray-800" :class="{ 'line-through text-gray-500': task.is_completed }">
          {{ task.name }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

// Dummy Data
const subtasks = ref([
  { id: 1, name: 'Initial research and data gathering', is_completed: true },
  { id: 2, name: 'Create wireframes for all screen sizes', is_completed: true },
  { id: 3, name: 'Build reusable UI components', is_completed: false },
  { id: 4, name: 'Develop the main layout structure', is_completed: false },
  { id: 5, name: 'Integrate with backend API', is_completed: false },
]);

const completedCount = computed(() => subtasks.value.filter(t => t.is_completed).length);

const progressPercentage = computed(() => {
  if (subtasks.value.length === 0) return 0;
  return (completedCount.value / subtasks.value.length) * 100;
});
</script>
