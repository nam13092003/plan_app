
import { defineStore } from 'pinia';
import api from '../services/api';

export const useReferentialStore = defineStore('referentials', {
  state: () => ({
    projects: [], // For filter dropdowns
    ticketStatuses: [],
    ticketTypes: [],
    ticketPriorities: [],
    users: [],
    roles: [], // Added roles
    loading: false,
    isLoaded: false, // Flag to check if data has been fetched
  }),

  actions: {
    async fetchAllReferentials() {
      if (this.isLoaded) return; // Don't refetch if already loaded

      this.loading = true;
      try {
        const [
          projectsRes,
          statusesRes,
          typesRes,
          prioritiesRes,
          usersRes,
          rolesRes,
        ] = await Promise.all([
          api.get('/projects', { params: { per_page: -1 } }),
          api.get('/referential/ticket-statuses'),
          api.get('/referential/ticket-types'),
          api.get('/referential/ticket-priorities'),
          api.get('/users', { params: { per_page: -1 } }),
          api.get('/roles'),
        ]);

        // Hyper-defensive data handling
        const projectsData = projectsRes.data?.data || projectsRes.data || [];
        const statusesData = statusesRes.data?.data || statusesRes.data || [];
        const typesData = typesRes.data?.data || typesRes.data || [];
        const prioritiesData = prioritiesRes.data?.data || prioritiesRes.data || [];
        const usersData = usersRes.data?.data || usersRes.data || [];
        const rolesData = rolesRes.data?.data || rolesRes.data || [];

        this.projects = Array.isArray(projectsData) ? projectsData : [];
        this.ticketStatuses = Array.isArray(statusesData) ? statusesData : [];
        this.users = Array.isArray(usersData) ? usersData : [];
        this.roles = Array.isArray(rolesData) ? rolesData : [];

        // Sort Type and Priority alphabetically, ensuring they are arrays
        this.ticketTypes = (Array.isArray(typesData) ? typesData : []).sort((a, b) => a.name.localeCompare(b.name));
        this.ticketPriorities = (Array.isArray(prioritiesData) ? prioritiesData : []).sort((a, b) => a.name.localeCompare(b.name));

        this.isLoaded = true;

      } catch (error) {
        console.error('Failed to fetch referential data:', error);
        // Even on failure, ensure state properties are valid arrays to prevent render errors
        this.projects = [];
        this.ticketStatuses = [];
        this.ticketTypes = [];
        this.ticketPriorities = [];
        this.users = [];
        this.roles = [];
      } finally {
        this.loading = false;
      }
    },
  },
});
