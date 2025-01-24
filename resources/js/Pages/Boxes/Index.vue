<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AuthenticatedLayout.vue'
import NewBoxModal from '@/Components/NewBoxModal.vue'
import { api } from '@/utils/api'
import TrashIcon from '@/Components/Icons/TrashIcon.vue'
import DeleteBoxModal from '@/Components/DeleteBoxModal.vue'
import type { Box } from '@/types'

const boxes = ref<Box[]>([])
const defaultBoxImage = '/images/packed-box.png'
const showNewBoxModal = ref(false)
const showDeleteBoxModal = ref(false)
const boxToDelete = ref<Box | null>(null)
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
      console.log('data', data)
    boxes.value = data.boxes;
    // Get unique boxes from search results
    // const uniqueBoxes = new Map()
    // data.forEach(result => {
    //   if (!uniqueBoxes.has(result.box.id)) {
    //     uniqueBoxes.set(result.box.id, {
    //       ...result.box,
    //       items: []
    //     })
    //   }
    //   uniqueBoxes.get(result.box.id).items.push(result.item)
    // })
    //
    // boxes.value = Array.from(uniqueBoxes.values())
  } catch (error) {
    console.error('Error searching:', error)
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

const confirmDelete = (box: Box) => {
  boxToDelete.value = box
  showDeleteBoxModal.value = true
}

const handleBoxDeleted = () => {
  showDeleteBoxModal.value = false
  boxToDelete.value = null
  loadBoxes()
}
</script>

<template>
  <Head title="Boxes" />

  <AppLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h1 class="font-semibold text-xl text-gray-800 leading-tight">
          Boxes
        </h1>
        <button
          @click="showNewBoxModal = true"
          class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
        >
          New Box
        </button>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Search Input -->
        <div class="mb-6">
          <input
            v-model="searchQuery"
            type="search"
            placeholder="Search items..."
            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
          >
          <p v-if="isSearching" class="mt-2 text-sm text-gray-500">
            Searching...
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="box in boxes"
            :key="box.id"
            class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200 relative group"
          >
            <div class="absolute top-2 right-2 flex space-x-1 z-10">
              <button
                class="p-2 rounded-full bg-white shadow-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:bg-red-50"
                @click.prevent="confirmDelete(box)"
              >
                <TrashIcon class="text-red-600" />
              </button>
            </div>
            <Link :href="route('boxes.show', box.id)">
              <div class="aspect-video bg-gray-100">
                <img
                  :src="box.photo_path || defaultBoxImage"
                  :alt="box.name"
                  class="w-full h-full object-contain"
                >
              </div>
              <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 hover:text-indigo-600">{{ box.name }}</h2>
                <p class="mt-1 text-xs text-gray-500">
                  <span class="font-semibold">Last updated:</span>
                  {{ new Date(box.updated_at).toLocaleDateString() }}
                </p>
                <p v-if="box.description" class="mt-2 text-gray-600">{{ box.description }}</p>
                <p class="mt-2 text-gray-500">{{ box.location }}</p>
                <p class="mt-2 text-sm text-gray-500">
                  {{ box.items.length }} item{{ box.items.length === 1 ? '' : 's' }}
                </p>
              </div>
            </Link>
          </div>
        </div>
      </div>
    </div>

    <NewBoxModal
      :show="showNewBoxModal"
      @box-added="handleBoxAdded"
      @close="showNewBoxModal = false"
    />

    <DeleteBoxModal
      v-if="boxToDelete"
      :show="showDeleteBoxModal"
      :box="boxToDelete"
      @close="showDeleteBoxModal = false; boxToDelete = null;"
      @box-deleted="handleBoxDeleted"
    />
  </AppLayout>
</template>
