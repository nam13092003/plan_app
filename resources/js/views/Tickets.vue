<template>
  <div>
    <div class="flex justify-end mb-6">
      <button @click="openCreateModal" class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700">
        Create Ticket
      </button>
    </div>

    <!-- Filters -->
    <div class="mb-6 bg-white p-4 rounded-lg shadow">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
          <label for="project-filter" class="block text-sm font-medium text-gray-700">Project</label>
          <select id="project-filter" v-model="filters.project_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
            <option value="">All Projects</option>
            <option v-for="project in referentialStore.projects" :key="project.id" :value="project.id">
              {{ project.name }}
            </option>
          </select>
        </div>
        <div>
          <label for="status-filter" class="block text-sm font-medium text-gray-700">Status</label>
          <select id="status-filter" v-model="filters.status_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
            <option value="">All Statuses</option>
            <option v-for="status in referentialStore.ticketStatuses" :key="status.id" :value="status.id">
              {{ status.name }}
            </option>
          </select>
        </div>
        <div>
          <label for="priority-filter" class="block text-sm font-medium text-gray-700">Priority</label>
          <select id="priority-filter" v-model="filters.priority_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
            <option value="">All Priorities</option>
            <option v-for="priority in referentialStore.ticketPriorities" :key="priority.id" :value="priority.id">
              {{ priority.name }}
            </option>
          </select>
        </div>
        <div>
          <label for="type-filter" class="block text-sm font-medium text-gray-700">Type</label>
          <select id="type-filter" v-model="filters.type_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
            <option value="">All Types</option>
            <option v-for="type in referentialStore.ticketTypes" :key="type.id" :value="type.id">
              {{ type.name }}
            </option>
          </select>
        </div>
      </div>
    </div>

    <!-- Loading and Error States -->
    <div v-if="ticketsStore.loading" class="text-center py-10">Loading tickets...</div>
    <div v-else-if="ticketsStore.error" class="text-center py-10 text-red-500">{{ ticketsStore.error }}</div>

    <!-- Tickets Table -->
    <div v-else class="bg-white shadow-md rounded-lg overflow-hidden">
      <table class="min-w-full leading-normal">
        <thead>
          <tr>
            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ticket</th>
            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Project</th>
            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Priority</th>
            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Assignee</th>
            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="ticket in ticketsStore.tickets" :key="ticket.id" class="hover:bg-gray-50">
            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
              <router-link :to="{ name: 'TicketDetail', params: { id: ticket.id } }" class="block hover:text-blue-600">
                <p class="text-gray-900 whitespace-no-wrap font-bold">{{ ticket.code }}</p>
                <p class="text-gray-600 whitespace-no-wrap">{{ ticket.name }}</p>
              </router-link>
            </td>
            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ ticket.project?.name }}</td>
            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                <span class="relative inline-block px-3 py-1 font-semibold leading-tight" :style="{ color: ticket.status?.color }">
                    <span aria-hidden class="absolute inset-0 opacity-20 rounded-full" :style="{ backgroundColor: ticket.status?.color }"></span>
                    <span class="relative">{{ ticket.status?.name || 'N/A' }}</span>
                </span>
            </td>
            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ ticket.priority?.name }}</td>
            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ ticket.responsible?.name || 'Unassigned' }}</td>
            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right">
              <button @click="openEditModal(ticket)" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</button>
              <button @click="confirmDelete(ticket)" class="text-red-600 hover:text-red-900">Delete</button>
            </td>
          </tr>
          <tr v-if="ticketsStore.tickets.length === 0">
            <td colspan="6" class="text-center py-10 text-gray-500">No tickets found.</td>
          </tr>
        </tbody>
      </table>
      
      <!-- Pagination -->
      <div class="px-5 py-5 bg-white border-t flex flex-col xs:flex-row items-center xs:justify-between">
        <span class="text-xs xs:text-sm text-gray-900">
          Showing {{ ticketsStore.tickets.length }} of {{ ticketsStore.pagination.total }} tickets
        </span>
        <div class="inline-flex mt-2 xs:mt-0">
          <button 
            @click="changePage(ticketsStore.pagination.currentPage - 1)" 
            :disabled="ticketsStore.pagination.currentPage <= 1"
            class="text-sm bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-l disabled:opacity-50">
              Prev
          </button>
          <button 
            @click="changePage(ticketsStore.pagination.currentPage + 1)"
            :disabled="ticketsStore.pagination.currentPage >= ticketsStore.pagination.totalPages"
            class="text-sm bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-r disabled:opacity-50">
              Next
          </button>
        </div>
      </div>
    </div>

    <!-- Ticket Form Modal -->
    <TicketFormModal 
      v-if="isModalOpen" 
      :ticket="selectedTicket"
      @close="closeModal" 
      @save="handleSave" 
    />

  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useTicketsStore } from '@/stores/tickets';
import { useReferentialStore } from '@/stores/referentials';
import TicketFormModal from '../components/Tickets/TicketFormModal.vue';
import { useAuthStore } from '@/stores';

const ticketsStore = useTicketsStore();
const referentialStore = useReferentialStore();
const authStore = useAuthStore();

const isModalOpen = ref(false);
const selectedTicket = ref(null);

const filters = ref({
  project_id: '',
  status_id: '',
  priority_id: '',
  type_id: '',
});

onMounted(() => {
  referentialStore.fetchAllReferentials();
  ticketsStore.fetchTickets(1, filters.value);
});

watch(filters, () => {
  ticketsStore.fetchTickets(1, filters.value);
}, { deep: true });

const changePage = (page) => {
  if (page > 0 && page <= ticketsStore.pagination.totalPages) {
    ticketsStore.fetchTickets(page, filters.value);
  }
};

const openCreateModal = () => {
  selectedTicket.value = null;
  isModalOpen.value = true;
};

const openEditModal = (ticket) => {
  selectedTicket.value = { ...ticket };
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
  selectedTicket.value = null;
};

const handleSave = async (ticketData) => {
  if (ticketData.id) {
    await ticketsStore.updateTicket(ticketData.id, ticketData);
  } else {
    await ticketsStore.createTicket(ticketData);
  }
  if (!ticketsStore.error) {
    closeModal();
  }
};

const confirmDelete = (ticket) => {
  if (window.confirm(`Are you sure you want to delete the ticket "${ticket.name}"?`)) {
    ticketsStore.deleteTicket(ticket.id);
  }
};
</script>
