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

        class="inline-flex items-center rounded-md border px-4 py-2 text-xs font-semibold uppercase tracking-widest border-secondary/40 text-primary bg-background-hover/50 hover:bg-background/70 hover:border-primary/50 hover:text-tertiary transition-all duration-200 shadow-none hover:shadow-[0_0_25px_rgba(109,40,217,0.3)]"
    >
        <slot />
    </button>
</template>
