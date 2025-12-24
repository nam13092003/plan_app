import { defineStore } from 'pinia';
import api from '../services/api';

export const useKanbanStore = defineStore('kanban', {
  state: () => ({
    board: {
      columns: [],
    },
    loading: false,
    error: null,
  }),

  getters: {
    // Example getter
    getColumns: (state) => state.board.columns,
  },

  actions: {
    async fetchBoard(projectId, projectType = 'kanban') {
      this.loading = true;
      this.error = null;
      try {
        // Xác định endpoint dựa trên loại dự án
        const ticketEndpoint = projectType === 'scrum' ? 'scrum' : 'kanban';
        
        const [statusesRes, ticketsRes] = await Promise.all([
          api.get(`/projects/${projectId}/kanban/statuses`),
          api.get(`/projects/${projectId}/${ticketEndpoint}/tickets`), // Gọi linh hoạt scrum/tickets hoặc kanban/tickets
        ]);

        const statuses = statusesRes.data;  
        const rawTickets = ticketsRes.data;
        const tickets = Array.isArray(rawTickets) ? rawTickets : (rawTickets.tickets || []);

        this.board.columns = statuses.map(status => ({
          id: status.id,
          title: status.title,
          color: status.color,
          // Dùng status.id để lọc vì Backend trả về status id
          tasks: tickets.filter(ticket => ticket.status && ticket.status.id === status.id)
        }));

      } catch (err) {
        this.error = 'Cannot load board data.';
        console.error(err);
      } finally {
        this.loading = false;
      }
    },

    async moveTask(taskId, newColumnId) {
      try {
        await api.put(`/tickets/${taskId}/move`, {
          status_id: newColumnId,
        });
      } catch (err) {
        this.error = 'Failed to move task.';
        console.error(err);
        // Re-throw the error so the component can handle it (e.g., revert UI)
        throw err;
      }
    },

    async createTask(taskData) {
      // taskData should include { name, project_id, status_id }
      try {
        await api.post('/tickets', taskData);
        // After creating, refresh the entire board to ensure consistency
        await this.fetchBoard(taskData.project_id);
      } catch (err) {
        this.error = 'Failed to create task.';
        console.error(err);
        throw err;
      }
    }
  },
});
