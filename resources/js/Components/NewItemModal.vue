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
    photo_path: null as string | null
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
        const response = await api.post(`/api/boxes/${props.boxId}/items`, form.value);

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
            photo_path: null
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
    <Modal :show="show" @close="$emit('close')">
        <div class="p-6 bg-background/90 backdrop-blur-sm border border-border/20">
            <h2 class="text-lg font-medium text-primary">
                Add New Item
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
                        class="mt-1 block w-full bg-background/50 border-border/30 text-tertiary focus:border-secondary focus:ring-secondary/50"
                        rows="3"
                    />
                    <InputError :message="errors.description" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="photo" value="Photo" class="text-primary" />
                    <input
                        type="file"
                        id="photo"
                        @change="handlePhotoChange"
                        accept="image/*"
                        class="mt-1 block w-full text-primary file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-border file:cursor-pointer file:shadow-[0_0_15px_rgba(129,140,248,0.5)] hover:file:shadow-[0_0_25px_rgba(129,140,248,0.7)]"
                    />
                    <InputError :message="errors.photo" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="$emit('close')" class="mr-3 border-secondary/30 text-primary hover:bg-background-hover/50">
                        Cancel
                    </SecondaryButton>

                    <PrimaryButton :class="{ 'opacity-25': loading }" :disabled="loading" class="bg-indigo-600 hover:bg-border focus:bg-border active:bg-indigo-700 shadow-[0_0_15px_rgba(129,140,248,0.5)] hover:shadow-[0_0_25px_rgba(129,140,248,0.7)]">
                        <span v-if="loading">Creating...</span>
                        <span v-else>Create Item</span>
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>
