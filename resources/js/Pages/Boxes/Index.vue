<script setup>
import { ref, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'

const boxes = ref([])
const defaultBoxImage = '/images/packed-box.png'

onMounted(async () => {
  const response = await fetch('/api/boxes', {
    headers: {
      'Accept': 'application/json'
    }
  })
  const data = await response.json()
  boxes.value = data.data
})
</script>

<template>
  <Head title="Boxes" />

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <h1 class="text-2xl font-bold mb-6">Boxes</h1>

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
</template>
