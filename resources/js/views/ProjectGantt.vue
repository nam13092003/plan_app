<template>
  <div class="p-4">
    <h2 class="text-2xl font-bold">Gantt Chart Test</h2>
    <p>If you can see this, the component is loading correctly.</p>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';

onMounted(() => {
  console.log('Minimal ProjectGantt.vue component has mounted!');
});
</script>

<style>
/* All styles removed for debugging */
</style>
