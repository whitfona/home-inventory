<script setup lang="ts">
import { onMounted, ref } from 'vue';

const model = defineModel<string>({ required: true });

const input = ref<HTMLInputElement | null>(null);

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value?.focus();
    }
});

defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
    <input
        class="w-full px-4 py-3 bg-gray-900/50 border border-indigo-500/30 rounded-lg shadow-sm focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 text-indigo-100 placeholder-indigo-300/50 backdrop-blur-sm transition-all duration-200 hover:border-indigo-400/50 shadow-[0_0_10px_rgba(129,140,248,0.2)]"
        v-model="model"
        ref="input"
    />
</template>
