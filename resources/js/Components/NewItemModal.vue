<script setup lang="ts">
import { ref } from 'vue';
import { useToast } from 'vue-toast-notification';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { api } from '@/utils/api';
import type { Item } from '@/types';

const props = defineProps<{
    show: boolean;
    boxId: number;
}>();

const emit = defineEmits<{
    close: [];
    'item-added': [item: Item];
}>();

const toast = useToast();
const form = ref({
    name: '',
    description: '',
    photo_path: null as string | null,
    box_id: props.boxId
});

const errors = ref({
    name: '',
    description: '',
    photo_path: ''
});

const loading = ref(false);

const submitForm = async () => {
    loading.value = true;
    errors.value = { name: '', description: '', photo_path: '' };

    try {
        const response = await api.post('/api/items', form.value);

        if (!response.ok) {
            const data = await response.json();
            if (data.errors) {
                errors.value = data.errors;
                return;
            }
            throw new Error('Failed to create item');
        }

        const { data } = await response.json();
        emit('item-added', data);
        form.value = {
            name: '',
            description: '',
            photo_path: null,
            box_id: props.boxId
        };
        emit('close');
        toast.success('Item added successfully', {
            position: 'top',
            duration: 3000,
        });
    } catch (error) {
        console.error('Error creating item:', error);
        toast.error('Failed to create item', {
            position: 'top',
            duration: 3000,
        });
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <Modal :show="show" @close="emit('close')">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Add New Item
            </h2>

            <form @submit.prevent="submitForm" class="mt-6">
                <div>
                    <InputLabel for="name" value="Name" />
                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.name"
                        required
                        autofocus
                    />
                    <InputError class="mt-2" :message="errors.name" />
                </div>

                <div class="mt-6">
                    <InputLabel for="description" value="Description" />
                    <TextInput
                        id="description"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.description"
                    />
                    <InputError class="mt-2" :message="errors.description" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="emit('close')" :disabled="loading">
                        Cancel
                    </SecondaryButton>

                    <PrimaryButton
                        class="ms-3"
                        :class="{ 'opacity-25': loading }"
                        :disabled="loading"
                    >
                        <span v-if="loading">Creating...</span>
                        <span v-else>Create Item</span>
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>
