<script setup lang="ts">
import TrashIcon from "@/Components/Icons/TrashIcon.vue";
import {Link} from "@inertiajs/vue3";
import {Box} from "@/types";
import {ref} from "vue";
import DeleteBoxModal from "@/Components/DeleteBoxModal.vue";

defineProps<{ box: Box }>()
const emit = defineEmits(['update'])
const boxToDelete = ref<Box | null>(null)
const showDeleteBoxModal = ref(false)
const defaultBoxImage = '/images/packed-box.png'
const confirmDelete = (box: Box) => {
    boxToDelete.value = box
    showDeleteBoxModal.value = true
}

const handleBoxDeleted = () => {
    showDeleteBoxModal.value = false
    boxToDelete.value = null
    emit('update')
}
</script>

<template>
    <div
        :key="box.id"
        class="bg-background/70 overflow-hidden rounded-lg hover:shadow-[0_0_30px_rgba(129,140,248,0.3)] transition-all duration-300 relative group backdrop-blur-sm border border-border/20 hover:border-secondary/40 flex flex-col h-full"
    >
        <div class="absolute top-2 right-2 flex space-x-1 z-10">
            <button
                class="p-2 rounded-full bg-background/80 shadow-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:bg-red-900/80 border border-red-500/30 hover:border-red-400/50"
                @click.prevent="confirmDelete(box)"
            >
                <TrashIcon class="text-red-400 hover:text-red-300" />
            </button>
        </div>
        <Link :href="route('boxes.show', box.id)" class="hover:scale-[1.02] transition-transform duration-300 flex-1 flex flex-col">
            <div class="aspect-video bg-background-hover/50 relative overflow-hidden">
                <img
                    :src="box.photo_path || defaultBoxImage"
                    :alt="box.name"
                    class="w-full h-full object-contain transform hover:scale-105 transition-transform duration-500"
                >
                <div class="absolute inset-0 image-bg-gradient"></div>
            </div>
            <div class="p-6 flex-1 flex flex-col relative">
                <h2 class="text-xl font-semibold text-primary hover:text-tertiary transition-colors duration-200">{{ box.name }}</h2>
                <p class="mt-1 text-xs text-secondary/70">
                    <span class="font-semibold">Last updated:</span>
                    {{ new Date(box.updated_at).toLocaleDateString() }}
                </p>
                <p v-if="box.description" class="mt-2 text-tertiary/80">{{ box.description }}</p>
                <p class="mt-2 text-primary/70">{{ box.location }}</p>
                <p class="mt-2 text-sm text-secondary/90">
                    {{ box.items.length }} item{{ box.items.length === 1 ? '' : 's' }}
                </p>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-600/0 via-border/50 to-indigo-600/0"></div>
            </div>
        </Link>
    </div>

    <DeleteBoxModal
        v-if="boxToDelete"
        :show="showDeleteBoxModal"
        :box="boxToDelete"
        @close="showDeleteBoxModal = false; boxToDelete = null;"
        @box-deleted="handleBoxDeleted"
    />

</template>
