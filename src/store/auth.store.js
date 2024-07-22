import { defineStore } from 'pinia';
import axios from "axios";
import router from '@router';

const loginUrl = `${import.meta.env.VITE_API_URL}/admin/login/check.php`;

export const useAuthStore = defineStore({
    id: 'auth',
    state: () => ({
        user: JSON.parse(localStorage.getItem('user')),
        returnUrl: null
    }),
    actions: {
        async login(login, password) {
            const form = new FormData();
            form.append('login', login);
            form.append('password', password);

            const result = await axios.post(loginUrl, form);

            //console.log(result);

            if(!result.data.params || result.data.error === 1){
                return result.data.error_text || 'Неизвестная ошибка, попробуйте позже';
            }

            const user = result.data.params;

            this.user = user;

            localStorage.setItem('user', JSON.stringify(user));

            axios.defaults.headers.post['Authorization'] = `${user.token}&${user.id}`;

            await router.push(this.returnUrl || {name: 'Admin'});
        },
        async logout() {
            this.user = null;
            localStorage.removeItem('user');
            axios.defaults.headers.post['Authorization'] = '';

            await router.push('/');
        }
    }
});