
<template>
  <Bar v-if="chartData" :data="data" :options="options" />
</template>

<script setup>
import { computed } from 'vue';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale
} from 'chart.js';
import { Bar } from 'vue-chartjs';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

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
      label: 'Hours',
      backgroundColor: '#3B82F6', // blue-500
      data: props.chartData.values,
    },
  ],
}));

const options = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false, // Hide legend for a cleaner look
    },
  },
  scales: {
    y: {
      beginAtZero: true
    }
  }
};
</script>
