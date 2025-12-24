<template>
  <div class="bg-white p-6 rounded-lg shadow-md text-center">
    <!-- Avatar -->
    <img
      class="h-32 w-32 rounded-full object-cover mx-auto mb-4 ring-4 ring-blue-100"
      :src="avatarUrl"
      :alt="user.name"
    />

    <!-- Info -->
    <h2 class="text-2xl font-bold text-gray-800">
      {{ user.name }}
    </h2>
    <p class="text-gray-500">
      {{ user.email }}
    </p>

    <!-- Roles -->
    <div v-if="user.roles?.length" class="mt-4 flex justify-center flex-wrap gap-2">
      <span
        v-for="role in user.roles"
        :key="role.id"
        class="px-3 py-1 text-sm font-medium rounded-full"
        :class="roleClass(role.name)"
      >
        {{ formatRole(role.name) }}
      </span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
});

/**
 * Avatar fallback
 */
const avatarUrl = computed(() => {
  return (
    props.user.avatar ||
    `https://ui-avatars.com/api/?name=${encodeURIComponent(
      props.user.name
    )}&background=0D8ABC&color=fff`
  );
});

/**
 * Role badge color
 */
const roleClass = (role) => {
  const classes = {
    manager: 'bg-blue-100 text-blue-800',
    member: 'bg-green-100 text-green-800',
    viewer: 'bg-gray-100 text-gray-800',
  };
  return classes[role] || 'bg-gray-100 text-gray-800';
};

const formatRole = (role) =>
  role.charAt(0).toUpperCase() + role.slice(1);
</script>