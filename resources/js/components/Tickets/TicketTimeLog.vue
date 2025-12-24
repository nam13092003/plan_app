
<template>
  <div class="space-y-4">
    <!-- Progress Bar -->
    <div>
      <div class="flex justify-between mb-1">
        <span class="text-base font-medium text-blue-700">Logged: {{ ticket.total_logged_hours || 0 }}h</span>
        <span class="text-sm font-medium text-gray-500">Estimated: {{ ticket.estimation || 0 }}h</span>
      </div>
      <div class="w-full bg-gray-200 rounded-full h-2.5">
        <div class="bg-blue-600 h-2.5 rounded-full" :style="{ width: progressPercentage }"></div>
      </div>
    </div>

    <!-- Log Work Form -->
    <form @submit.prevent="submitLog" class="space-y-3">
      <div>
        <label for="hours" class="block text-sm font-medium text-gray-700">Hours</label>
        <input type="number" id="hours" v-model="form.value" step="0.1" required class="mt-1 form-input" placeholder="e.g., 1.5">
      </div>
      <div>
        <label for="activity" class="block text-sm font-medium text-gray-700">Activity</label>
        <select id="activity" v-model="form.activity_id" required class="mt-1 form-select">
          <option v-for="activity in referentialStore.activities" :key="activity.id" :value="activity.id">{{ activity.name }}</option>
        </select>
      </div>
      <div>
        <label for="log-comment" class="block text-sm font-medium text-gray-700">Comment</label>
        <input type="text" id="log-comment" v-model="form.comment" class="mt-1 form-input">
      </div>
      <button type="submit" class="w-full px-4 py-2 text-white bg-green-600 rounded-md hover:bg-green-700">Log Work</button>
    </form>

    <!-- History -->
    <div>
      <h4 class="font-semibold mb-2">History</h4>
      <ul class="space-y-2">
        <li v-for="hour in ticket.hours" :key="hour.id" class="text-sm text-gray-600">
          <span class="font-bold">{{ hour.value }}h</span> on {{ new Date(hour.created_at).toLocaleDateString() }} by {{ hour.user.name }}
          <p v-if="hour.comment" class="pl-2 text-xs">- "{{ hour.comment }}"</p>
        </li>
        <li v-if="!ticket.hours || ticket.hours.length === 0" class="text-sm text-gray-500">No time logged yet.</li>
      </ul>
    </div>
  </div>
</template>

<style scoped>
.form-input, .form-select {
  @apply block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm;
}
</style>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useTicketsStore } from '@/stores/tickets';
import { useReferentialStore } from '@/stores/referentials';

const props = defineProps({
  ticket: {
    type: Object,
    required: true,
  },
});

const ticketsStore = useTicketsStore();
const referentialStore = useReferentialStore();

const form = ref({
  value: '',
  activity_id: referentialStore.activities[0]?.id || '',
  comment: '',
});

const progressPercentage = computed(() => {
  if (!props.ticket.estimation || props.ticket.estimation == 0) return '0%';
  const percentage = (props.ticket.total_logged_hours / props.ticket.estimation) * 100;
  return `${Math.min(percentage, 100)}%`;
});

onMounted(() => {
  ticketsStore.fetchHours(props.ticket.id);
  // Ensure activities are available
  if (referentialStore.activities.length === 0) {
      referentialStore.fetchAllReferentials();
  }
});

const submitLog = async () => {
  if (!form.value.value) return;
  await ticketsStore.logHours(props.ticket.id, form.value);
  // Reset form
  form.value.value = '';
  form.value.comment = '';
};
</script>
