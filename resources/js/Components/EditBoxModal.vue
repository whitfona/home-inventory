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
    <Modal :show="show" @close="$emit('close')">
        <div class="p-6 bg-background/90 backdrop-blur-sm border border-border/20">
            <h2 class="text-lg font-medium text-primary">
                Edit Box
            </h2>

            <form @submit.prevent="submitForm" class="mt-6 space-y-6">
                <div>
                    <InputLabel for="name" value="Name" class="text-primary" />
                    <TextInput
                        id="name"
                        type="text"
                        v-model="form.name"
                        class="mt-1 block w-full bg-background-hover/50 border-border/30 text-tertiary focus:border-secondary focus:ring-secondary/50"
                        required
                        autofocus
                    />
                    <InputError :message="errors.name" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="description" value="Description" class="text-primary" />
                    <textarea
                        id="description"
                        v-model="form.description"
                        class="mt-1 block w-full bg-background-hover/50 border-border/30 text-tertiary focus:border-secondary focus:ring-secondary/50"
                        rows="3"
                    />
                    <InputError :message="errors.description" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="location" value="Location" class="text-primary" />
                    <TextInput
                        id="location"
                        type="text"
                        v-model="form.location"
                        class="mt-1 block w-full bg-background-hover/50 border-border/30 text-tertiary focus:border-secondary focus:ring-secondary/50"
                        required
                    />
                    <InputError :message="errors.location" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <SecondaryButton
                        @click="$emit('close')"
                    >
                        Cancel
                    </SecondaryButton>

                    <PrimaryButton :class="{ 'opacity-25': loading }" :disabled="loading" class="bg-indigo-600 hover:bg-border focus:bg-border active:bg-indigo-700 shadow-[0_0_15px_rgba(129,140,248,0.5)] hover:shadow-[0_0_25px_rgba(129,140,248,0.7)]">
                        <span v-if="loading">Saving...</span>
                        <span v-else>Save Changes</span>
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>
