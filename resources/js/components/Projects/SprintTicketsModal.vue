<template>
  <div
    class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center z-50"
    @click.self="$emit('close')"
  >
    <div class="relative mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white">
      <div class="flex justify-between items-start mb-4">
        <h3 class="text-lg font-bold">Manage Tickets for {{ sprint?.name }}</h3>
        <button @click="$emit('close')" class="text-gray-500 hover:text-gray-700">✕</button>
      </div>

      <div v-if="error" class="mb-4 rounded-md bg-red-50 p-4">
        <p class="text-sm font-medium text-red-800">{{ stringifyError(error) }}</p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Available Tickets -->
        <div>
          <h4 class="font-semibold mb-2">Available Tickets</h4>
          <div class="border rounded-lg p-4 max-h-72 overflow-y-auto">
            <label
              v-for="ticket in availableTickets"
              :key="ticket.id"
              class="flex items-center p-2 hover:bg-gray-50"
            >
              <input
                type="checkbox"
                :value="ticket.id"
                v-model="selectedTicketIds"
                class="mr-2"
              />
              <span>{{ ticket.code }} - {{ ticket.name }}</span>
            </label>
            <p v-if="availableTickets.length === 0" class="text-gray-500 text-sm">No tickets available</p>
          </div>
        </div>

        <!-- Current Sprint Tickets -->
        <div>
          <h4 class="font-semibold mb-2">
            Current Sprint Tickets ({{ (sprintData?.tickets || sprint?.tickets || []).length }})
          </h4>
          <div class="border rounded-lg p-4 max-h-72 overflow-y-auto">
            <div 
              v-for="ticket in (sprintData?.tickets || sprint?.tickets || [])" 
              :key="ticket.id" 
              class="p-2 border-b"
            >
              {{ ticket.code }} - {{ ticket.name }}
            </div>
            <p 
              v-if="(!sprintData?.tickets && !sprint?.tickets) || (sprintData?.tickets || sprint?.tickets || []).length === 0" 
              class="text-gray-500 text-sm"
            >
              No tickets in this sprint
            </p>
          </div>
        </div>
      </div>

      <div class="flex justify-end space-x-3 mt-6">
        <button @click="$emit('close')" class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">
          Cancel
        </button>
        <button
          @click="saveTickets"
          :disabled="loading"
          class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
        >
          {{ loading ? 'Saving...' : 'Save' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import api from '@/services/api';

const props = defineProps({
  sprint: {
    type: Object,
    required: true,
  },
  project: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['close', 'saved']);

const availableTickets = ref([]);
const selectedTicketIds = ref([]);
const loading = ref(false);
const error = ref(null);
const sprintData = ref(null); // Local copy of sprint data with tickets

const stringifyError = (e) => {
  if (!e) return '';
  if (typeof e === 'string') return e;
  try {
    return JSON.stringify(e);
  } catch (err) {
    return 'An error occurred';
  }
};

const loadAvailableTickets = async () => {
  loading.value = true;
  error.value = null;
  try {
    const response = await api.get('/tickets', {
      params: {
        project_id: props.project.id,
        per_page: -1,
      },
    });
    const data = response.data?.data || response.data || [];
    // Exclude tickets already in this sprint from the available list (still can be selected via checkbox)
    availableTickets.value = data;
  } catch (err) {
    console.error('Failed to load tickets:', err);
    error.value = err.response?.data?.message || 'Failed to load tickets';
  } finally {
    loading.value = false;
  }
};

const saveTickets = async () => {
  loading.value = true;
  error.value = null;
  try {
    await api.post(`/sprints/${props.sprint.id}/tickets`, {
      ticket_ids: selectedTicketIds.value,
    });
    // Reload sprint data to get updated tickets list
    await loadSprintData();
    emit('saved');
    // Close modal after successful save
    emit('close');
  } catch (err) {
    console.error('Failed to save tickets:', err);
    error.value = err.response?.data?.message || 'Failed to save tickets';
  } finally {
    loading.value = false;
  }
};

const loadSprintData = async () => {
  // Fetch fresh sprint data with tickets
  try {
    const response = await api.get(`/sprints/${props.sprint.id}`);
    sprintData.value = response.data;
    // Update selected tickets based on fresh data
    if (sprintData.value?.tickets?.length) {
      selectedTicketIds.value = sprintData.value.tickets.map((t) => t.id);
    } else {
      selectedTicketIds.value = [];
    }
  } catch (err) {
    console.error('Failed to load sprint data:', err);
    // Fallback to props data
    sprintData.value = props.sprint;
    if (props.sprint?.tickets?.length) {
      selectedTicketIds.value = props.sprint.tickets.map((t) => t.id);
    }
  }
};

onMounted(async () => {
  await loadSprintData();
  await loadAvailableTickets();
});
</script>

