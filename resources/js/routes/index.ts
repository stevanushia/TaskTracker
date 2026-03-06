import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';
import MainLayout from '@/components/MainLayout.vue';

const routes: Array<RouteRecordRaw> = [
    {
        path: '/login',
        name: 'login',
        component: () => import('@/views/auth/LoginView.vue'),
        meta: { requiresGuest: true }
    },
    {
        path: '/',
        component: MainLayout,
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                name: 'dashboard',
                component: () => import('@/views/DashboardView.vue')
            }
        ]
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Navigation Guard
router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();
    const isAuthenticated = authStore.isAuthenticated;

    if (to.meta.requiresAuth && !isAuthenticated) {
        // Not logged in but trying to access a secure page? Go to login.
        next({ name: 'login' });
    } else if (to.meta.requiresGuest && isAuthenticated) {
        // Logged in but trying to access the login page? Go to dashboard.
        next({ name: 'dashboard' });
    } else {
        // Otherwise, proceed normally.
        next();
    }
});

export default router;
