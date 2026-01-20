<script setup lang="ts">
import { onMounted, ref } from 'vue';

import MainLayout from '../../layouts/MainLayout.vue';
import { useApi } from '../../composables/useApi';

interface Product {
    id: number;
    name: string;
    description?: string | null;
    price: number | string;
    category_id?: number;
    category?: { id: number; name: string } | null;
}

const { request, loading, error } = useApi();

const products = ref<Product[]>([]);
const currentPage = ref(1);
const lastPage = ref(1);
const total = ref(0);

const fetchProducts = async (page = 1) => {
    const data: any = await request({
        method: 'GET',
        url: '/api/v1/products',
        params: { page },
    });

    products.value = data.data || data;
    if (data.meta) {
        currentPage.value = data.meta.current_page;
        lastPage.value = data.meta.last_page;
        total.value = data.meta.total;
    }
};

const deleteProduct = async (id: number) => {
    if (!confirm('Удалить товар?')) return;

    await request({
        method: 'DELETE',
        url: `/api/v1/products/${id}`,
    });

    await fetchProducts(currentPage.value);
};

onMounted(() => {
    fetchProducts();
});
</script>

<template>
    <MainLayout title="Управление товарами">
        <div class="flex justify-between items-center mb-4">
            <div>
                <p class="text-sm text-gray-500">Всего товаров:</p>
                <p class="text-lg font-semibold">{{ total }}</p>
            </div>

            <a
                href="/admin/products/create"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded"
            >
                Добавить товар
            </a>
        </div>

        <div v-if="error" class="mb-4 text-sm text-red-600">
            {{ error }}
        </div>

        <div v-if="loading" class="py-8 text-center text-gray-500">
            Загрузка...
        </div>

        <div v-else>
            <table class="min-w-full bg-white rounded-lg shadow overflow-hidden">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                        ID
                    </th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                        Название
                    </th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                        Категория
                    </th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                        Цена
                    </th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">
                        Действия
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr
                    v-for="product in products"
                    :key="product.id"
                    class="border-t"
                >
                    <td class="px-4 py-2 text-sm text-gray-700">
                        {{ product.id }}
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-700">
                        {{ product.name }}
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-700">
                        {{ product.category?.name || product.category_id || '—' }}
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-700">
                        {{ Number(product.price).toFixed(2) }} ₽
                    </td>
                    <td class="px-4 py-2 text-sm text-right space-x-2">
                        <a
                            :href="`/admin/products/${product.id}/edit`"
                            class="inline-flex items-center px-2 py-1 text-xs bg-yellow-500 hover:bg-yellow-600 text-white rounded"
                        >
                            Редактировать
                        </a>
                        <button
                            type="button"
                            class="inline-flex items-center px-2 py-1 text-xs bg-red-600 hover:bg-red-700 text-white rounded"
                            @click="deleteProduct(product.id)"
                        >
                            Удалить
                        </button>
                    </td>
                </tr>

                <tr v-if="products.length === 0">
                    <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">
                        Товаров пока нет
                    </td>
                </tr>
                </tbody>
            </table>

            <!-- Пагинация, если нужна -->
            <div v-if="lastPage > 1" class="mt-4 flex gap-2">
                <button
                    type="button"
                    class="px-3 py-1 text-sm border rounded bg-white disabled:opacity-50"
                    :disabled="currentPage === 1 || loading"
                    @click="fetchProducts(currentPage - 1)"
                >
                    Назад
                </button>

                <button
                    v-for="page in lastPage"
                    :key="page"
                    type="button"
                    class="px-3 py-1 text-sm border rounded bg-white"
                    :class="page === currentPage ? 'border-indigo-500 text-indigo-600 font-semibold' : 'border-gray-300'"
                    :disabled="loading"
                    @click="fetchProducts(page)"
                >
                    {{ page }}
                </button>

                <button
                    type="button"
                    class="px-3 py-1 text-sm border rounded bg-white disabled:opacity-50"
                    :disabled="currentPage === lastPage || loading"
                    @click="fetchProducts(currentPage + 1)"
                >
                    Вперёд
                </button>
            </div>
        </div>
    </MainLayout>
</template>
