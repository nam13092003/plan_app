<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center" @click.self="$emit('close')">
    <div class="relative mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <h3 class="text-lg leading-6 font-medium text-gray-900 text-center">Create New Epic</h3>
        <form @submit.prevent="submitForm" class="mt-4 px-4 py-3 text-left space-y-4">
          
          <div>
            <label for="epic-name" class="block text-sm font-medium text-gray-700">Epic Name</label>
            <input type="text" id="epic-name" v-model="form.name" required class="mt-1 form-input" />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label for="epic-start-date" class="block text-sm font-medium text-gray-700">Start Date</label>
              <input type="date" id="epic-start-date" v-model="form.starts_at" class="mt-1 form-input" />
            </div>
            <div>
              <label for="epic-end-date" class="block text-sm font-medium text-gray-700">End Date</label>
              <input type="date" id="epic-end-date" v-model="form.ends_at" class="mt-1 form-input" />
            </div>
          </div>

          <div class="items-center pt-4">
            <button type="submit"
                    class="w-full px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
              Save Epic
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
.form-input {
  @apply block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm;
}
</style>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  projectId: {
    type: [String, Number],
    required: true,
  }
});

const emit = defineEmits(['close', 'save']);

const form = ref({
  name: '',
  project_id: props.projectId,
  starts_at: '',
  ends_at: '',
});

const submitForm = () => {
  emit('save', { ...form.value });
};
</script>
