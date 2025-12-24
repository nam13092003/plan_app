
<template>
  <div class="space-y-6">
    <!-- Add Member Form -->
    <div v-if="canManage" class="bg-white p-4 rounded-lg shadow">
      <h3 class="font-semibold text-lg mb-2">Add New Member</h3>
      <form @submit.prevent="handleAddUser" class="flex items-end space-x-4">
        <div class="flex-grow relative">
          <label for="user-search" class="block text-sm font-medium text-gray-700">User</label>
          <div class="relative mt-1">
            <input
              id="user-search"
              v-model="userSearchQuery"
              type="text"
              placeholder="Search by name or email..."
              autocomplete="off"
              class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              @focus="showUserSuggestions = true"
              @blur="handleUserSearchBlur"
              @input="showUserSuggestions = true"
            />
            <!-- User Suggestions Dropdown -->
            <div
              v-if="showUserSuggestions && filteredAvailableUsers.length > 0"
              class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto"
            >
              <ul class="py-1">
                <li
                  v-for="user in filteredAvailableUsers"
                  :key="user.id"
                  @mousedown.prevent="selectUser(user)"
                  class="px-4 py-2 hover:bg-blue-50 cursor-pointer"
                >
                  <div class="font-medium">{{ user.name }}</div>
                  <div class="text-sm text-gray-500">{{ user.email }}</div>
                </li>
              </ul>
            </div>
            <div
              v-if="showUserSuggestions && userSearchQuery && filteredAvailableUsers.length === 0"
              class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg"
            >
              <div class="px-4 py-2 text-gray-500 text-sm">No users found</div>
            </div>
            <!-- Selected User Display -->
            <div v-if="selectedUser" class="mt-2 p-2 bg-gray-50 rounded border border-gray-200">
              <div class="flex items-center justify-between">
                <div>
                  <div class="font-medium text-sm">{{ selectedUser.name }}</div>
                  <div class="text-xs text-gray-500">{{ selectedUser.email }}</div>
                </div>
                <button
                  type="button"
                  @click="clearSelectedUser"
                  class="text-gray-400 hover:text-gray-600 text-sm"
                >
                  ✕
                </button>
              </div>
            </div>
          </div>
        </div>
        <div>
          <label for="role-select" class="block text-sm font-medium text-gray-700">Role</label>
          <select id="role-select" v-model="newUser.role" required class="mt-1 form-select">
            <option v-for="role in roleOptions" :key="role.value" :value="role.value">
              {{ role.label }}
            </option>
          </select>
        </div>
        <button
          type="submit"
          :disabled="!selectedUser"
          class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 h-10 disabled:bg-gray-400 disabled:cursor-not-allowed"
        >
          Add Member
        </button>
      </form>
      <p v-if="projectsStore.error" class="text-sm text-red-600 mt-2">{{ projectsStore.error }}</p>
    </div>

    <!-- Members List -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
      <!-- Search Bar -->
      <div class="px-5 py-4 border-b border-gray-200">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search by name or email..."
          class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
        />
      </div>
      <table class="min-w-full leading-normal">
        <thead>
          <tr>
            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Role</th>
            <th v-if="canManage" class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="filteredMembers.length === 0">
            <td :colspan="canManage ? 3 : 2" class="px-5 py-5 text-center text-gray-500">
              No members found matching your search.
            </td>
          </tr>
          <tr v-for="member in filteredMembers" :key="member.id" class="hover:bg-gray-50">
            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
              <p class="font-semibold">{{ member.name }}</p>
              <p class="text-gray-600">{{ member.email }}</p>
            </td>
            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
              <select
                v-if="canManage"
                v-model="member.pivot.role"
                @change="updateRole(member.id, $event.target.value)"
                class="form-select py-1"
              >
                <option v-for="role in roleOptions" :key="role.value" :value="role.value">
                  {{ role.label }}
                </option>
              </select>
              <span v-else>{{ getRoleLabel(member.pivot.role) }}</span>
            </td>
            <td v-if="canManage" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right">
              <button @click="removeUser(member.id)" class="text-red-600 hover:text-red-900">Remove</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<style scoped>
.form-select {
  @apply block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm;
}
</style>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useProjectsStore } from '@/stores/projects';
import { useReferentialStore } from '@/stores/referentials';
import { useAuthStore } from '@/stores';

const props = defineProps({
  project: {
    type: Object,
    required: true,
  },
});

const projectsStore = useProjectsStore();
const referentialStore = useReferentialStore();
const authStore = useAuthStore();

// Backend expects one of: employee, customer, administrator
const roleOptions = [
  { value: 'employee', label: 'Member' },
  { value: 'administrator', label: 'Manager' },
  { value: 'customer', label: 'Viewer' },
];

const newUser = ref({
  id: '',
  role: roleOptions[0].value,
});

const searchQuery = ref('');
const userSearchQuery = ref('');
const showUserSuggestions = ref(false);
const selectedUser = ref(null);

const canManage = computed(() => {
  if (!authStore.user || !props.project) {
    return false;
  }
  // Check if the current user is the owner of the project.
  return authStore.user.id === props.project.owner_id;
});

// Filter out users who are already members of the project
const availableUsers = computed(() => {
  if (!props.project?.users) return referentialStore.users;
  const memberIds = new Set(props.project.users.map(m => m.id));
  return referentialStore.users.filter(u => !memberIds.has(u.id));
});

// Filter available users by search query (name or email)
const filteredAvailableUsers = computed(() => {
  if (!userSearchQuery.value.trim()) {
    return []; // Don't show suggestions until user starts typing
  }
  
  const query = userSearchQuery.value.toLowerCase().trim();
  return availableUsers.value.filter(user => {
    const name = (user.name || '').toLowerCase();
    const email = (user.email || '').toLowerCase();
    return name.includes(query) || email.includes(query);
  }).slice(0, 10); // Limit to 10 results
});

// Filter members by search query (name or email)
const filteredMembers = computed(() => {
  if (!props.project?.users) return [];
  
  if (!searchQuery.value.trim()) {
    return props.project.users;
  }
  
  const query = searchQuery.value.toLowerCase().trim();
  return props.project.users.filter(member => {
    const name = (member.name || '').toLowerCase();
    const email = (member.email || '').toLowerCase();
    return name.includes(query) || email.includes(query);
  });
});

onMounted(() => {
  referentialStore.fetchAllReferentials();
});

const selectUser = (user) => {
  selectedUser.value = user;
  newUser.value.id = user.id;
  userSearchQuery.value = '';
  showUserSuggestions.value = false;
};

const clearSelectedUser = () => {
  selectedUser.value = null;
  newUser.value.id = '';
  userSearchQuery.value = '';
  showUserSuggestions.value = false;
};

const handleUserSearchBlur = () => {
  // Delay to allow click on suggestion to register
  setTimeout(() => {
    showUserSuggestions.value = false;
  }, 200);
};

const handleAddUser = async () => {
  if (!newUser.value.id || !selectedUser.value) return;
  await projectsStore.attachUser(props.project.id, {
    user_id: newUser.value.id,
    role: newUser.value.role,
  });
  if (!projectsStore.error) {
    clearSelectedUser();
    newUser.value.role = roleOptions[0].value;
  }
};

const getRoleLabel = (value) => {
  const found = roleOptions.find(r => r.value === value);
  return found ? found.label : value;
};

const updateRole = async (userId, role) => {
  await projectsStore.updateUserRole(props.project.id, userId, role);
};

const removeUser = async (userId) => {
  if (window.confirm('Are you sure you want to remove this member from the project?')) {
    await projectsStore.detachUser(props.project.id, userId);
  }
};
</script>
