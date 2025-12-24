<template>
  <div class="bg-white p-6 rounded-lg shadow-md flex flex-col items-center justify-center">
    <h3 class="font-semibold text-lg text-gray-800 mb-2">Total Hours Logged</h3>
    <div class="relative w-40 h-40">
      <svg class="w-full h-full" viewBox="0 0 36 36">
        <path class="text-gray-200"
          d="M18 2.0845
            a 15.9155 15.9155 0 0 1 0 31.831
            a 15.9155 15.9155 0 0 1 0 -31.831"
          fill="none"
          stroke="currentColor"
          stroke-width="3" />
        <path class="text-blue-500"
          :stroke-dasharray="circumference"
          :stroke-dashoffset="strokeOffset"
          d="M18 2.0845
            a 15.9155 15.9155 0 0 1 0 31.831
            a 15.9155 15.9155 0 0 1 0 -31.831"
          fill="none"
          stroke="currentColor"
          stroke-width="3"
          stroke-linecap="round" />
      </svg>
      <div class="absolute inset-0 flex flex-col items-center justify-center">
        <span class="text-3xl font-bold text-gray-800">{{ hours }}</span>
        <span class="text-sm text-gray-500">hours</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  hours: {
    type: Number,
    default: 0,
  },
  maxHours: {
    type: Number,
    default: 100,
  },
});

const radius = 15.9155;
const circumference = 2 * Math.PI * radius;

const normalizedHours = computed(() =>
  Number.isFinite(props.hours) ? props.hours : 0
);

const strokeOffset = computed(() => {
  const percentage = Math.min(
    normalizedHours.value / props.maxHours,
    1
  );
  return circumference * (1 - percentage);
});
</script>
