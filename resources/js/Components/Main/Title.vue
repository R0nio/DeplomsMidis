<script setup>
defineProps({
    value: {
        type: String,
        required: true
    },
    id: {
        type: String,
        default: null
    },
    level: {
        type: String,
        default: 'h2',
        validator: (value) => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'].includes(value)
    }
});

// ===== ЦВЕТА И СТИЛИ КОМПОНЕНТА =====
const colors = {
    brand: 'var(--color-brand)',
    text: 'var(--color-text)',
};

const fonts = {
    heading: 'var(--font-heading)',
};
</script>

<template>
    <component 
        :is="level"
        :id="id"
        class="text-2xl sm:text-3xl lg:text-4xl font-heading font-semibold leading-tight focus:outline-none rounded-lg"
        :style="{ color: colors.brand, fontFamily: fonts.heading }"
        tabindex="0"
    >
        {{ value }}
        <span v-if="id" class="sr-only">Начало раздела: {{ value }}</span>
    </component>
</template>