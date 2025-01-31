<script setup lang="ts">
import { ref, onMounted } from 'vue';
import AppLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import NewItemModal from '@/Components/NewItemModal.vue';
import EditBoxModal from '@/Components/EditBoxModal.vue';
import PencilIcon from '@/Components/Icons/PencilIcon.vue';
import { api } from '@/utils/api';
import type { Box, Item } from '@/types';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import CardItem from "@/Components/CardItem.vue";

interface BoxResponse {
    data: Box;
}

const props = defineProps<{
    id: string;
}>();

const box = ref<BoxResponse | null>(null);
const defaultBoxImage = '/images/packed-box.png'
const loading = ref(true);
const showEditBoxModal = ref(false);
const showNewItemModal = ref(false);

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
                <h1 class="font-semibold text-xl text-primary leading-tight">
                    {{ box?.data.name }}
                </h1>
            </div>
        </template>

        <div class="py-12 bg-gradient min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="loading" class="text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-border mx-auto"></div>
                    <p class="mt-4 text-primary">Loading box details...</p>
                </div>

                <div v-else-if="box" class="bg-background/70 overflow-hidden rounded-lg backdrop-blur-sm border border-border/20">
                    <!-- Box Details -->
                    <div class="p-6 border-b border-border/20 relative">
                        <button
                            class="absolute top-4 right-4 p-2 rounded-full bg-background/80 hover:bg-background-hover/80 border border-secondary/30 hover:border-secondary/50 shadow-[0_0_10px_rgba(129,140,248,0.2)] hover:shadow-[0_0_15px_rgba(129,140,248,0.3)]"
                            @click="showEditBoxModal = true"
                        >
                            <PencilIcon class="text-secondary hover:text-primary" />
                        </button>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-2xl font-bold text-primary">{{ box.data.name }}</h3>
                                <p class="mt-1 text-xs text-secondary/70">
                                    <span class="font-semibold">Last updated:</span>
                                    {{ new Date(box.data.updated_at).toLocaleDateString() }}
                                </p>
                                <p class="mt-4 text-primary/70 mb-2">
                                    <span class="font-semibold">Location:</span> {{ box.data.location }}
                                </p>
                                <p class="text-primary/70 mb-4">
                                    <span class="font-semibold">Description:</span>
                                    {{ box.data.description || 'No description provided' }}
                                </p>
                            </div>
                            <div class="flex justify-center items-center">
                                <img :src="box.data.photo_path || defaultBoxImage" :alt="box.data.name" class="max-h-48 object-contain rounded-lg shadow-[0_0_20px_rgba(129,140,248,0.2)]">
                            </div>
                        </div>
                    </div>

                    <!-- Items List -->
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-xl font-semibold text-primary">Items in this Box</h4>
                            <PrimaryButton
                                @click="showNewItemModal = true"
                            >
                                Add Item
                            </PrimaryButton>
                        </div>

                        <div v-if="box.data.items.length === 0" class="text-center py-8 text-primary/70">
                            No items found in this box.
                        </div>

                        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <CardItem :items="box.data.items" @update="loadBox" />
                        </div>
                    </div>
                </div>

                <div v-else class="bg-background/70 overflow-hidden rounded-lg backdrop-blur-sm border border-border/20 p-6">
                    <p class="text-center text-primary">Box not found.</p>
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
    </AppLayout>
</template>
