<template>
  <div class="p-4">
    <GanttChart :project-id="id" />
  </div>
</template>

<script setup>
import GanttChart from '../components/Roadmap/GanttChart.vue';

defineProps({
  id: {
    type: String,
    required: true,
  },
});
</script>
