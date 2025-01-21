<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import AppLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import NewItemModal from '@/Components/NewItemModal.vue';
import EditBoxModal from '@/Components/EditBoxModal.vue';
import EditItemModal from '@/Components/EditItemModal.vue';
import PencilIcon from '@/Components/Icons/PencilIcon.vue';
import { api } from '@/utils/api';

interface Item {
    id: number;
    name: string;
    description: string | null;
    photo_path: string | null;
    box_id: number;
    created_at: string;
    updated_at: string;
}

interface Box {
    id: number;
    name: string;
    description: string | null;
    location: string;
    photo_path: string | null;
    created_at: string;
    updated_at: string;
    items: Item[];
}

interface BoxResponse {
    data: Box;
}

const props = defineProps<{
    id: string;
}>();

const box = ref<BoxResponse | null>(null);
const loading = ref(true);
const showEditBoxModal = ref(false);
const showNewItemModal = ref(false);
const showEditItemModal = ref(false);
const selectedItem = ref<Item | null>(null);

const pageTitle = computed(() => {
    return box.value ? `${box.value.data.name} - Box Details` : 'Loading...';
});

const getItemPhotoPath = (photoPath: string | null) => {
    if (!photoPath) return '/images/default-item.svg';
    return `/images/${photoPath}`;
};

const editItem = (item: Item) => {
    selectedItem.value = item;
    showEditItemModal.value = true;
};

const closeEditModal = () => {
    showEditItemModal.value = false;
    selectedItem.value = null;
};

onMounted(async () => {
    try {
        const response = await api.get(`/api/boxes/${props.id}`);
        box.value = await response.json();
    } catch (error) {
        console.error('Error fetching box details:', error);
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <AppLayout>
        <Head :title="pageTitle" />

        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ box?.data.name || 'Box Details' }}</h2>
                <div class="flex space-x-4">
                    <Link
                        :href="route('dashboard')"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        Back to Boxes
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="loading" class="text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900 mx-auto"></div>
                    <p class="mt-4 text-gray-600">Loading box details...</p>
                </div>

                <div v-else-if="box" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <!-- Box Details -->
                    <div class="p-6 border-b border-gray-200 relative">
                        <button
                            class="absolute top-4 right-4 p-2 rounded-full bg-white shadow-sm hover:bg-gray-50"
                            @click="showEditBoxModal = true"
                        >
                            <PencilIcon />
                        </button>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">{{ box.data.name }}</h3>
                                <p class="mt-1 text-xs text-gray-500">
                                    <span class="font-semibold">Last updated:</span>
                                    {{ new Date(box.data.updated_at).toLocaleDateString() }}
                                </p>
                                <p class="mt-4 text-gray-600 mb-2">
                                    <span class="font-semibold">Location:</span> {{ box.data.location }}
                                </p>
                                <p class="text-gray-600 mb-4">
                                    <span class="font-semibold">Description:</span>
                                    {{ box.data.description || 'No description provided' }}
                                </p>
                            </div>
                            <div v-if="box.data.photo_path" class="flex justify-center items-center">
                                <img :src="getItemPhotoPath(box.data.photo_path)" :alt="box.data.name" class="max-h-48 object-contain rounded-lg shadow-md">
                            </div>
                        </div>
                    </div>

                    <!-- Items List -->
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-xl font-semibold text-gray-900">Items in this Box</h4>
                            <button
                                @click="showNewItemModal = true"
                                class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                Add Item
                            </button>
                        </div>

                        <div v-if="box.data.items.length === 0" class="text-center py-8 text-gray-500">
                            No items found in this box.
                        </div>

                        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div v-for="item in box.data.items" :key="item.id" class="bg-gray-50 rounded-lg p-4 shadow-sm relative group">
                                <button
                                    class="absolute top-2 right-2 p-2 rounded-full bg-white shadow-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:bg-gray-50"
                                    @click.prevent="editItem(item)"
                                >
                                    <PencilIcon />
                                </button>
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <img :src="getItemPhotoPath(item.photo_path)" :alt="item.name" class="h-20 w-20 object-cover rounded">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-lg font-medium text-gray-900 truncate">{{ item.name }}</p>
                                        <p class="text-sm text-gray-500">{{ item.description || 'No description' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-center text-gray-600">Box not found.</p>
                </div>
            </div>
        </div>

        <NewItemModal
            v-if="box"
            :show="showNewItemModal"
            :box-id="Number(box.data.id)"
            @close="showNewItemModal = false"
        />

        <EditBoxModal
            v-if="box"
            :show="showEditBoxModal"
            :box="box.data"
            @close="showEditBoxModal = false"
        />

        <EditItemModal
            v-if="selectedItem"
            :show="showEditItemModal"
            :item="selectedItem"
            @close="closeEditModal"
        />
    </AppLayout>
</template>
