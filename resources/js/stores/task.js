import { defineStore } from 'pinia';
import api from '../services/api';

export const useTaskStore = defineStore('task', {
  state: () => ({
    tasks: [],
    loading: false,
    error: null,
  }),

  actions: {
    async fetchTasks(params = {}) {
      this.loading = true;
      this.error = null;
      try {
        const response = await api.get('/tickets', { params });
        // Assuming the API returns data in a { data: [...] } structure
        this.tasks = response.data.data; 
      } catch (err) {
        this.error = 'Failed to fetch tasks.';
        console.error(err);
      } finally {
        this.loading = false;
      }
    },
  },
});
