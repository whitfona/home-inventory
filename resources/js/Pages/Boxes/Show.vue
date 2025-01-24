<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import AppLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import NewItemModal from '@/Components/NewItemModal.vue';
import EditBoxModal from '@/Components/EditBoxModal.vue';
import EditItemModal from '@/Components/EditItemModal.vue';
import PencilIcon from '@/Components/Icons/PencilIcon.vue';
import TrashIcon from '@/Components/Icons/TrashIcon.vue';
import { api } from '@/utils/api';
import DeleteItemModal from '@/Components/DeleteItemModal.vue';
import type { Box, Item } from '@/types';

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
const showDeleteItemModal = ref(false);
const selectedItem = ref<Item | null>(null);
const itemToDelete = ref<Item | null>(null);

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

const confirmDelete = (item: Item) => {
    itemToDelete.value = item;
    showDeleteItemModal.value = true;
};

const loadBox = async () => {
    try {
        const response = await api.get(`/api/boxes/${props.id}`);
        const boxResponse = await response.json();
        
        // Load items for the box
        const itemsResponse = await api.get(`/api/boxes/${props.id}/items`);
        const itemsData = await itemsResponse.json();
        
        box.value = {
            data: {
                ...boxResponse.data,
                items: itemsData.data
            }
        };
    } catch (error) {
        console.error('Error fetching box details:', error);
    } finally {
        loading.value = false;
    }
};

const handleItemDeleted = () => {
    showDeleteItemModal.value = false;
    selectedItem.value = null;
    loadBox();
};

const handleItemAdded = () => {
    showNewItemModal.value = false;
    loadBox();
};

const handleBoxUpdated = () => {
    showEditBoxModal.value = false;
    loadBox();
};

onMounted(loadBox);
</script>

<template>
    <Head :title="box?.data.name || 'Loading...'" />

    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h1 class="font-semibold text-xl text-indigo-300 leading-tight">
                    {{ box?.data.name }}
                </h1>
            </div>
        </template>

        <div class="py-12 bg-gradient-to-b from-gray-900 to-black min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="loading" class="text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-500 mx-auto"></div>
                    <p class="mt-4 text-indigo-300">Loading box details...</p>
                </div>

                <div v-else-if="box" class="bg-gray-900/70 overflow-hidden rounded-lg backdrop-blur-sm border border-indigo-500/20">
                    <!-- Box Details -->
                    <div class="p-6 border-b border-indigo-500/20 relative">
                        <button
                            class="absolute top-4 right-4 p-2 rounded-full bg-gray-900/80 shadow-sm hover:bg-gray-800/80 border border-indigo-400/30 hover:border-indigo-400/50 shadow-[0_0_10px_rgba(129,140,248,0.2)] hover:shadow-[0_0_15px_rgba(129,140,248,0.3)]"
                            @click="showEditBoxModal = true"
                        >
                            <PencilIcon class="text-indigo-400 hover:text-indigo-300" />
                        </button>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-2xl font-bold text-indigo-300">{{ box.data.name }}</h3>
                                <p class="mt-1 text-xs text-indigo-400/70">
                                    <span class="font-semibold">Last updated:</span>
                                    {{ new Date(box.data.updated_at).toLocaleDateString() }}
                                </p>
                                <p class="mt-4 text-indigo-300/70 mb-2">
                                    <span class="font-semibold">Location:</span> {{ box.data.location }}
                                </p>
                                <p class="text-indigo-300/70 mb-4">
                                    <span class="font-semibold">Description:</span>
                                    {{ box.data.description || 'No description provided' }}
                                </p>
                            </div>
                            <div v-if="box.data.photo_path" class="flex justify-center items-center">
                                <img :src="getItemPhotoPath(box.data.photo_path)" :alt="box.data.name" class="max-h-48 object-contain rounded-lg shadow-[0_0_20px_rgba(129,140,248,0.2)]">
                            </div>
                        </div>
                    </div>

                    <!-- Items List -->
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-xl font-semibold text-indigo-300">Items in this Box</h4>
                            <button
                                @click="showNewItemModal = true"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-indigo-400 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 transition ease-in-out duration-150 shadow-[0_0_15px_rgba(129,140,248,0.5)] hover:shadow-[0_0_25px_rgba(129,140,248,0.7)]"
                            >
                                Add Item
                            </button>
                        </div>

                        <div v-if="box.data.items.length === 0" class="text-center py-8 text-indigo-300/70">
                            No items found in this box.
                        </div>

                        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div v-for="item in box.data.items" :key="item.id" class="bg-gray-800/50 rounded-lg p-4 backdrop-blur-sm border border-indigo-500/20 hover:border-indigo-400/40 relative group shadow-[0_0_15px_rgba(129,140,248,0.1)] hover:shadow-[0_0_25px_rgba(129,140,248,0.2)] transition-all duration-300">
                                <div class="absolute top-2 right-2 flex space-x-1">
                                    <button
                                        class="p-2 rounded-full bg-gray-900/80 shadow-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:bg-gray-800/80 border border-indigo-400/30 hover:border-indigo-400/50"
                                        @click.prevent="editItem(item)"
                                    >
                                        <PencilIcon class="text-indigo-400 hover:text-indigo-300" />
                                    </button>
                                    <button
                                        class="p-2 rounded-full bg-gray-900/80 shadow-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:bg-red-900/80 border border-red-500/30 hover:border-red-400/50"
                                        @click.prevent="confirmDelete(item)"
                                    >
                                        <TrashIcon class="text-red-400 hover:text-red-300" />
                                    </button>
                                </div>
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <img :src="getItemPhotoPath(item.photo_path)" :alt="item.name" class="h-20 w-20 object-cover rounded shadow-[0_0_15px_rgba(129,140,248,0.15)]">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-lg font-medium text-indigo-300 truncate">{{ item.name }}</p>
                                        <p class="text-sm text-indigo-400/70">{{ item.description || 'No description' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="bg-gray-900/70 overflow-hidden rounded-lg backdrop-blur-sm border border-indigo-500/20 p-6">
                    <p class="text-center text-indigo-300">Box not found.</p>
                </div>
            </div>
        </div>

        <NewItemModal
            v-if="box"
            :show="showNewItemModal"
            :box-id="Number(box.data.id)"
            @close="showNewItemModal = false"
            @item-added="handleItemAdded"
        />

        <EditBoxModal
            v-if="box"
            :show="showEditBoxModal"
            :box="box.data"
            @close="showEditBoxModal = false"
            @box-updated="handleBoxUpdated"
        />

        <DeleteItemModal
            v-if="itemToDelete"
            :show="showDeleteItemModal"
            :item="itemToDelete"
            @close="showDeleteItemModal = false"
            @item-deleted="handleItemDeleted"
        />

        <EditItemModal
            v-if="selectedItem"
            :show="showEditItemModal"
            :item="selectedItem"
            @close="showEditItemModal = false; selectedItem = null;"
        />
    </AppLayout>
</template>
