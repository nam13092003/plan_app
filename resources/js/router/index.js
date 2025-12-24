import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores';
import Layout from '../components/Layout.vue'; // Import the layout

const routes = [
  {
    path: '/',
    component: () => import('@/views/Landing.vue')
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('../views/Login.vue'),
    meta: { requiresGuest: true }
  },
  {
    path: '/',
    component: Layout, // All authenticated routes will use this layout
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'Home',
        redirect: { name: 'Dashboard' }
      },
      {
        path: 'dashboard',
        name: 'Dashboard',
        component: () => import('../views/Dashboard.vue'),
        meta: { title: 'Dashboard' },
      },
      {
        path: 'projects/active',
        name: 'ActiveProjects',
        component: () => import('../views/ActiveProjects.vue'),
        meta: { title: 'Active Projects' },
      },
      {
        path: 'tickets/open',
        name: 'tickets.open',
        component: () => import('../views/OpenTickets.vue'),
        meta: { title: 'Open Tickets' },
      },
      {
        path: 'tickets/in-progress',
        name: 'tickets.in-progress',
        component: () => import('../views/TicketsInProgress.vue'),
        meta: { title: 'Tickets In Progress' },
      },
      {
        path: 'projects',
        name: 'Projects',
        component: () => import('../views/Projects.vue'),
        meta: { title: 'Projects' },
      },
      {
        path: 'projects/:id',
        name: 'ProjectDetail',
        component: () => import('../views/ProjectDetail.vue'),
        props: true,
        meta: { title: 'Project Details' },
      },
      {
        path: 'projects/:id/roadmap',
        name: 'Roadmap',
        component: () => import('../views/Roadmap.vue'),
        props: true,
        meta: { title: 'Project Roadmap' },
      },
      {
        path: 'tickets',
        name: 'Tickets',
        component: () => import('../views/Tickets.vue'),
        meta: { title: 'Tickets' },
      },
      {
        path: 'tickets/:id',
        name: 'TicketDetail',
        component: () => import('../views/TicketDetail.vue'),
        props: true,
        meta: { title: 'Ticket Details' },
      },
      {
        path: 'users',
        name: 'Users',
        component: () => import('../views/Users.vue'),
        meta: { requiresAdmin: true, title: 'User Management' },
      },
      {
        path: 'my-tasks',
        name: 'MyTasks',
        component: () => import('../views/MyTasks.vue'),
        meta: { title: 'My Tasks' },
      },
      {
        path: 'profile',
        name: 'UserProfile',
        component: () => import('../views/UserProfile.vue'),
        meta: { title: 'User Profile' },
      },
      // Add other authenticated routes here
    ]
  },
  // Redirect any unmatched routes to the dashboard if logged in, or login if not
  { 
    path: '/:pathMatch(.*)*', 
    redirect: to => {
      const authStore = useAuthStore();
      return authStore.isAuthenticated ? { name: 'Dashboard' } : { name: 'Login' };
    }
  }
];

const router = createRouter({
  history: createWebHistory('/app'), // Base path is /app
  routes
});

// Navigation guards
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();

  // Try to fetch user if not authenticated to check for existing session
  if (!authStore.isAuthenticated && (to.meta.requiresAuth || to.matched.some(record => record.meta.requiresAuth))) {
    await authStore.fetchUser();
  }

  const isAuthenticated = authStore.isAuthenticated;

  if (to.meta.requiresAuth && !isAuthenticated) {
    // If route requires auth and user is not logged in, redirect to login
    next({ name: 'Login', query: { redirect: to.fullPath } });
  } else if (to.meta.requiresGuest && isAuthenticated) {
    // If route is for guests (like login) and user is logged in, redirect to dashboard
    next({ name: 'Dashboard' });
  } else if (to.meta.requiresAdmin && !authStore.user?.is_admin) {
    // If route requires admin and user is not an admin, redirect to dashboard
    next({ name: 'Dashboard' });
  } else {
    // Otherwise, allow navigation
    next();
  }
});

export default router;

