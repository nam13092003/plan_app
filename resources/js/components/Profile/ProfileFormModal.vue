<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center" @click.self="$emit('close')">
    <div class="relative mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <h3 class="text-lg leading-6 font-medium text-gray-900 text-center">Edit Profile</h3>
        <form @submit.prevent="submitForm" class="mt-4 px-4 py-3 text-left space-y-4">
          
          <div v-if="usersStore.error" class="mb-4 rounded-md bg-red-50 p-4">
            <p class="text-sm font-medium text-red-800">{{ usersStore.error }}</p>
          </div>

          <div>
            <label for="profile-name" class="block text-sm font-medium text-gray-700">Full Name</label>
            <input type="text" id="profile-name" v-model="form.name" required class="mt-1 form-input" />
          </div>

          <div>
            <label for="profile-password" class="block text-sm font-medium text-gray-700">New Password (optional)</label>
            <input type="password" id="profile-password" v-model="form.password" class="mt-1 form-input" />
          </div>
          
          <div>
            <label for="profile-password-confirm" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
            <input type="password" id="profile-password-confirm" v-model="form.password_confirmation" class="mt-1 form-input" />
          </div>

          <div class="items-center pt-4">
            <button type="submit" :disabled="usersStore.loading"
                    class="w-full px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50">
              {{ usersStore.loading ? 'Saving...' : 'Save Changes' }}
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
import { ref, watch } from 'vue';
import { useUsersStore } from '@/stores/users';

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['close', 'save']);

const usersStore = useUsersStore();

const form = ref({
  name: '',
  password: '',
  password_confirmation: '',
});

watch(
  () => props.user,
  (newUser) => {
    if (newUser) {
      form.value.name = newUser.name;
    }
    // Reset password fields
    form.value.password = '';
    form.value.password_confirmation = '';
    usersStore.error = null;
  },
  { immediate: true }
);

const submitForm = () => {
  const payload = {
    name: form.value.name,
  };

  // Only include password in payload if it's being changed
  if (form.value.password) {
    if (form.value.password !== form.value.password_confirmation) {
        usersStore.error = "Passwords do not match.";
        return;
    }
    payload.password = form.value.password;
    payload.password_confirmation = form.value.password_confirmation;
  }
  
  emit('save', payload);
};
</script>
