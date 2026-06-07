<script setup>
// ===== ЦВЕТА И СТИЛИ КОМПОНЕНТА =====
const colors = {
    brand: 'var(--color-brand)',
    brandDark: 'var(--color-brand-dark)',
    accent: 'var(--color-accent)',
    text: 'var(--color-text)',
};

const fonts = {
    heading: 'var(--font-heading)',
};

defineProps({
    value: {
        type: String,
        required: true
    },
    id: {
        type: String,
        default: null
    }
});
</script>

<template>
    <component 
        :is="id ? 'section' : 'div'"
        :id="id"
        class="w-full"
        role="heading"
        :aria-level="id ? 2 : 1"
        tabindex="-1"
    >
        <h1 
            class="text-xl sm:text-2xl lg:text-3xl font-heading tracking-wide"
            :style="{ 
                color: colors.brand, 
                fontFamily: fonts.heading 
            }" 
            :aria-label="`Заголовок раздела: ${value}`"
        >
            {{ value }}
        </h1>
        <span v-if="id" class="sr-only">Начало раздела: {{ value }}</span>
    </component>
</template>