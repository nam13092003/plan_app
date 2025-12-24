<template>
  <div 
    class="bg-white rounded-lg shadow-sm p-4 border-l-4 cursor-pointer hover:shadow-lg hover:-translate-y-1 transition-all duration-200 group" 
    :class="priorityClass"
    @click="$emit('view-task', ticket)" 
  >
    <!-- Epic Tag -->
    <div v-if="ticket.epic" class="mb-2">
      <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-purple-100 text-purple-700 border border-purple-200">
        {{ ticket.epic.name }}
      </span>
    </div>

    <h4 class="font-semibold text-gray-800 text-sm mb-3 group-hover:text-blue-600 transition-colors">
      {{ ticket?.title || ticket?.name || 'Không có tiêu đề' }}
    </h4>
    
    <!-- Bottom section -->
    <div class="flex justify-between items-center text-xs text-gray-500">
      <!-- Status & Estimation -->
      <div class="flex items-center gap-3">
        <div class="flex items-center gap-1.5" v-if="ticket.status">
          <span class="w-2.5 h-2.5 rounded-full" :style="{ backgroundColor: ticket.status.color }"></span>
          <span class="font-medium">{{ ticket.status.name }}</span>
        </div>
        <div v-if="ticket.estimation" class="flex items-center gap-1">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          <span class="font-medium">{{ ticket.estimation }}h</span>
        </div>
      </div>
      
      <!-- Assignee & Code -->
      <div class="flex items-center gap-2">
        <span class="px-2 py-1 font-mono font-medium rounded-md bg-gray-100 text-gray-600">
          {{ ticket?.code || 'N/A' }}
        </span>
        <img 
          :src="ticket?.responsible?.avatar || 'data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\' fill=\'%23ccc\'><circle cx=\'50\' cy=\'50\' r=\'50\'/></svg>'" 
          class="w-6 h-6 rounded-full object-cover" 
          :title="ticket?.responsible?.name || 'Chưa phân công'"
          alt="Avatar"
        >
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  ticket: {
    type: Object,
    required: true
  }
});

defineEmits(['view-task']);

const priorityClass = computed(() => {
  const pName = props.ticket?.priority?.name || '';
  const priorities = {
    'High': 'border-red-500',
    'Normal': 'border-blue-500',
    'Low': 'border-green-500'
  };
  return priorities[pName] || 'border-gray-200';
});
</script>