<template>
  <div>
    <!-- Group Header -->
    <div class="flex items-center mb-3">
      <span class="h-3 w-3 rounded-full mr-2" :style="{ backgroundColor: group.color }"></span>
      <h3 class="font-semibold text-lg text-gray-800">{{ group.title }}</h3>
      <span class="ml-3 text-sm text-gray-500 bg-gray-200 rounded-full px-2 py-0.5">{{ group.tasks.length }}</span>
    </div>

    <!-- Task List -->
    <div class="space-y-3">
      <TaskRow v-for="task in group.tasks" :key="task.id" :task="task" />
    </div>
  </div>
</template>

<script setup>
import TaskRow from './TaskRow.vue';

const props = defineProps({
  group: {
    type: Object,
    required: true,
  }
});
</script>
