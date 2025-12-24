<template>
  <div>
    <!-- Header -->
    <div class="flex justify-end items-center mb-6">
      <div class="flex items-center space-x-4">
        <!-- Filter -->
        <select v-model="filterBy" class="form-select rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
          <option value="assigned_to_me">Assigned to Me</option>
          <option value="created_by_me">Created by Me</option>
        </select>
        <!-- Sort -->
        <select v-model="sortBy" class="form-select rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
          <option value="priority">Sort by: Priority</option>
          <option value="due_date">Sort by: Due Date</option>
        </select>
      </div>
    </div>

    <!-- Loading / Error States -->
    <div v-if="taskStore.loading" class="text-center py-10">Loading tasks...</div>
    <div v-else-if="taskStore.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
      Error: {{ taskStore.error }}
    </div>

    <!-- Task Groups -->
    <div v-else-if="taskGroups.length" class="space-y-8">
      <TaskGroup v-for="group in taskGroups" :key="group.id" :group="group" />
    </div>
    
    <div v-else class="text-center py-10 text-gray-500">
      No tasks found.
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useTaskStore } from '@/stores/task';
import TaskGroup from '../components/MyTasks/TaskGroup.vue';

const taskStore = useTaskStore();

// Filters and sorting state
const filterBy = ref('assigned_to_me');
const sortBy = ref('priority');

// Group tasks from the store by status
const taskGroups = computed(() => {
  const groups = {};
  
  taskStore.tasks.forEach(task => {
    const status = task.status?.name || 'Uncategorized';
    if (!groups[status]) {
      groups[status] = {
        id: task.status?.id || 0,
        title: status,
        color: task.status?.color || '#808080',
        tasks: []
      };
    }
    // Map API data to the format TaskRow expects
    groups[status].tasks.push({
      id: task.id,
      title: task.name,
      projectName: task.project?.name || 'N/A',
      priority: task.priority?.name || 'Normal',
      startDate: task.created_at, // Placeholder, API might have better dates
      endDate: task.due_date,     // Placeholder
      members: task.subscribers || [], // Placeholder
    });
  });

  return Object.values(groups);
});

// Fetch tasks when component is mounted or filters change
const fetchTasks = () => {
  let params = { sort: sortBy.value };
  if (filterBy.value === 'assigned_to_me') {
    params.responsible = 'me'; // Assuming API supports this filter
  }
  if (filterBy.value === 'created_by_me') {
    params.owner = 'me'; // Assuming API supports this filter
  }
  taskStore.fetchTasks(params);
};

onMounted(fetchTasks);
watch([filterBy, sortBy], fetchTasks);

</script>
