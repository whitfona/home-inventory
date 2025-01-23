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
        const response = await api.delete(`/api/items/${props.item.id}`);

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
    <Modal :show="show" @close="emit('close')" max-width="sm">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Delete Item
            </h2>

            <p class="mt-3 text-sm text-gray-600">
                Are you sure you want to delete "{{ item.name }}"? This action cannot be undone.
            </p>

            <div class="mt-6 flex justify-end space-x-3">
                <SecondaryButton @click="emit('close')">
                    Cancel
                </SecondaryButton>

                <DangerButton
                    @click="deleteItem"
                    :class="{ 'opacity-25': loading }"
                    :disabled="loading"
                >
                    Delete Item
                </DangerButton>
            </div>
        </div>
    </Modal>
</template>
