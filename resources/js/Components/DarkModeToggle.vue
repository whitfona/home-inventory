<script setup lang="ts">
import { useToggle, useDark } from '@vueuse/core'
import Sun from "@/Components/Icons/Sun.vue";
import Moon from "@/Components/Icons/Moon.vue";

const isDark = useDark({
    selector: 'body',
    valueDark: 'dark',
    valueLight: 'light'
})
const toggleDark = useToggle(isDark)
</script>

<template>
    <button @click="toggleDark()" class="w-full px-4 py-2 flex gap-2 items-center text-sm transition duration-150 ease-in-out hover:bg-background-hover focus:bg-background-hover focus:outline-none">

        <Sun v-if="isDark" size="1.5rem" stroke="primary" />
        <Moon v-else size="1rem" fill="primary" />

        <span class="text-primary">{{ isDark ? 'Light' : 'Dark' }}</span>
    </button>
</template>
