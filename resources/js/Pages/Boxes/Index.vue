<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AuthenticatedLayout.vue'

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

onMounted(async () => {
  const response = await fetch('/api/boxes')
  const data = await response.json()
  boxes.value = data.data
})
</script>

<template>
  <Head title="Boxes" />

  <AppLayout>
    <template #header>
      <h1 class="font-semibold text-xl text-gray-800 leading-tight">
        Boxes
      </h1>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="box in boxes"
            :key="box.id"
            class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
          >
            <div class="aspect-video bg-gray-100">
              <img
                :src="box.photo_path || defaultBoxImage"
                :alt="box.name"
                class="w-full h-full object-contain"
              >
            </div>
            <div class="p-6">
              <h2 class="text-xl font-semibold">{{ box.name }}</h2>
              <p v-if="box.description" class="mt-2 text-gray-600">{{ box.description }}</p>
              <p class="mt-2 text-gray-500">{{ box.location }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
