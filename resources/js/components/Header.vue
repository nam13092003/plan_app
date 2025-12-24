<template>
  <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-gray-200">
    <!-- Page Title -->
    <h1 class="text-xl font-semibold text-gray-800">{{ pageTitle }}</h1>

    <!-- Right side -->
    <div class="flex items-center space-x-4">

      <!-- User Profile -->
      <div class="relative">
        <button
          @click="dropdownOpen = !dropdownOpen"
          class="flex items-center space-x-2 focus:outline-none"
        >
          <span class="text-sm font-medium text-gray-700" v-if="authStore.user">
            {{ authStore.user.name }}
          </span>

          <img
            class="h-9 w-9 rounded-full object-cover border"
            :src="avatarUrl"
            :alt="authStore.user?.name"
          />
        </button>

        <!-- Overlay -->
        <div
          v-show="dropdownOpen"
          @click="dropdownOpen = false"
          class="fixed inset-0 h-full w-full z-30"
        ></div>

        <!-- Dropdown -->
        <transition
          enter-active-class="transition ease-out duration-100"
          enter-from-class="transform opacity-0 scale-95"
          enter-to-class="transform opacity-100 scale-100"
          leave-active-class="transition ease-in duration-75"
          leave-from-class="transform opacity-100 scale-100"
          leave-to-class="transform opacity-0 scale-95"
        >
          <div
            v-show="dropdownOpen"
            class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-40"
          >
            <router-link
              :to="{ name: 'UserProfile' }"
              class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
            >
              Your Profile
            </router-link>
            
            <div class="border-t border-gray-100"></div>

            <a
              href="#"
              @click.prevent="handleLogout"
              class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
            >
              Logout
            </a>
          </div>
        </transition>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const dropdownOpen = ref(false)

const pageTitle = computed(() => route.meta.title || 'Dashboard')

/**
 * ✅ Avatar fallback giống UI bạn đưa
 */
const avatarUrl = computed(() => {
  if (!authStore.user) return 'https://via.placeholder.com/150'

  return (
    authStore.user.avatar ||
    `https://ui-avatars.com/api/?name=${encodeURIComponent(
      authStore.user.name
    )}&background=0D8ABC&color=fff`
  )
})

const handleLogout = async () => {
  await authStore.logout()
  router.push({ name: 'Login' })
}
</script>
