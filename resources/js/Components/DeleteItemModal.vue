<script setup lang="ts">
import { ref } from 'vue';
import { useToast } from 'vue-toast-notification';
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { api } from '@/utils/api';
import type { Item } from '@/types';

const props = defineProps<{
    show: boolean;
    item: Item;
}>();

const emit = defineEmits<{
    close: [];
    'item-deleted': [item: Item];
}>();

const toast = useToast();
const loading = ref(false);

const deleteItem = async () => {
    loading.value = true;

    try {
        const response = await api.delete(`/api/boxes/${props.item.box_id}/items/${props.item.id}`);

        if (!response.ok) {
            throw new Error('Failed to delete item');
        }

        emit('item-deleted', props.item);
        emit('close');
        toast.success('Item deleted successfully', {
            position: 'top',
            duration: 3000,
        });
    } catch (error) {
        console.error('Error deleting item:', error);
        toast.error('Failed to delete item', {
            position: 'top',
            duration: 3000,
        });
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <Modal :show="show" @close="$emit('close')">
        <div class="p-6 bg-gray-900/90 backdrop-blur-sm border border-red-500/20">
            <h2 class="text-lg font-medium text-red-400">
                Delete Item
            </h2>

            <p class="mt-3 text-sm text-indigo-300">
                Are you sure you want to delete "{{ item.name }}"? This action cannot be undone.
            </p>

            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="$emit('close')" class="mr-3 border-indigo-400/30 text-indigo-300 hover:bg-gray-800/50">
                    Cancel
                </SecondaryButton>

                <DangerButton
                    :class="{ 'opacity-25': loading }"
                    :disabled="loading"
                    @click="deleteItem"
                    class="bg-red-600 hover:bg-red-500 focus:bg-red-500 active:bg-red-700 shadow-[0_0_15px_rgba(239,68,68,0.5)] hover:shadow-[0_0_25px_rgba(239,68,68,0.7)]"
                >
                    Delete Item
                </DangerButton>
            </div>
        </div>
    </Modal>
</template>
