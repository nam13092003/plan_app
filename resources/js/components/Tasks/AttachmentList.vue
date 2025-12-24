<template>
  <div>
    <h3 class="font-semibold text-gray-700 mb-2">Attachments</h3>
    <div class="space-y-3">
      <!-- Attachment Item -->
      <div v-for="file in files" :key="file.name" class="flex items-center bg-gray-50 p-2 rounded-md border">
        <div class="flex-shrink-0">
          <svg class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
        </div>
        <div class="ml-3 flex-1">
          <p class="text-sm font-medium text-gray-800 truncate">{{ file.name }}</p>
          <p class="text-xs text-gray-500">{{ file.size }} - Added by {{ file.user }}</p>
        </div>
        <button class="ml-4 p-1 text-gray-400 hover:text-gray-600">
          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
        </button>
      </div>
      
      <!-- Upload Area -->
      <div class="mt-2 p-4 border-2 border-dashed rounded-lg text-center text-gray-500 hover:border-blue-500 hover:text-blue-500 cursor-pointer">
        Drag & drop files here or click to upload
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

// Dummy Data
const files = ref([
  { name: 'dashboard_mockup_v3.fig', size: '2.1 MB', user: 'Jane Smith' },
  { name: 'API_Specification.pdf', size: '870 KB', user: 'John Doe' },
]);
</script>
