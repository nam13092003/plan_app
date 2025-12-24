<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          {{ isRegisterMode ? 'Đăng ký tài khoản mới' : 'Đăng nhập vào tài khoản' }}
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          PLANORA
        </p>
      </div>

      <!-- Tabs để chuyển đổi giữa Đăng nhập và Đăng ký -->
      <div class="flex rounded-lg bg-gray-100 p-1">
        <button
          @click="isRegisterMode = false"
          :class="[
            'flex-1 rounded-md py-2 px-4 text-sm font-medium transition-colors',
            !isRegisterMode
              ? 'bg-white text-gray-900 shadow-sm'
              : 'text-gray-600 hover:text-gray-900'
          ]"
        >
          Đăng nhập
        </button>
        <button
          @click="isRegisterMode = true"
          :class="[
            'flex-1 rounded-md py-2 px-4 text-sm font-medium transition-colors',
            isRegisterMode
              ? 'bg-white text-gray-900 shadow-sm'
              : 'text-gray-600 hover:text-gray-900'
          ]"
        >
          Đăng ký
        </button>
      </div>
      
      <!-- Form đăng nhập -->
      <form v-if="!isRegisterMode" class="mt-8 space-y-6" @submit.prevent="handleLogin">
        <div v-if="authStore.error" class="rounded-md bg-red-50 p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-red-800">{{ authStore.error }}</p>
            </div>
          </div>
        </div>

        <div class="rounded-md shadow-sm -space-y-px">
          <div>
            <label for="login-email" class="sr-only">Email</label>
            <input
              id="login-email"
              v-model="loginForm.email"
              name="email"
              type="email"
              autocomplete="email"
              required
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
              placeholder="Email address"
            />
          </div>
          <div>
            <label for="login-password" class="sr-only">Mật khẩu</label>
            <input
              id="login-password"
              v-model="loginForm.password"
              name="password"
              type="password"
              autocomplete="current-password"
              required
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
              placeholder="Password"
            />
          </div>
        </div>

        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <input
              id="remember-me"
              v-model="loginForm.remember"
              name="remember-me"
              type="checkbox"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            />
            <label for="remember-me" class="ml-2 block text-sm text-gray-900">
              Ghi nhớ đăng nhập
            </label>
          </div>
        </div>

        <div>
          <button
            type="submit"
            :disabled="authStore.loading"
            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="authStore.loading">Đang đăng nhập...</span>
            <span v-else>Đăng nhập</span>
          </button>
        </div>
      </form>

      <!-- Form đăng ký -->
      <form v-else class="mt-8 space-y-6" @submit.prevent="handleRegister">
        <div v-if="authStore.error" class="rounded-md bg-red-50 p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-red-800">{{ authStore.error }}</p>
            </div>
          </div>
        </div>

        <div class="rounded-md shadow-sm -space-y-px">
          <div>
            <label for="register-name" class="sr-only">Họ và tên</label>
            <input
              id="register-name"
              v-model="registerForm.name"
              name="name"
              type="text"
              autocomplete="name"
              required
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
              placeholder="Họ và tên"
            />
          </div>
          <div>
            <label for="register-email" class="sr-only">Email</label>
            <input
              id="register-email"
              v-model="registerForm.email"
              name="email"
              type="email"
              autocomplete="email"
              required
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
              placeholder="Email address"
            />
          </div>
          <div>
            <label for="register-password" class="sr-only">Mật khẩu</label>
            <input
              id="register-password"
              v-model="registerForm.password"
              name="password"
              type="password"
              autocomplete="new-password"
              required
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
              placeholder="Mật khẩu"
            />
          </div>
          <div>
            <label for="register-password-confirm" class="sr-only">Xác nhận mật khẩu</label>
            <input
              id="register-password-confirm"
              v-model="registerForm.password_confirmation"
              name="password_confirmation"
              type="password"
              autocomplete="new-password"
              required
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
              placeholder="Xác nhận mật khẩu"
            />
          </div>
        </div>

        <div>
          <button
            type="submit"
            :disabled="authStore.loading"
            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="authStore.loading">Đang đăng ký...</span>
            <span v-else>Đăng ký</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores';

export default {
  name: 'Login',
  setup() {
    const router = useRouter();
    const authStore = useAuthStore();
    
    const isRegisterMode = ref(false);
    
    const loginForm = ref({
      email: '',
      password: '',
      remember: false
    });

    const registerForm = ref({
      name: '',
      email: '',
      password: '',
      password_confirmation: ''
    });

    const handleLogin = async () => {
      authStore.clearError();
      const result = await authStore.login(loginForm.value);
      
      if (result.success) {
        router.push('/dashboard');
      }
    };

    const handleRegister = async () => {
      authStore.clearError();
      const result = await authStore.register(registerForm.value);
      
      if (result.success) {
        router.push('/dashboard');
      }
    };

    onMounted(() => {
      // Nếu đã đăng nhập, redirect đến dashboard
      if (authStore.isAuthenticated) {
        router.push('/dashboard');
      }
    });

    return {
      isRegisterMode,
      loginForm,
      registerForm,
      authStore,
      handleLogin,
      handleRegister
    };
  }
};
</script>

