import { defineStore } from 'pinia';
import http from '../services/http';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        token: localStorage.getItem('access_token') || null,
        user: null as any,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
    },

    actions: {
        async login(email: string, senha: string) {
            try {
                const response = await http.post('/login', { email, senha });
                
                this.token = response.data.access_token;
                this.user = response.data.user;
                localStorage.setItem('access_token', this.token!);
                
                return true;
            } catch (error) {
                console.error('Erro no login:', error);
                throw error;
            }
        },

        async logout() {
            try {
                await http.post('/logout');
            } catch (error) {
                console.error('Erro ao fazer logout na API', error);
            } finally {
                this.token = null;
                this.user = null;
                localStorage.removeItem('access_token');
            }
        },

        async fetchUser() {
            if (this.token && !this.user) {
                try {
                    const response = await http.get('/me');
                    this.user = response.data;
                } catch (error) {
                    this.logout();
                }
            }
        }
    }
});