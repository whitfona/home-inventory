<script setup lang="ts">
import { ref } from 'vue';
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
}>();

const loading = ref(false);

const deleteBox = async () => {
    loading.value = true;
    try {
        const response = await api.delete(`/api/boxes/${props.box.id}`);

        if (!response.ok) {
            throw new Error('Failed to delete box');
        }

        emit('close');
        window.location.reload();
    } catch (error) {
        console.error('Error deleting box:', error);
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <Modal :show="show" @close="$emit('close')">
        <div class="p-6 bg-gray-900/90 backdrop-blur-sm border border-red-500/20">
            <h2 class="text-lg font-medium text-red-400">
                Delete Box
            </h2>

            <div class="mt-4 space-y-4">
                <p class="text-gray-300">
                    Are you sure you want to delete <span class="font-semibold text-red-400">{{ box.name }}</span>? This action will permanently delete this box and all its items. This action cannot be undone.
                </p>

                <div class="mt-6 flex justify-end space-x-3">
                    <SecondaryButton 
                        @click="$emit('close')" 
                        class="border-red-400/30 text-red-300 hover:bg-gray-800/50"
                    >
                        Cancel
                    </SecondaryButton>

                    <DangerButton 
                        @click="deleteBox" 
                        :class="{ 'opacity-25': loading }" 
                        :disabled="loading"
                        class="bg-red-600 hover:bg-red-500 focus:bg-red-500 active:bg-red-700 shadow-[0_0_15px_rgba(239,68,68,0.5)] hover:shadow-[0_0_25px_rgba(239,68,68,0.7)]"
                    >
                        <span v-if="loading">Deleting...</span>
                        <span v-else>Delete Box</span>
                    </DangerButton>
                </div>
            </div>
        </div>
    </Modal>
</template>
