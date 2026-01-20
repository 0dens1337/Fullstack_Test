<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

import { useApi } from '../composables/useApi';

const props = defineProps<{
    title?: string;
}>();

const isAuthenticated = ref(false);

const { setToken, request } = useApi();

const checkAuth = () => {
    const token = localStorage.getItem('auth_token');
    isAuthenticated.value = !!token;

    if (token) {
        setToken(token);
    }
};

const logout = async () => {
    try {
        const token = localStorage.getItem('auth_token');

        if (token) {
            await request({
                method: 'POST',
                url: '/api/v1/auth/logout',
            });
        }
    } catch (e) {
    } finally {
        setToken(null);
        isAuthenticated.value = false;
        window.location.href = '/';
    }
};

onMounted(checkAuth);
</script>

<template>
    <div class="min-h-screen bg-gray-100 text-gray-800">
        <header class="border-b bg-white">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
                <Link href="/" class="text-lg font-semibold text-indigo-600"> Catalog </Link>

                <nav class="flex items-center gap-4 text-sm">
                    <Link href="/" class="hover:underline"> Публичный каталог </Link>

                    <Link href="/admin/products" class="hover:underline"> Админка </Link>

                    <button v-if="isAuthenticated" type="button" class="text-red-600 hover:underline" @click="logout">Выйти</button>
                    <Link v-else href="/admin/login" class="hover:underline"> Вход </Link>
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-4 py-6">
            <h1 class="mb-4 text-2xl font-bold">
                {{ props.title ?? 'Catalog' }}
            </h1>
            <slot />
        </main>
    </div>
</template>
