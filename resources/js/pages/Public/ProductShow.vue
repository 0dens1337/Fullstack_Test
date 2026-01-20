<script setup lang="ts">
import { onMounted, ref } from 'vue';

import { useApi } from '@/composables/useApi';
import MainLayout from '@/layouts/MainLayout.vue';

const props = defineProps<{
    productId: number;
}>();

const { request, loading } = useApi();
const product = ref<any | null>(null);
const notFound = ref(false);

const fetchProduct = async () => {
    try {
        const data: any = await request({
            method: 'GET',
            url: `/api/v1/products/${props.productId}`,
        });
        product.value = data.data || data;
    } catch (e: any) {
        if (e.response?.status === 404) {
            notFound.value = true;
        }
    }
};

onMounted(fetchProduct);
</script>

<template>
    <MainLayout :title="product?.name || 'Товар'">
        <div v-if="loading" class="py-8 text-center text-gray-500">Загрузка...</div>

        <div v-else-if="notFound" class="py-8 text-center text-red-600">Товар не найден</div>

        <div v-else-if="product" class="rounded-lg bg-white p-6 shadow">
            <h2 class="mb-2 text-2xl font-bold">
                {{ product.name }}
            </h2>
            <p class="mb-4 text-sm text-gray-500">
                Категория:
                <span class="font-medium">
                    {{ product.category?.name || product.category_name || '—' }}
                </span>
            </p>
            <p class="mb-4 whitespace-pre-line text-gray-700">
                {{ product.description || 'Описание отсутствует' }}
            </p>
            <p class="text-xl font-semibold text-indigo-600">{{ Number(product.price).toFixed(2) }} ₽</p>

            <div class="mt-6">
                <a href="/" class="inline-flex items-center text-sm text-indigo-600 hover:underline"> ← Назад к списку </a>
            </div>
        </div>
    </MainLayout>
</template>
