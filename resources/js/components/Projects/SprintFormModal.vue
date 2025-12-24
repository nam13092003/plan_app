<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center z-50" @click.self="$emit('close')">
    <div class="relative mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <h3 class="text-lg leading-6 font-medium text-gray-900 text-center">
          {{ sprint ? 'Edit Sprint' : 'Create Sprint' }}
        </h3>
        <form @submit.prevent="handleSubmit" class="mt-4 px-4 py-3 text-left space-y-4">
          
          <div v-if="error" class="mb-4 rounded-md bg-red-50 p-4">
            <p class="text-sm font-medium text-red-800">{{ typeof error === 'string' ? error : JSON.stringify(error) }}</p>
          </div>

          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Sprint Name *</label>
            <input 
              type="text" 
              id="name" 
              v-model="form.name" 
              required 
              class="mt-1 form-input" 
              placeholder="e.g., Sprint 1 - Q1 2025"
            />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="starts_at" class="block text-sm font-medium text-gray-700">Start Date *</label>
              <input 
                type="date" 
                id="starts_at" 
                v-model="form.starts_at" 
                required 
                class="mt-1 form-input"
              />
            </div>
            <div>
              <label for="ends_at" class="block text-sm font-medium text-gray-700">End Date *</label>
              <input 
                type="date" 
                id="ends_at" 
                v-model="form.ends_at" 
                required 
                :min="form.starts_at"
                class="mt-1 form-input"
              />
            </div>
          </div>

          <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea 
              id="description" 
              v-model="form.description" 
              rows="4"
              class="mt-1 form-input"
              placeholder="Sprint goals, key deliverables, etc."
            ></textarea>
          </div>

          <div class="flex justify-end space-x-3 pt-4">
            <button
              type="button"
              @click="$emit('close')"
              class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="loading"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
            >
              {{ loading ? 'Saving...' : (sprint ? 'Update' : 'Create') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { useProjectsStore } from '@/stores/projects';

const props = defineProps({
  sprint: {
    type: Object,
    default: null,
  },
  projectId: {
    type: Number,
    required: true,
  },
});

const emit = defineEmits(['close', 'save']);

const projectsStore = useProjectsStore();

const form = ref({
  name: '',
  starts_at: '',
  ends_at: '',
  description: '',
});

const localError = ref(null);
const loading = ref(false);

// Combine local errors with store errors
const error = computed(() => localError.value || projectsStore.error);

watch(() => props.sprint, (newSprint) => {
  if (newSprint) {
    form.value = {
      name: newSprint.name || '',
      starts_at: newSprint.starts_at ? newSprint.starts_at.split('T')[0] : '',
      ends_at: newSprint.ends_at ? newSprint.ends_at.split('T')[0] : '',
      description: newSprint.description || '',
    };
  } else {
    form.value = {
      name: '',
      starts_at: '',
      ends_at: '',
      description: '',
    };
  }
  localError.value = null;
  projectsStore.error = null;
}, { immediate: true });

const handleSubmit = () => {
  console.log('SprintFormModal: handleSubmit called', form.value);
  localError.value = null;
  projectsStore.error = null; // Clear store error
  
  // Validate required fields
  if (!form.value.name || !form.value.name.trim()) {
    localError.value = 'Sprint name is required';
    console.log('Validation failed: name required');
    return;
  }

  if (!form.value.starts_at) {
    localError.value = 'Start date is required';
    console.log('Validation failed: start date required');
    return;
  }

  if (!form.value.ends_at) {
    localError.value = 'End date is required';
    console.log('Validation failed: end date required');
    return;
  }
  
  // Validate dates
  if (new Date(form.value.ends_at) < new Date(form.value.starts_at)) {
    localError.value = 'End date must be after start date';
    console.log('Validation failed: end date before start date');
    return;
  }

  const sprintData = {
    id: props.sprint?.id,
    ...form.value,
  };
  
  console.log('SprintFormModal: Emitting save event', sprintData);
  emit('save', sprintData);
};
</script>

<style scoped>
.form-input {
  @apply block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm;
}
</style>

