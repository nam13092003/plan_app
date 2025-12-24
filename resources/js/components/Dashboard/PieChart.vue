<!-- 
<template>
  <Doughnut v-if="chartData" :data="data" :options="options" />
</template>

<script setup>
import { computed } from 'vue';
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js';
import { Doughnut } from 'vue-chartjs';

ChartJS.register(ArcElement, Tooltip, Legend);

const props = defineProps({
  chartData: {
    type: Object,
    required: true,
  },
});

const data = computed(() => ({
  labels: props.chartData.labels,
  datasets: [
    {
      backgroundColor: props.chartData.colors,
      data: props.chartData.values,
    },
  ],
}));

const options = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom',
    },
  },
};
</script> -->

<script setup>
import { computed } from 'vue'
import { Chart as ChartJS, ArcElement, Tooltip, Legend, Title } from 'chart.js'
import { Doughnut } from 'vue-chartjs'

ChartJS.register(ArcElement, Tooltip, Legend, Title)

const props = defineProps({
  chartData: {
    type: Object,
    required: true,
  },
})

const data = computed(() => ({
  labels: props.chartData?.labels || [],
  datasets: props.chartData?.datasets || [],
}))

const options = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom',
    },
  },
}
</script>

<template>
  <div class="w-full h-full">
    <Doughnut v-if="data.datasets.length" :data="data" :options="options" />
    <div v-else class="w-full h-full flex items-center justify-center text-gray-500">
      Loading chart...
    </div>
  </div>
</template>
