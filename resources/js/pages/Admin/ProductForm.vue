<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';

import { useApi } from '../../composables/useApi';
import MainLayout from '../../layouts/MainLayout.vue';

interface Category {
    id: number;
    name: string;
}

const props = defineProps<{
    mode: 'create' | 'edit';
    productId?: number;
}>();

const isEdit = computed(() => props.mode === 'edit');

const { request, loading, error } = useApi();

const name = ref('');
const description = ref('');
const price = ref<number | null>(null);
const categoryId = ref<number | null>(null);

const categories = ref<Category[]>([]);
const formError = ref<string | null>(null);

const title = computed(() =>
    isEdit.value ? 'Редактирование товара' : 'Создание товара'
);

const fetchCategories = async () => {
    const data: any = await request({
        method: 'GET',
        url: '/api/v1/categories',
    });

    categories.value = data.data ?? data;
};

const fetchProduct = async () => {
    if (!isEdit.value || !props.productId) return;

    const data: any = await request({
        method: 'GET',
        url: `/api/v1/products/${props.productId}`,
    });

    const p = data.data || data;

    name.value = p.name ?? '';
    description.value = p.description ?? '';
    price.value = Number(p.price ?? 0);
    categoryId.value = p.category_id ?? p.category?.id ?? null;
};

const validate = (): boolean => {
    formError.value = null;

    if (!name.value.trim()) {
        formError.value = 'Название обязательно';
        return false;
    }

    if (!categoryId.value) {
        formError.value = 'Выберите категорию';
        return false;
    }

    if (price.value === null || isNaN(price.value) || price.value <= 0) {
        formError.value = 'Цена должна быть больше 0';
        return false;
    }

    return true;
};

const submit = async () => {
    if (!validate()) return;

    try {
        formError.value = null;

        const payload = {
            name: name.value,
            description: description.value,
            price: price.value,
            category_id: categoryId.value,
        };

        if (isEdit.value && props.productId) {
            await request({
                method: 'PATCH',
                url: `/api/v1/products/${props.productId}`,
                data: payload,
            });
        } else {
            await request({
                method: 'POST',
                url: '/api/v1/products',
                data: payload,
            });
        }

        window.location.href = '/admin/products';
    } catch (e) {
        if (!formError.value) {
            formError.value = error.value || 'Ошибка сохранения товара';
        }
    }
};

onMounted(async () => {
    await fetchCategories();
    await fetchProduct();
});
</script>

<template>
    <MainLayout :title="title">
        <div class="max-w-xl mx-auto bg-white rounded-lg shadow p-6">
            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Название
                    </label>
                    <input
                        v-model="name"
                        type="text"
                        required
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Категория
                    </label>
                    <select
                        v-model="categoryId"
                        required
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                        <option :value="null" disabled>Выберите категорию</option>
                        <option
                            v-for="cat in categories"
                            :key="cat.id"
                            :value="cat.id"
                        >
                            {{ cat.name }}
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Описание
                    </label>
                    <textarea
                        v-model="description"
                        rows="4"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Цена
                    </label>
                    <input
                        v-model.number="price"
                        type="number"
                        min="0"
                        step="0.01"
                        required
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    />
                </div>

                <!-- Ошибки -->
                <div v-if="formError" class="text-sm text-red-600">
                    {{ formError }}
                </div>
                <div v-if="error && !formError" class="text-sm text-red-600">
                    {{ error }}
                </div>

                <div class="flex items-center justify-between pt-2">
                    <a
                        href="/admin/products"
                        class="text-sm text-gray-600 hover:text-gray-800"
                    >
                        ← Назад к списку
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded disabled:opacity-50"
                        :disabled="loading"
                    >
            <span v-if="loading">
              Сохраняем...
            </span>
                        <span v-else>
              {{ isEdit ? 'Сохранить изменения' : 'Создать товар' }}
            </span>
                    </button>
                </div>
            </form>
        </div>
    </MainLayout>
</template>
