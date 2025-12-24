import { defineStore } from 'pinia'
import api from '../services/api'

export const useDashboardStore = defineStore('dashboard', {
  state: () => ({
    stats: null,
    favoriteProjects: [],
    latestActivities: [],
    latestComments: [],
    latestProjects: [],
    latestTickets: [],
    ticketsByPriority: null,
    ticketsByType: null,
    ticketTimeLogged: null,
    userTimeLogged: null,
    myTasksToday: [],
    loading: false,
    error: null,
  }),

  actions: {
    async fetchAllDashboardData() {
      this.loading = true
      this.error = null

      const requests = [
        api.get('/dashboard/stats'),
        api.get('/dashboard/favorite-projects'),
        api.get('/dashboard/latest-activities'),
        api.get('/dashboard/latest-comments'),
        api.get('/dashboard/latest-projects'),
        api.get('/dashboard/latest-tickets'),
        api.get('/dashboard/tickets-by-priority'),
        api.get('/dashboard/tickets-by-type'),
        api.get('/dashboard/ticket-time-logged'),
        api.get('/dashboard/user-time-logged'),
        api.get('/dashboard/my-tasks-today'),
      ]

      const results = await Promise.allSettled(requests)

      const [
        stats,
        favProjects,
        latestActivities,
        latestComments,
        latestProjects,
        latestTickets,
        ticketsByPriority,
        ticketsByType,
        ticketTimeLogged,
        userTimeLogged,
        myTasksToday,
      ] = results

      // Helper
      const getData = (res, fallback) =>
        res.status === 'fulfilled' ? res.value.data : fallback

      this.stats = getData(stats, null)
      this.favoriteProjects = getData(favProjects, [])
      this.latestActivities = getData(latestActivities, [])
      this.latestComments = getData(latestComments, [])
      this.latestProjects = getData(latestProjects, [])
      this.latestTickets = getData(latestTickets, [])
      this.ticketsByPriority = getData(ticketsByPriority, null)
      this.ticketsByType = getData(ticketsByType, null)
      this.ticketTimeLogged = getData(ticketTimeLogged, null)
      this.userTimeLogged = getData(userTimeLogged, null)
      this.myTasksToday = getData(myTasksToday, [])

      // Log lỗi chi tiết để debug
      results.forEach((r, i) => {
        if (r.status === 'rejected') {
          console.error(`Dashboard API ${i} failed`, r.reason)
        }
      })

      this.loading = false
    },
  },
})
