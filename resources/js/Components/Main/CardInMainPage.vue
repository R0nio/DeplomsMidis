<script setup>
// ===== ЦВЕТА И СТИЛИ КОМПОНЕНТА =====
const colors = {
    brand: 'var(--color-brand)',
    light: 'var(--color-light)',
    hover: 'var(--color-hover)',
};

const fonts = {
    heading: 'var(--font-heading)',
    body: 'var(--font-body)',
};

const transitions = {
    normal: 'var(--transition-normal)',
};

defineProps({
    infos: {
        type: Array,
        required: true
    },
});
</script>

<template>
    <div 
        v-for="(item, index) in infos" 
        :key="index"
        class="px-6 py-8 flex flex-col gap-3 rounded-xl text-center transition-all duration-200 hover:-translate-y-0.5"
        :style="{ 
            backgroundColor: colors.light, 
            border: `1px solid ${colors.hover}`,
            transition: transitions.normal
        }"
        role="article"
        tabindex="0"
        :aria-label="`Статистика: ${item.title} — ${item.info}`"
    >
        <p class="text-base font-medium" :style="{ color: colors.brand, fontFamily: fonts.heading }">
            {{ item.title }}
        </p>
        <p class="text-3xl sm:text-4xl font-bold" :style="{ color: colors.brand, fontFamily: fonts.body }">
            {{ item.info }}
        </p>
    </div>
</template>