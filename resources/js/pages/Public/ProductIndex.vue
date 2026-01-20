<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';

import { useApi } from '@/composables/useApi';
import MainLayout from '@/layouts/MainLayout.vue';

const { request, loading, error } = useApi();

interface Category {
    id: number;
    name: string;
}

interface Product {
    id: number;
    name: string;
    description?: string | null;
    price: number | string;
    category?: Category;
    category_name?: string;
    category_id?: number;
}

const products = ref<Product[]>([]);
const categories = ref<Category[]>([]);
const selectedCategory = ref<string>('');
const currentPage = ref(1);
const lastPage = ref(1);
const total = ref(0);

const fetchCategories = async () => {
    const data: any = await request({
        method: 'GET',
        url: '/api/v1/categories',
    });
    categories.value = data.data ?? data;
};

const fetchProducts = async (page = 1) => {
    const params: any = { page };
    if (selectedCategory.value) {
        params.category_id = selectedCategory.value;
    }

    const data: any = await request({
        method: 'GET',
        url: '/api/v1/products',
        params,
    });

    products.value = data.data || data;
    if (data.meta) {
        currentPage.value = data.meta.current_page;
        lastPage.value = data.meta.last_page;
        total.value = data.meta.total;
    }
};

const onChangeCategory = async () => {
    await fetchProducts(1);
};

const pages = computed(() => {
    const arr: number[] = [];
    for (let i = 1; i <= lastPage.value; i++) arr.push(i);
    return arr;
});

onMounted(async () => {
    await Promise.all([fetchCategories(), fetchProducts()]);
});
</script>

<template>
    <MainLayout title="Каталог товаров">
        <div class="mb-4 flex flex-col gap-4 rounded-lg bg-white p-4 shadow md:flex-row md:items-center md:justify-between">
            <div class="flex flex-col">
                <span class="text-sm text-gray-500">Всего товаров:</span>
                <span class="text-lg font-semibold">{{ total }}</span>
            </div>

            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-600">Фильтр по категории:</label>
                <select
                    v-model="selectedCategory"
                    @change="onChangeCategory"
                    class="rounded border border-gray-300 px-3 py-1 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                >
                    <option value="">Все категории</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                        {{ cat.name }}
                    </option>
                </select>
            </div>
        </div>

        <div v-if="error" class="mb-4 text-sm text-red-600">
            {{ error }}
        </div>

        <div v-if="loading" class="py-8 text-center text-gray-500">Загрузка...</div>

        <div v-else class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            <div v-for="product in products" :key="product.id" class="flex flex-col justify-between rounded-lg bg-white p-4 shadow">
                <div>
                    <h2 class="mb-1 text-lg font-semibold">
                        {{ product.name }}
                    </h2>
                    <p class="mb-2 text-xs text-gray-500">
                        Категория:
                        <span class="font-medium">
                            {{ product.category?.name || product.category_name || '—' }}
                        </span>
                    </p>
                    <p class="mb-3 text-sm text-gray-700">
                        {{ product.description || 'Без описания' }}
                    </p>
                </div>

                <div class="mt-2 flex items-center justify-between">
                    <span class="font-semibold text-indigo-600"> {{ Number(product.price).toFixed(2) }} ₽ </span>
                    <a :href="`/product/${product.id}`" class="text-sm text-indigo-600 hover:underline"> Подробнее → </a>
                </div>
            </div>
        </div>

        <div v-if="lastPage > 1" class="mt-6 flex flex-wrap items-center gap-2">
            <button
                type="button"
                class="rounded border border-gray-300 bg-white px-3 py-1 text-sm disabled:opacity-50"
                :disabled="currentPage === 1 || loading"
                @click="fetchProducts(currentPage - 1)"
            >
                Назад
            </button>

            <button
                v-for="page in pages"
                :key="page"
                type="button"
                class="rounded border bg-white px-3 py-1 text-sm"
                :class="page === currentPage ? 'border-indigo-500 font-semibold text-indigo-600' : 'border-gray-300'"
                :disabled="loading"
                @click="fetchProducts(page)"
            >
                {{ page }}
            </button>

            <button
                type="button"
                class="rounded border border-gray-300 bg-white px-3 py-1 text-sm disabled:opacity-50"
                :disabled="currentPage === lastPage || loading"
                @click="fetchProducts(currentPage + 1)"
            >
                Вперёд
            </button>
        </div>
    </MainLayout>
</template>
