<template>
  <div v-if="projectsStore.loading" class="text-center py-10">Loading project details...</div>
  <div v-else-if="projectsStore.error" class="text-center py-10 text-red-500">{{ projectsStore.error }}</div>
  <div v-else-if="project" class="space-y-6">
    
    <!-- Tab Navigation -->
    <div class="border-b border-gray-200">
      <nav class="-mb-px flex space-x-8" aria-label="Tabs">
        <button 
          v-for="tab in tabs" 
          :key="tab.name" 
          @click="currentTab = tab.name"
          :class="[
            currentTab === tab.name 
              ? 'border-blue-500 text-blue-600' 
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
            'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
          ]"
        >
          {{ tab.name }}
        </button>
      </nav>
    </div>

    <!-- Tab Content -->
    <div>
      <div v-if="currentTab === 'Tickets' && project">
        <ProjectTickets :tickets="project.tickets" :project="project" @ticket-created="refreshProjectData" />
      </div>

      <div v-if="currentTab === 'Members' && project">
        <ProjectMembers :project="project" />
      </div>

      <div v-if="currentTab === 'Kanban' && project?.type === 'kanban'">
        <ProjectKanbanTab :project="project" />
      </div>

      <div v-if="currentTab === 'Sprints' && project?.type === 'scrum'">
        <ProjectSprintsTab :project="project" />
      </div>

      <div v-if="currentTab === 'Roadmap' && project">
        <GanttChart :project-id="project.id.toString()" />
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useProjectsStore } from '@/stores/projects';
import ProjectMembers from '../components/Projects/ProjectMembers.vue';
import ProjectTickets from '../components/Projects/ProjectTickets.vue';
import ProjectSprintsTab from '../components/Projects/ProjectSprintsTab.vue';
import ProjectKanbanTab from '../components/Projects/ProjectKanbanTab.vue';
import GanttChart from '../components/Roadmap/GanttChart.vue';

const route = useRoute();
const projectsStore = useProjectsStore();

const project = computed(() => projectsStore.project);
const projectId = route.params.id;

const baseTabs = [
  { name: 'Tickets' },
  { name: 'Members' },
];

const projectSpecificTabs = computed(() => {
  if (!project.value) return [];
  if (project.value.type === 'scrum') {
    return [
      { name: 'Sprints' },
      { name: 'Roadmap' },
    ];
  }
  if (project.value.type === 'kanban') {
    return [
      { name: 'Kanban' },
      { name: 'Roadmap' },
    ];
  }
  return [];
});

const tabs = computed(() => [...baseTabs, ...projectSpecificTabs.value]);

const currentTab = ref('Tickets'); // Default tab

// Watch for project changes to reset the tab if it's no longer valid
watch(project, (newProject, oldProject) => {
    if (newProject && oldProject && newProject.id === oldProject.id) {
        // If it's the same project, do nothing, let the user control the tab.
        return;
    }
    // If the project changes, or on initial load, reset to the first tab.
    currentTab.value = 'Tickets';
});

const refreshProjectData = () => {
  projectsStore.fetchProject(projectId);
};

onMounted(() => {
  refreshProjectData();
});
</script>
