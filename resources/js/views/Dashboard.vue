<template>
  <div>
    <!-- Loading / Error States -->
    <div v-if="dashboardStore.loading" class="text-center py-10">Loading...</div>
    <div v-else-if="dashboardStore.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
      Error: {{ dashboardStore.error }}
    </div>

    <!-- Dashboard Content -->
    <div v-else>
      <!-- Stat Cards -->
      <div class="flex justify-center mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <StatCard title="Active Projects" :value="dashboardStore.stats?.active_projects" icon="collection" to="/projects/active"/>
          <StatCard title="Open Tickets" :value="dashboardStore.stats?.open_tickets" icon="ticket" to="/tickets/open"/>
          <StatCard title="Tickets In Progress" :value="dashboardStore.stats?.in_progress_tickets" icon="fire" to="/tickets/in-progress"/>
        </div>
      </div>

      <!-- Main 2-Column Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Column (2/3) -->
        <div class="lg:col-span-2 space-y-6">
          <!-- My Tasks Today -->
          <MyTasksToday :tasks="dashboardStore.myTasksToday" />
          <ActivityTimeline :activities="dashboardStore.latestActivities" />
        </div>

        <!-- Side Column (1/3) -->
        <div class="lg:col-span-1 space-y-6">
          <StatusPieChart :chart-data="dashboardStore.ticketsByPriority" title="Tickets by Priority" />
          <StatusPieChart :chart-data="dashboardStore.ticketsByType" title="Tickets by Type" />
          <WorkloadList :workload-data="dashboardStore.userTimeLogged" />
        </div>
      </div>
    </div>
  </div>
</template>


<script setup>
import { onMounted } from 'vue';
import { useDashboardStore } from '@/stores/dashboard';
import StatCard from '../components/Dashboard/StatCard.vue';
import StatusPieChart from '../components/Dashboard/StatusPieChart.vue';
import WorkloadList from '../components/Dashboard/WorkloadList.vue';
import ActivityTimeline from '../components/Dashboard/ActivityTimeline.vue';
import OpenTickets from '@/views/OpenTickets.vue'

const dashboardStore = useDashboardStore();
import MyTasksToday from '../components/Dashboard/MyTasksToday.vue';

onMounted(() => {
  dashboardStore.fetchAllDashboardData();
});
</script>
