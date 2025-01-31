<script setup lang="ts">

import TrashIcon from "@/Components/Icons/TrashIcon.vue";
import PencilIcon from "@/Components/Icons/PencilIcon.vue";
import {Item} from "@/types";
import {ref} from "vue";
import EditItemModal from "@/Components/EditItemModal.vue";
import DeleteItemModal from "@/Components/DeleteItemModal.vue";

defineProps<{ items: Item[] }>()

const emit = defineEmits(['update'])

const defaultItemImage = '/images/question-mark.png'
const selectedItem = ref<Item | null>(null)
const showEditItemModal = ref(false)
const itemToDelete = ref<Item | null>(null)
const showDeleteItemModal = ref(false)

const editItem = (item: Item) => {
    selectedItem.value = item
    showEditItemModal.value = true
}

const confirmDelete = (item: Item) => {
    itemToDelete.value = item
    showDeleteItemModal.value = true
}

const handleItemDeleted = () => {
    showDeleteItemModal.value = false
    selectedItem.value = null
    emit('update')
}

const handleItemUpdated = () => {
    showEditItemModal.value = false
    selectedItem.value = null
    emit('update')
}

</script>

<template>
    <div v-for="item in items" :key="item.id" class="bg-background-hover/50 rounded-lg p-4 backdrop-blur-sm border border-border/20 hover:border-secondary/40 relative group shadow-[0_0_15px_rgba(129,140,248,0.1)] hover:shadow-[0_0_25px_rgba(129,140,248,0.2)] transition-all duration-300">
        <div class="absolute top-2 right-2 flex space-x-1">
            <button
                class="p-2 rounded-full bg-background/80 shadow-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:bg-background-hover/80 border border-secondary/30 hover:border-secondary/50"
                @click.prevent="editItem(item)"
            >
                <PencilIcon class="text-secondary hover:text-primary" />
            </button>
            <button
                class="p-2 rounded-full bg-background/80 shadow-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:bg-red-900/80 border border-red-500/30 hover:border-red-400/50"
                @click.prevent="confirmDelete(item)"
            >
                <TrashIcon class="text-red-400 hover:text-red-300" />
            </button>
        </div>
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
                <img :src="item.photo_path || defaultItemImage" :alt="item.name" class="h-20 w-20 object-cover rounded shadow-[0_0_15px_rgba(129,140,248,0.15)]">
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-lg font-medium text-primary truncate">{{ item.name }}</p>
                <p class="text-sm text-secondary/70">{{ item.description || 'No description' }}</p>
            </div>
        </div>
    </div>

    <DeleteItemModal
        v-if="itemToDelete"
        :show="showDeleteItemModal"
        :item="itemToDelete"
        @close="showDeleteItemModal = false"
        @item-deleted="handleItemDeleted"
    />

    <EditItemModal
        v-if="selectedItem"
        :show="showEditItemModal"
        :item="selectedItem"
        @close="showEditItemModal = false; selectedItem = null;"
        @item-updated="handleItemUpdated"
    />
</template>
