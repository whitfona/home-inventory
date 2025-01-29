<script setup lang="ts">
import { ref, watch } from 'vue';
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
    item: Item;
}>();

const emit = defineEmits<{
    close: [];
}>();

const form = ref({
    name: '',
    description: '',
    photo_path: ''
});

const errors = ref({
    name: '',
    description: '',
    photo_path: ''
});

watch(() => props.item, (newItem) => {
    form.value = {
        name: newItem.name,
        description: newItem.description || '',
        photo_path: newItem.photo_path || ''
    };
}, { immediate: true });

const submitForm = async () => {
    try {
        const response = await api.put(`/api/boxes/${props.item.box_id}/items/${props.item.id}`, form.value);

        if (!response.ok) {
            const data = await response.json();
            if (data.errors) {
                errors.value = data.errors;
                return;
            }
            throw new Error('Failed to update item');
        }

        emit('close');
        window.location.reload();
    } catch (error) {
        console.error('Error updating item:', error);
    }
};
</script>

<template>
    <Modal :show="show" @close="$emit('close')">
        <div class="p-6 bg-background/90 backdrop-blur-sm border border-border/20">
            <h2 class="text-lg font-medium text-primary">
                Edit Item
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
                    <TextArea
                        id="description"
                        v-model="form.description"
                        class="mt-1 block w-full bg-gray-800/50 border-border/30 text-tertiary focus:border-secondary focus:ring-secondary/50"
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
                    <SecondaryButton @click="$emit('close')" class="mr-3 border-secondary/30 text-primary hover:bg-gray-800/50">
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
