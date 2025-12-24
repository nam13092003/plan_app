<template>
  <div>
    <div class="flex justify-end mb-6">
      <button @click="openCreateModal" class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700">
        Create User
      </button>
    </div>

    <!-- Loading and Error States -->
    <div v-if="usersStore.loading" class="text-center py-10">Loading users...</div>
    <div v-else-if="usersStore.error" class="text-center py-10 text-red-500">{{ usersStore.error }}</div>

    <!-- Users Table -->
    <div v-else class="bg-white shadow-md rounded-lg overflow-hidden">
      <table class="min-w-full leading-normal">
        <thead>
          <tr>
            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
              Name
            </th>
            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
              Email
            </th>
            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
              Roles
            </th>
            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in usersStore.users" :key="user.id" class="hover:bg-gray-50">
            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
              {{ user.name }}
            </td>
            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
              {{ user.email }}
            </td>
            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
              <span v-for="role in user.roles" :key="role.id" class="mr-2 inline-block px-2 py-1 text-xs font-semibold text-gray-700 bg-gray-200 rounded-full">
                {{ role.name }}
              </span>
            </td>
            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right">
              <button @click="openEditModal(user)" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</button>
              <button @click="confirmDelete(user)" class="text-red-600 hover:text-red-900">Delete</button>
            </td>
          </tr>
           <tr v-if="usersStore.users.length === 0">
            <td colspan="4" class="text-center py-10 text-gray-500">No users found.</td>
          </tr>
        </tbody>
      </table>
      
      <!-- Pagination -->
      <div class="px-5 py-5 bg-white border-t flex flex-col xs:flex-row items-center xs:justify-between">
        <span class="text-xs xs:text-sm text-gray-900">
          Showing {{ usersStore.users.length }} of {{ usersStore.pagination.total }} users
        </span>
        <div class="inline-flex mt-2 xs:mt-0">
          <button 
            @click="changePage(usersStore.pagination.currentPage - 1)" 
            :disabled="usersStore.pagination.currentPage <= 1"
            class="text-sm bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-l disabled:opacity-50">
              Prev
          </button>
          <button 
            @click="changePage(usersStore.pagination.currentPage + 1)"
            :disabled="usersStore.pagination.currentPage >= usersStore.pagination.totalPages"
            class="text-sm bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-r disabled:opacity-50">
              Next
          </button>
        </div>
      </div>
    </div>

    <!-- User Form Modal -->
    <UserFormModal 
      v-if="isModalOpen" 
      :user="selectedUser"
      @close="closeModal" 
      @save="handleSave" 
    />

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useUsersStore } from '@/stores/users';
import { useReferentialStore } from '@/stores/referentials';
import UserFormModal from '../components/Users/UserFormModal.vue';

const usersStore = useUsersStore();
const referentialStore = useReferentialStore();
const isModalOpen = ref(false);
const selectedUser = ref(null);

onMounted(() => {
  usersStore.fetchUsers();
  referentialStore.fetchAllReferentials(); // To get roles
});

const changePage = (page) => {
  if (page > 0 && page <= usersStore.pagination.totalPages) {
    usersStore.fetchUsers(page);
  }
};

const openCreateModal = () => {
  selectedUser.value = null;
  isModalOpen.value = true;
};

const openEditModal = (user) => {
  selectedUser.value = { ...user };
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
  selectedUser.value = null;
};

const handleSave = async (userData) => {
  if (userData.id) {
    // Update
    await usersStore.updateUser(userData.id, userData);
  } else {
    // Create
    await usersStore.createUser(userData);
  }
  if (!usersStore.error) {
    closeModal();
  }
};

const confirmDelete = (user) => {
  if (window.confirm(`Are you sure you want to delete the user "${user.name}"?`)) {
    usersStore.deleteUser(user.id);
  }
};
</script>