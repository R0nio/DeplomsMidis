<script setup>
// ===== ЦВЕТА И СТИЛИ КОМПОНЕНТА =====
const colors = {
    brand: 'var(--color-brand)',
};

const fonts = {
    body: 'var(--font-body)',
};

defineProps({
    value: {
        type: String,
    },
    for: {
        type: String,
        default: null
    }
});
</script>

<template>
    <label 
        :for="for"
        class="mb-1.5 block text-lg font-medium tracking-wide"
        :style="{ color: colors.brand, fontFamily: fonts.body }"
    >
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>