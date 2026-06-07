<script setup>
import { onMounted, ref } from 'vue';

// ===== ЦВЕТА И СТИЛИ КОМПОНЕНТА =====
const colors = {
    brand: 'var(--color-brand)',
    accent: 'var(--color-accent)',
    hover: 'var(--color-hover)',
    light: 'var(--color-light)',
    page: 'var(--color-page)',
    text: 'var(--color-text)',
    textSoft: 'var(--color-text-soft)',
    focusRing: 'var(--color-focus-ring)',
};

const transitions = {
    normal: 'var(--transition-normal)',
};

const shadows = {
    sm: 'var(--shadow-sm)',
};

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({
    focus: () => input.value?.focus(),
});
</script>

<template>
    <input
        v-model="model"
        ref="input"
        class="h-12 w-full rounded-xl px-4 shadow-sm transition-all duration-200 outline-none disabled:cursor-not-allowed disabled:opacity-60"
        :style="{ 
            border: `1px solid ${colors.light}`, 
            backgroundColor: colors.page, 
            color: colors.brand,
            transition: transitions.normal,
            boxShadow: shadows.sm
        }"
        :aria-invalid="false"
    />
</template>

<style scoped>
input:focus {
    border-color: var(--color-accent);
    ring: 2px solid var(--color-focus-ring);
    outline: none;
}
input::placeholder {
    color: var(--color-text-soft);
}
</style>