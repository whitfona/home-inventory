<script setup lang="ts">
import {ref} from 'vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import {api} from '@/utils/api';
import type {Item} from '@/types';
import {useToast} from "vue-toast-notification";

const props = defineProps<{
    show: boolean;
    item: Item;
}>();

const emit = defineEmits<{
    close: [];
    'item-updated': [item: Item];
}>();

const toast = useToast()

const form = ref({
    name: props.item.name,
    description: props.item.description,
    photo: null as Blob | null
});

const errors = ref({
    name: '',
    description: '',
    photo: ''
});
const loading = ref(false)

const handlePhotoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        const file = target.files[0];
        form.value.photo = new Blob([file], { type: file.type });
    }
};

const submitForm = async () => {
    try {
        loading.value = true
        const formData = new FormData();
        formData.append('_method', 'PUT');
        formData.append('name', form.value.name);
        formData.append('description', form.value.description ?? '');
        if (form.value.photo) {
            formData.append('photo', form.value.photo);
        }

        const response = await api.post(`/api/boxes/${props.item.box_id}/items/${props.item.id}`, formData);

        if (!response.ok) {
            const data = await response.json();
            if (data.errors) {
                errors.value = data.errors;
                return;
            }
            throw new Error('Failed to update item');
        }

        emit('item-updated', props.item);
        emit('close');

        toast.success('Item updated successfully', {
            position: 'top',
            duration: 3000,
        });
    } catch (error) {
        console.error('Error updating item:', error);
        toast.error('Failed to update item', {
            position: 'top',
            duration: 3000,
        });
    } finally {
        loading.value = false
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
                        class="mt-1"
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
                        class="mt-1 block w-full bg-background/50 border rounded-lg border-border/30 text-tertiary focus:border-secondary focus:ring-secondary/50"
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
