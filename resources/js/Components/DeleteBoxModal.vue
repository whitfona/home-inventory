<script setup lang="ts">
import { ref } from 'vue';
import { useToast } from 'vue-toast-notification';
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { api } from '@/utils/api';
import type { Box } from '@/types';

const props = defineProps<{
    show: boolean;
    box: Box;
}>();

const emit = defineEmits<{
    close: [];
    'box-deleted': [box: Box];
}>();

const toast = useToast();
const loading = ref(false);

const deleteBox = async () => {
    loading.value = true;

    try {
        const response = await api.delete(`/api/boxes/${props.box.id}`);

        if (!response.ok) {
            throw new Error('Failed to delete box');
        }

        emit('box-deleted', props.box);
        emit('close');
        toast.success('Box deleted successfully', {
            position: 'top',
            duration: 3000,
        });
    } catch (error) {
        console.error('Error deleting box:', error);
        toast.error('Failed to delete box', {
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
                Delete Box
            </h2>

            <p class="mt-3 text-sm text-gray-600">
                Are you sure you want to delete "{{ box.name }}"?
                <span v-if="box.items.length" class="font-medium text-red-600">
                    This box contains {{ box.items.length }} item{{ box.items.length === 1 ? '' : 's' }}.
                </span>
                This action cannot be undone.
            </p>

            <div class="mt-6 flex justify-end space-x-3">
                <SecondaryButton @click="emit('close')">
                    Cancel
                </SecondaryButton>

                <DangerButton
                    @click="deleteBox"
                    :class="{ 'opacity-25': loading }"
                    :disabled="loading"
                >
                    Delete Box
                </DangerButton>
            </div>
        </div>
    </Modal>
</template>
