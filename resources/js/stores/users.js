import { defineStore } from 'pinia';
import api from '../services/api';

export const useUsersStore = defineStore('users', {
  state: () => ({
    users: [],
    user: null,
    pagination: {
      currentPage: 1,
      totalPages: 1,
      total: 0,
      perPage: 15,
    },
    loading: false,
    error: null,
  }),

  actions: {
    async fetchUsers(page = 1, filters = {}) {
      this.loading = true;
      this.error = null;

      try {
        const params = { page, ...filters };
        const response = await api.get('/users', { params });

        // ✅ Có paginate
        if (response.data.meta) {
          this.users = response.data.data;
          this.pagination = {
            currentPage: response.data.meta.current_page,
            totalPages: response.data.meta.last_page,
            total: response.data.meta.total,
            perPage: response.data.meta.per_page,
          };
        } 
        // ✅ Không paginate (per_page = -1)
        else {
          this.users = response.data.data ?? response.data;
          this.pagination = {
            currentPage: 1,
            totalPages: 1,
            total: this.users.length,
            perPage: this.users.length,
          };
        }
      } catch (err) {
        this.error = 'Failed to fetch users.';
        console.error(err);
      } finally {
        this.loading = false;
      }
    },

    async createUser(userData) {
      this.loading = true;
      this.error = null;

      try {
        const response = await api.post('/users', userData);

        // refresh list
        await this.fetchUsers(this.pagination.currentPage);

        // ✅ backend trả { data: user }
        return response.data.data;
      } catch (err) {
        this.error =
          err.response?.data?.message ||
          Object.values(err.response?.data?.errors || {}).flat().join(' ') ||
          'Failed to create user.';

        console.error(err);
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async updateUser(id, userData) {
      this.loading = true;
      this.error = null;

      try {
        const response = await api.put(`/users/${id}`, userData);

        await this.fetchUsers(this.pagination.currentPage);

        return response.data.data;
      } catch (err) {
        this.error =
          err.response?.data?.message ||
          Object.values(err.response?.data?.errors || {}).flat().join(' ') ||
          'Failed to update user.';

        console.error(err);
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async deleteUser(id) {
      this.loading = true;
      this.error = null;

      try {
        await api.delete(`/users/${id}`);

        await this.fetchUsers(this.pagination.currentPage);
      } catch (err) {
        this.error = 'Failed to delete user.';
        console.error(err);
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async updateMe(data) {
      this.loading = true
      this.error = null

      try {
        const res = await api.put('/users/me', data)

        // cập nhật user đang xem
        this.user = res.data.data

        return this.user
      } catch (err) {
        this.error =
          err.response?.data?.message ||
          Object.values(err.response?.data?.errors || {}).flat().join(' ') ||
          'Failed to update profile.'

        throw err
      } finally {
        this.loading = false
      }
    },
  },
});
