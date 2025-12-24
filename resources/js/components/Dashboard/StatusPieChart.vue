<!-- <template>
  <div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4">
      <h3 class="font-semibold text-lg text-gray-800">{{ title }}</h3>
    </div>
    
    <div class="h-64">
      <PieChart v-if="chartData" :chart-data="chartData" />
      <div v-else class="h-full flex items-center justify-center bg-gray-50 rounded-md">
        <p class="text-gray-500">No data available.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import PieChart from './PieChart.vue';

const props = defineProps({
  chartData: {
    type: Object,
    default: null
  },
  title: {
    type: String,
    default: 'Chart'
  }
});
</script> -->

<template>
  <div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4">
      <h3 class="font-semibold text-lg text-gray-800">{{ title }}</h3>
    </div>
    
    <div class="h-64 relative">
      <PieChart v-if="chartData?.datasets?.length" :chart-data="chartData" />
      
      <div v-else class="absolute inset-0 flex items-center justify-center bg-gray-50 rounded-md">
        <p class="text-gray-500">No data available.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import PieChart from './PieChart.vue'

const props = defineProps({
  chartData: {
    type: Object,
    default: () => ({ labels: [], datasets: [] }),
  },
  title: {
    type: String,
    default: 'Chart',
  },
})
</script>
