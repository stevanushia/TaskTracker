import { defineStore } from 'pinia';
import api from '@/services/api';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null as any | null,
        token: localStorage.getItem('access_token') || null,
    }),
    getters: {
        isAuthenticated: (state) => !!state.token,
    },
    actions: {
        async login(credentials: any) {
            const response = await api.post('/login', credentials);
            this.token = response.data.access_token;
            this.user = response.data.user;
            localStorage.setItem('access_token', this.token!);
        },
        async logout() {
            await api.post('/logout');
            this.token = null;
            this.user = null;
            localStorage.removeItem('access_token');
        }
    }
});
