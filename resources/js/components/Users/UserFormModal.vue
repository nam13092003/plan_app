
<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center" @click.self="$emit('close')">
    <div class="relative mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
      <div class="mt-3 text-center">
        <h3 class="text-lg leading-6 font-medium text-gray-900">{{ formTitle }}</h3>
        <form @submit.prevent="submitForm" class="mt-2 px-7 py-3 text-left">
          
          <div v-if="usersStore.error" class="mb-4 rounded-md bg-red-50 p-4">
            <p class="text-sm font-medium text-red-800">{{ usersStore.error }}</p>
          </div>

          <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
            <input type="text" id="name" v-model="form.name" required
                   class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
          </div>

          <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" id="email" v-model="form.email" required
                   class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
          </div>

          <div v-if="!isEditMode" class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" id="password" v-model="form.password" :required="!isEditMode"
                   class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
          </div>
          
          <div v-if="!isEditMode" class="mb-4">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <input type="password" id="password_confirmation" v-model="form.password_confirmation" :required="!isEditMode"
                   class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
          </div>

          <div class="mb-4">
            <label for="roles" class="block text-sm font-medium text-gray-700">Roles</label>
            <select id="roles" v-model="form.roles" multiple
                    class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
              <option v-for="role in referentialStore.roles" :key="role.id" :value="role.id">
                {{ role.name }}
              </option>
            </select>
            <p class="text-xs text-gray-500 mt-1">Hold Ctrl or Cmd to select multiple roles.</p>
          </div>


          <div class="items-center px-4 py-3">
            <button type="submit" :disabled="usersStore.loading"
                    class="w-full px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50">
              {{ usersStore.loading ? 'Saving...' : 'Save User' }}
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

<script setup>
import { ref, watch, computed } from 'vue';
import { useUsersStore } from '@/stores/users';
import { useReferentialStore } from '@/stores/referentials';

const props = defineProps({
  user: {
    type: Object,
    default: null,
  },
});

const emit = defineEmits(['close', 'save']);

const usersStore = useUsersStore();
const referentialStore = useReferentialStore();

const form = ref({
  id: null,
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  roles: [],
});

const isEditMode = computed(() => !!props.user);
const formTitle = computed(() => (isEditMode.value ? 'Edit User' : 'Create New User'));

watch(
  () => props.user,
  (newUser) => {
    if (newUser) {
      form.value = {
        id: newUser.id,
        name: newUser.name,
        email: newUser.email,
        password: '',
        password_confirmation: '',
        roles: newUser.roles.map(role => role.id), // We need role IDs for the select input
      };
    } else {
      form.value = {
        id: null,
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        roles: [],
      };
    }
    usersStore.error = null;
  },
  { immediate: true }
);

const submitForm = () => {
  const payload = { ...form.value };
  // Don't send password fields on edit if they are empty
  if (isEditMode.value && !payload.password) {
    delete payload.password;
    delete payload.password_confirmation;
  }
  emit('save', payload);
};
</script>
