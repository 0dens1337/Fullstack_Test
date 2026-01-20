<script setup lang="ts">
import { ref } from 'vue';

import { useApi } from '../../composables/useApi';
import MainLayout from '../../layouts/MainLayout.vue';

const email = ref('');
const password = ref('');
const message = ref<string | null>(null);

const { request, setToken, loading, error } = useApi();

const submit = async () => {
    message.value = null;

    try {
        const data: any = await request({
            method: 'POST',
            url: '/api/v1/auth/login',
            data: {
                email: email.value,
                password: password.value,
            },
        });

        const token = data.token ?? data.access_token ?? data.data?.token ?? null;

        if (!token) {
            message.value = 'Ошибка: токен не получен 😢';
            return;
        }

        setToken(token);

        window.location.href = '/admin/products';
    } catch (e) {
        message.value = error.value || 'Ошибка авторизации';
    }
};
</script>

<template>
    <MainLayout title="Вход в админку">
        <div class="mx-auto mt-10 max-w-md rounded-lg bg-white p-6 shadow-md">
            <h2 class="mb-6 text-center text-xl font-semibold">Авторизация</h2>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                    <input
                        v-model="email"
                        type="email"
                        required
                        class="w-full rounded border px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    />
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Пароль</label>
                    <input
                        v-model="password"
                        type="password"
                        required
                        class="w-full rounded border px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    />
                </div>

                <!-- ERROR -->
                <div v-if="message" class="text-sm text-red-600">
                    {{ message }}
                </div>

                <button
                    type="submit"
                    class="w-full rounded-lg bg-indigo-600 py-2 text-center font-medium text-white transition hover:bg-indigo-700 disabled:opacity-50"
                    :disabled="loading"
                >
                    <span v-if="loading">Входим...</span>
                    <span v-else>Войти</span>
                </button>
            </form>
        </div>
    </MainLayout>
</template>
