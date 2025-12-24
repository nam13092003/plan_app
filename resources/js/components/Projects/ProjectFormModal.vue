<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center" @click.self="$emit('close')">
    <div class="relative mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <h3 class="text-lg leading-6 font-medium text-gray-900 text-center">{{ formTitle }}</h3>
        <form @submit.prevent="submitForm" class="mt-4 px-4 py-3 text-left space-y-4">
          
          <div v-if="projectsStore.error" class="mb-4 rounded-md bg-red-50 p-4">
            <p class="text-sm font-medium text-red-800">{{ projectsStore.error }}</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700">Project Name</label>
              <input type="text" id="name" v-model="form.name" required class="mt-1 form-input" />
            </div>
            <div>
              <label for="ticket_prefix" class="block text-sm font-medium text-gray-700">Ticket Prefix</label>
              <input type="text" id="ticket_prefix" v-model="form.ticket_prefix" required placeholder="e.g., PROJ" class="mt-1 form-input" />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Project Type</label>
            <div class="mt-2 grid grid-cols-2 gap-4">
              <div 
                @click="form.type = 'kanban'"
                :class="['p-4 border rounded-lg cursor-pointer text-center', form.type === 'kanban' ? 'border-blue-500 ring-2 ring-blue-500' : 'border-gray-300']">
                <h4 class="font-semibold">Kanban</h4>
                <p class="text-xs text-gray-500">For continuous workflows.</p>
              </div>
              <div 
                @click="form.type = 'scrum'"
                :class="['p-4 border rounded-lg cursor-pointer text-center', form.type === 'scrum' ? 'border-blue-500 ring-2 ring-blue-500' : 'border-gray-300']">
                <h4 class="font-semibold">Scrum</h4>
                <p class="text-xs text-gray-500">For iterative development.</p>
              </div>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Ticket Statuses</label>
            <div class="mt-2 grid grid-cols-2 gap-4">
              <div 
                @click="form.status_type = 'default'"
                :class="['p-4 border rounded-lg cursor-pointer text-center', form.status_type === 'default' ? 'border-blue-500 ring-2 ring-blue-500' : 'border-gray-300']">
                <h4 class="font-semibold">Default</h4>
                <p class="text-xs text-gray-500">Use system's default statuses.</p>
              </div>
              <div 
                @click="form.status_type = 'custom'"
                :class="['p-4 border rounded-lg cursor-pointer text-center', form.status_type === 'custom' ? 'border-blue-500 ring-2 ring-blue-500' : 'border-gray-300']">
                <h4 class="font-semibold">Custom</h4>
                <p class="text-xs text-gray-500">Manage statuses for this project only.</p>
              </div>
            </div>
          </div>

          <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea id="description" v-model="form.description" rows="3" class="mt-1 form-input"></textarea>
          </div>

          <div class="items-center pt-4">
            <button type="submit" :disabled="projectsStore.loading"
                    class="w-full px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50">
              {{ projectsStore.loading ? 'Saving...' : 'Save Project' }}
            </button>
            <button type="button" @click="$emit('close')"
                    class="w-full px-4 py-2 mt-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
.form-input, .form-select {
  @apply block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm;
}
</style>

<script setup>
import { ref, watch, computed } from 'vue';
import { useProjectsStore } from '@/stores/projects';

const props = defineProps({
  project: {
    type: Object,
    default: null,
  },
});

const emit = defineEmits(['close', 'save']);

const projectsStore = useProjectsStore();

const form = ref({
  id: null,
  name: '',
  description: '',
  ticket_prefix: '',
  type: 'kanban', // Default to kanban
  status_type: 'default', // Default to default statuses
});

const formTitle = computed(() => (props.project ? 'Edit Project' : 'Create New Project'));

// Watch for changes in the prop and update the form
watch(
  () => props.project,
  (newProject) => {
    if (newProject) {
      form.value = {
        id: newProject.id,
        name: newProject.name,
        description: newProject.description || '',
        ticket_prefix: newProject.ticket_prefix,
        type: newProject.type,
        status_type: newProject.status_type || 'basic',
      };
    } else {
      form.value = {
        id: null,
        name: '',
        description: '',
        ticket_prefix: '',
        type: 'kanban',
        status_type: 'basic',
      };
    }
    projectsStore.error = null;
  },
  { immediate: true }
);

// Auto-generate ticket prefix from project name
watch(() => form.value.name, (newName) => {
  if (!props.project) { // Only auto-generate for new projects
    form.value.ticket_prefix = newName
      .split(' ')
      .map(word => word.charAt(0))
      .join('')
      .toUpperCase()
      .substring(0, 5);
  }
});

const submitForm = () => {
  // Use FormData to handle file uploads
  const formData = new FormData();
  Object.keys(form.value).forEach(key => {
    if (key !== 'id' && form.value[key] !== null) {
      formData.append(key, form.value[key]);
    }
  });

  // For PUT requests, Laravel doesn't handle FormData well for file uploads,
  // so we need to append _method.
  if (form.value.id) {
      formData.append('_method', 'PUT');
  }

  emit('save', { id: form.value.id, data: formData });
};
</script>