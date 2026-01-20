import axios from 'axios';
import { ref } from 'vue';

const loading = ref(false);
const error = ref<string | null>(null);

const api = axios.create({
    headers: {
        Accept: 'application/json',
    },
});

export function useApi() {
    const setToken = (token: string | null) => {
        if (token) {
            localStorage.setItem('auth_token', token);
            api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        } else {
            localStorage.removeItem('auth_token');
            delete api.defaults.headers.common['Authorization'];
        }
    };

    const request = async (config: any) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.request(config);
            return response.data;
        } catch (e: any) {
            error.value = e.response?.data?.message || 'Ошибка запроса';
            throw e;
        } finally {
            loading.value = false;
        }
    };

    return {
        request,
        loading,
        error,
        setToken,
    };
}
