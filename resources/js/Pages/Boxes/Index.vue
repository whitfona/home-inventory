<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AuthenticatedLayout.vue'
import NewBoxModal from '@/Components/NewBoxModal.vue'
import { api } from '@/utils/api'

interface Box {
  id: number
  name: string
  description: string | null
  location: string
  photo_path: string | null
  created_at: string
  updated_at: string
}

const boxes = ref<Box[]>([])
const defaultBoxImage = '/images/packed-box.png'
const showNewBoxModal = ref(false)

onMounted(async () => {
  const response = await api.get('/api/boxes')
  const data = await response.json()
  boxes.value = data.data
})
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
          class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
        >
          New Box
        </button>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="box in boxes"
            :key="box.id"
            class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200"
          >
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
              </div>
            </Link>
          </div>
        </div>
      </div>
    </div>

    <NewBoxModal
      :show="showNewBoxModal"
      @close="showNewBoxModal = false"
    />
  </AppLayout>
</template>
