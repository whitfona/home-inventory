<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gradient-to-b from-gray-900 to-black pt-6 sm:justify-center sm:pt-0"
    >
        <ApplicationLogo class="h-16 w-16  text-white" />

        <div
            class="mt-6 px-6 py-4 w-full sm:max-w-md bg-gray-900/70 overflow-hidden rounded-lg hover:shadow-[0_0_30px_rgba(129,140,248,0.3)] transition-all duration-300 relative group backdrop-blur-sm border border-indigo-500/20 hover:border-indigo-400/40 flex flex-col h-full"
        >
            <slot />
        </div>
    </div>
</template>
