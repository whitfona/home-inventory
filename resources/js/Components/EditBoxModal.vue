<script setup lang="ts">
import { ref, watch } from 'vue';
import { useToast } from 'vue-toast-notification';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { api } from '@/utils/api';
import type { Box } from '@/types';

const props = defineProps<{
    show: boolean;
    box: Box;
}>();

const emit = defineEmits<{
    close: [];
    'box-updated': [box: Box];
}>();

const toast = useToast();
const form = ref({
    name: props.box.name,
    location: props.box.location,
    description: props.box.description || '',
    photo_path: props.box.photo_path || ''
});

const errors = ref({
    name: '',
    location: '',
    description: '',
    photo_path: ''
});

const loading = ref(false);

// Update form when box changes
watch(() => props.box, (newBox) => {
    form.value = {
        name: newBox.name,
        description: newBox.description || '',
        location: newBox.location,
        photo_path: newBox.photo_path || ''
    };
}, { immediate: true });

const submitForm = async () => {
    loading.value = true;
    errors.value = { name: '', location: '', description: '', photo_path: '' };

    try {
        const response = await api.put(`/api/boxes/${props.box.id}`, form.value);

        if (!response.ok) {
            const data = await response.json();
            if (data.errors) {
                errors.value = data.errors;
                return;
            }
            throw new Error('Failed to update box');
        }

        const { data } = await response.json();
        emit('box-updated', data);
        emit('close');
        toast.success('Box updated successfully', {
            position: 'top',
            duration: 3000,
        });
    } catch (error) {
        console.error('Error updating box:', error);
        toast.error('Failed to update box', {
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
                Edit Box
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
                    <InputLabel for="location" value="Location" />
                    <TextInput
                        id="location"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.location"
                        required
                    />
                    <InputError class="mt-2" :message="errors.location" />
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
                        <span v-if="loading">Saving...</span>
                        <span v-else>Save Changes</span>
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template> 