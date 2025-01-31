<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AuthenticatedLayout.vue'
import NewBoxModal from '@/Components/NewBoxModal.vue'
import { api } from '@/utils/api'
import TrashIcon from '@/Components/Icons/TrashIcon.vue'
import DeleteBoxModal from '@/Components/DeleteBoxModal.vue'
import type { Box } from '@/types'
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CardBox from "@/Components/CardBox.vue";

const boxes = ref<Box[]>([])
const showNewBoxModal = ref(false)
const searchQuery = ref('')
const isSearching = ref(false)

const loadBoxes = async () => {
  const response = await api.get('/api/boxes')
  const data = await response.json()
  boxes.value = data.data
}

const searchItems = async () => {
  if (!searchQuery.value.trim()) {
    await loadBoxes()
    return
  }

  isSearching.value = true
  try {
    const response = await api.get(`/api/search?q=${encodeURIComponent(searchQuery.value)}`)
    const { data } = await response.json()
    boxes.value = data
  } catch (error) {
    console.error('Error searching items:', error)
  } finally {
    isSearching.value = false
  }
}

watch(searchQuery, () => {
  const timeoutId = setTimeout(searchItems, 300)
  return () => clearTimeout(timeoutId)
})

onMounted(loadBoxes)

const handleBoxAdded = () => {
  showNewBoxModal.value = false
  loadBoxes()
}
</script>

<template>
  <Head title="Boxes" />

  <AppLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h1 class="font-semibold text-xl text-primary leading-tight">
          Boxes
        </h1>
          <PrimaryButton @click="showNewBoxModal = true">
              Add Box
          </PrimaryButton>
      </div>
    </template>

    <div class="py-12 bg-gradient min-h-screen">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Search Input -->
        <div class="mb-6">
            <TextInput
                id="search"
                ref="searchInput"
                type="text"
                v-model="searchQuery"
                placeholder="Search items..."
            />
          <p v-if="isSearching" class="mt-2 text-sm text-primary">
            Searching...
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <CardBox
            v-for="box in boxes"
            :box="box"
            @update="loadBoxes"
          ></CardBox>
        </div>
      </div>
    </div>

    <NewBoxModal
      :show="showNewBoxModal"
      @box-added="handleBoxAdded"
      @close="showNewBoxModal = false"
    />
  </AppLayout>
</template>
