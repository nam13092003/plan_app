<template>
  <div
    class="bg-white p-6 rounded-lg shadow transition
           hover:shadow-lg hover:bg-gray-50"
    :class="{ 'cursor-pointer': to }"
    @click="handleClick"
  >
    <div class="flex items-center">
      <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
        <!-- ICONS -->
        <svg v-if="icon === 'collection'" class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0l2-2m-2 2l-2-2" />
        </svg>

        <svg v-if="icon === 'ticket'" class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
        </svg>

        <svg v-if="icon === 'fire'" class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7.014A8.003 8.003 0 0112 3c2.395 0 4.547.998 6.014 2.628.935.935 1.68 2.12 2.16 3.372C20.604 10.002 21 11.344 21 12.5c0 1.156-.396 2.498-1.128 3.628a8.003 8.003 0 01-2.215 2.529z" />
        </svg>

        <svg v-if="icon === 'users'" class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197" />
        </svg>
      </div>

      <div class="ml-4">
        <p class="text-sm font-medium text-gray-500 truncate">
          {{ title }}
        </p>
        <p class="text-2xl font-semibold text-gray-900">
          {{ value }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';

const router = useRouter();

const props = defineProps({
  title: String,
  value: [String, Number],
  icon: String,
  to: {
    type: String,
    default: null,
  },
});

function handleClick() {
  if (props.to) {
    router.push(props.to);
  }
}
</script>
