
<template>
  <div v-if="ticketsStore.loading" class="text-center py-10">Loading ticket details...</div>
  <div v-else-if="ticketsStore.error" class="text-center py-10 text-red-500">{{ ticketsStore.error }}</div>
  <div v-else-if="ticket" class="space-y-6">
    
    <!-- Page Header -->
    <div>
      <p class="text-sm text-gray-500">
        <router-link :to="{ name: 'ProjectDetail', params: { id: ticket.project.id } }" class="hover:underline">{{ ticket.project.name }}</router-link>
        / {{ ticket.code }}
      </p>
      <h1 class="text-3xl font-bold text-gray-800">{{ ticket.name }}</h1>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      
      <!-- Left Column (Description & Comments) -->
      <div class="lg:col-span-2 space-y-6">
        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="font-semibold text-lg mb-2">Description</h3>
          <p class="text-gray-700 whitespace-pre-wrap">{{ ticket.description || 'No description provided.' }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="font-semibold text-lg mb-4">Comments</h3>
          <TicketComments :ticket-id="ticket.id" :comments="ticket.comments" />
        </div>
      </div>

      <!-- Right Column (Details & Time Log) -->
      <div class="lg:col-span-1 space-y-6">
        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="font-semibold text-lg mb-4">Details</h3>
          <dl class="space-y-4">
            <div>
              <dt class="text-sm font-medium text-gray-500">Status</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ ticket.status.name }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Priority</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ ticket.priority.name }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Type</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ ticket.type.name }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Assignee</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ ticket.responsible?.name || 'Unassigned' }}</dd>
            </div>
             <div>
              <dt class="text-sm font-medium text-gray-500">Reporter</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ ticket.owner.name }}</dd>
            </div>
          </dl>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="font-semibold text-lg mb-4">Time Tracking</h3>
          <TicketTimeLog :ticket="ticket" />
        </div>
      </div>

    </div>

  </div>
</template>

<script setup>
import { onMounted, computed } from 'vue';
import { useTicketsStore } from '@/stores/tickets';
import TicketComments from '../components/Tickets/TicketComments.vue';
import TicketTimeLog from '../components/Tickets/TicketTimeLog.vue';

const props = defineProps({
  id: {
    type: String,
    required: true,
  },
});

const ticketsStore = useTicketsStore();
const ticket = computed(() => ticketsStore.ticket);

onMounted(() => {
  ticketsStore.fetchTicket(props.id);
});
</script>
