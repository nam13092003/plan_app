<template>
  <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
    <div class="p-4 flex items-center justify-between">
      <!-- Left side: Title and Project -->
      <div class="flex items-center">
        <span class="h-2 w-2 rounded-full mr-3" :class="priorityClass"></span>
        <div>
          <p class="font-bold text-gray-800">{{ task.title }}</p>
          <p class="text-sm text-gray-500">{{ task.projectName }}</p>
        </div>
      </div>

      <!-- Right side: Avatars and Time -->
      <div class="flex items-center space-x-4">
        <!-- Member Avatars -->
        <div class="flex -space-x-2">
          <img v-for="member in task.members" :key="member.id" class="inline-block h-8 w-8 rounded-full ring-2 ring-white" :src="member.avatar" :alt="member.name">
        </div>
        <!-- Time Pills -->
        <div class="flex items-center space-x-2 text-sm text-gray-600">
          <span class="bg-gray-200 rounded-full px-3 py-1">{{ task.startDate }}</span>
          <span>&rarr;</span>
          <span class="bg-gray-200 rounded-full px-3 py-1">{{ task.endDate }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  task: {
    type: Object,
    required: true,
  }
});

const priorityClass = computed(() => {
  const priorities = {
    High: 'bg-red-500',
    Medium: 'bg-yellow-500',
    Low: 'bg-green-500'
  };
  return priorities[props.task.priority] || 'bg-gray-300';
});
</script>
