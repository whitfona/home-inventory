<script setup lang="ts">
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { api } from '@/utils/api';

interface Item {
    id: number;
    name: string;
}

const props = defineProps<{
    show: boolean;
    item: Item;
}>();

const emit = defineEmits<{
    close: [];
}>();

const isDeleting = ref(false);

const deleteItem = async () => {
    isDeleting.value = true;

    try {
        const response = await api.delete(`/api/items/${props.item.id}`);

        if (!response.ok) {
            throw new Error('Failed to delete item');
        }

        emit('close');
        window.location.reload();
    } catch (error) {
        console.error('Error deleting item:', error);
    } finally {
        isDeleting.value = false;
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
                    :class="{ 'opacity-25': isDeleting }"
                    :disabled="isDeleting"
                >
                    Delete Item
                </DangerButton>
            </div>
        </div>
    </Modal>
</template>
