<script setup lang="ts">
withDefaults(
    defineProps<{
        type?: 'button' | 'submit' | 'reset';
    }>(),
    {
        type: 'button',
    },
);
</script>

<template>
    <button
        :type="type"

        class="inline-flex items-center rounded-md border px-4 py-2 text-xs font-semibold uppercase tracking-widest border-indigo-400/40 text-indigo-300 bg-gray-800/50 hover:bg-gray-900/70 hover:border-indigo-300/50 hover:text-indigo-200 transition-all duration-200 shadow-none hover:shadow-[0_0_25px_rgba(109,40,217,0.3)]"
    >
        <slot />
    </button>
</template>
