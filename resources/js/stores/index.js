import { defineStore } from 'pinia';
import api from '../services/api';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    isAuthenticated: false,
    loading: false,
    error: null,
  }),
  
  getters: {
    isLoggedIn: (state) => state.isAuthenticated && state.user !== null,
    can: (state) => (permission) => {
      if (state.user && state.user.permissions) {
        return state.user.permissions.includes(permission);
      }
      return false;
    },
  },
  
  actions: {
    async login(credentials) {
      this.loading = true;
      this.error = null;
      
      try {
        // Lấy CSRF cookie trước khi login
        await window.axios.get('/sanctum/csrf-cookie');
        
        // Thực hiện login
        const response = await api.post('/login', credentials);
        
        this.user = response.data.user;
        this.isAuthenticated = true;
        this.loading = false;
        
        return { success: true };
      } catch (error) {
        this.loading = false;
        this.error = error.response?.data?.message || error.message || 'Login failed';
        
        if (error.response?.data?.errors) {
          this.error = Object.values(error.response.data.errors).flat().join(', ');
        }
        
        return { success: false, error: this.error };
      }
    },
    
    async register(credentials) {
      this.loading = true;
      this.error = null;
      
      try {
        // Lấy CSRF cookie trước khi register
        await window.axios.get('/sanctum/csrf-cookie');
        
        // Thực hiện register
        const response = await api.post('/register', credentials);
        
        this.user = response.data.user;
        this.isAuthenticated = true;
        this.loading = false;
        
        return { success: true };
      } catch (error) {
        this.loading = false;
        this.error = error.response?.data?.message || error.message || 'Registration failed';
        
        if (error.response?.data?.errors) {
          this.error = Object.values(error.response.data.errors).flat().join(', ');
        }
        
        return { success: false, error: this.error };
      }
    },
    
    async logout() {
      this.loading = true;
      
      try {
        await api.post('/logout');
        this.user = null;
        this.isAuthenticated = false;
        this.loading = false;
        
        return { success: true };
      } catch (error) {
        // Vẫn logout local nếu API call fail
        this.user = null;
        this.isAuthenticated = false;
        this.loading = false;
        
        return { success: true };
      }
    },
    
    async fetchUser() {
      try {
        const response = await api.get('/user');
        this.user = response.data;
        this.isAuthenticated = true;
        return { success: true };
      } catch (error) {
        this.user = null;
        this.isAuthenticated = false;
        return { success: false };
      }
    },
    
    clearError() {
      this.error = null;
    }
  }
});

