<template>
  <div>
    <div class="flex justify-end mb-6">
      <button v-if="canManage" @click="openCreateModal" class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700">
        Create Ticket
      </button>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
      <table class="min-w-full leading-normal">
        <thead>
          <tr>
            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ticket</th>
            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Priority</th>
            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Assignee</th>
            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
              Start Date
            </th>
            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
              Due Date
            </th>
          </tr>
        </thead>
<tbody>
  <tr
    v-for="ticket in tickets"
    :key="ticket.id"
    class="hover:bg-gray-50 cursor-pointer"
    @click="openTicketDetail(ticket)"
  >
    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
      <p class="text-gray-900 font-bold">{{ ticket.code }}</p>
      <p class="text-gray-600">{{ ticket.name }}</p>
    </td>
    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
      <span
        class="relative inline-block px-3 py-1 font-semibold leading-tight"
        :style="{ color: ticket.status?.color }"
      >
        <span
          aria-hidden
          class="absolute inset-0 opacity-20 rounded-full"
          :style="{ backgroundColor: ticket.status?.color }"
        ></span>
        <span class="relative">{{ ticket.status?.name || 'N/A' }}</span>
      </span>
    </td>
    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
      {{ ticket.priority?.name }}
    </td>
    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
      {{ ticket.responsible?.name || 'Unassigned' }}
    </td>
<td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
  <span v-if="ticket.start_date">
    {{ formatDate(ticket.start_date) }}
  </span>
  <span v-else class="text-gray-400">—</span>
</td>

<td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
  <span
    v-if="ticket.due_date"
    :class="isOverdue(ticket) ? 'text-red-600 font-semibold' : ''"
  >
    {{ formatDate(ticket.due_date) }}
  </span>
  <span v-else class="text-gray-400">—</span>
</td>
  </tr>
</tbody>
      </table>
    </div>

    <!-- Ticket Form Modal -->
    <TicketFormModal 
      v-if="isModalOpen" 
      :ticket="selectedTicket"
      @close="closeModal" 
      @save="handleSave" 
    />

    <!-- Ticket Detail Modal -->
<TicketDetailModal
  v-if="showDetailModal"
  :ticket="selectedTicketDetail"
  @close="showDetailModal = false"
  @edit="editTicket"
  @deleted="onTicketDeleted"
/>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useAuthStore } from '@/stores';
import { useTicketsStore } from '@/stores/tickets';
import TicketFormModal from '../Tickets/TicketFormModal.vue';
import TicketDetailModal from '../Tickets/TicketDetailModal.vue'

const props = defineProps({
  tickets: {
    type: Array,
    required: true,
  },
  project: {
    type: Object,
    required: true,
  }
});

const emit = defineEmits(['ticket-created']);

const authStore = useAuthStore();
const ticketsStore = useTicketsStore();

const isModalOpen = ref(false);
const selectedTicket = ref(null);
const editingTicket = ref(null);

const canManage = computed(() => {
  if (!authStore.user || !props.project) {
    return false;
  }
  // Check if the current user is the owner of the project.
  return authStore.user.id === props.project.owner_id;
});

const openCreateModal = () => {
  selectedTicket.value = {
    project_id: props.project.id, // Pre-fill project_id
    status_id: 1, // Gán mặc định status_id là 1 (thường là "Todo" hoặc "Open")
  };
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
  selectedTicket.value = null;
};

const handleSave = async (formData) => {
  try {
    // SỬA: Thay props.projectId bằng props.project.id
    const payload = { 
      ...formData, 
      project_id: props.project.id // Lấy ID từ object project có sẵn trong props
    };

    if (editingTicket.value) {
      await ticketsStore.updateTicket(editingTicket.value.id, payload);
    } else {
      await ticketsStore.createTicket(payload);
    }
    
    isModalOpen.value = false;
    editingTicket.value = null;
    
    // Emit để ProjectDetail load lại dữ liệu mới
    emit('ticket-created'); 
  } catch (error) {
    console.error('Lỗi khi lưu ticket:', error);
  }
};

const showDetailModal = ref(false)
const selectedTicketDetail = ref(null)

const openTicketDetail = async (ticket) => {
  await ticketsStore.fetchTicket(ticket.id)
  selectedTicketDetail.value = ticketsStore.ticket
  showDetailModal.value = true
}

const editTicket = (ticket) => {
  editingTicket.value = ticket
  selectedTicket.value = { ...ticket }
  showDetailModal.value = false
  isModalOpen.value = true
}

const formatDate = (date) => {
  if (!date) return '';
  return new Date(date).toLocaleDateString('vi-VN', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  });
};

const isOverdue = (ticket) => {
  if (!ticket.due_date) return false;

  // nếu ticket đã closed thì không overdue
  if (
    ticket.status?.code === 'closed' ||
    ticket.status?.is_closed === true
  ) {
    return false;
  }

  return new Date(ticket.due_date) < new Date();
};

const onTicketDeleted = async () => {
  showDetailModal.value = false
  selectedTicketDetail.value = null

  // reload list ticket
  emit('ticket-created')
}
</script>
