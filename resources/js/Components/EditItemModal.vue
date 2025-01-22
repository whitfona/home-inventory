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

const resetForm = () => {
    form.value = {
        name: props.item.name,
        description: props.item.description || '',
        photo_path: props.item.photo_path || ''
    };
    errors.value = {
        name: '',
        description: '',
        photo_path: ''
    };
};

const submitForm = async () => {
    try {
        const response = await api.put(`/api/items/${props.item.id}`, form.value);

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
    <Modal :show="show" @close="emit('close')">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Edit Item
            </h2>

            <div class="mt-6">
                <form @submit.prevent="submitForm" class="space-y-6">
                    <div>
                        <InputLabel for="name" value="Name" />
                        <TextInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="errors.name" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="description" value="Description" />
                        <TextInput
                            id="description"
                            v-model="form.description"
                            type="text"
                            class="mt-1 block w-full"
                        />
                        <InputError :message="errors.description" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="photo_path" value="Photo Path" />
                        <TextInput
                            id="photo_path"
                            v-model="form.photo_path"
                            type="text"
                            class="mt-1 block w-full"
                        />
                        <InputError :message="errors.photo_path" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <SecondaryButton class="mr-3" @click="emit('close')">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton :class="{ 'opacity-25': false }" :disabled="false">
                            Update Item
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </Modal>
</template>
