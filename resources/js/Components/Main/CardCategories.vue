<script setup>
const colors = {
    brand: 'var(--color-brand)',
    light: 'var(--color-light)',
    hover: 'var(--color-hover)',
    white: 'var(--color-white)',
};

const fonts = {
    heading: 'var(--font-heading)',
};

const props = defineProps({
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
        class="px-6 py-3 flex items-center justify-center gap-3 rounded-full transition-all duration-200 cursor-pointer hover:-translate-y-0.5 group"
        :style="{ 
            backgroundColor: colors.light, 
            border: `1px solid ${colors.hover}`,
            transition: 'var(--transition-normal)',
            whiteSpace: 'nowrap'
        }"
        @mouseenter="$event.currentTarget.style.backgroundColor = colors.hover"
        @mouseleave="$event.currentTarget.style.backgroundColor = colors.light"
    >
        <span 
            class="text-lg font-medium transition-colors duration-200"
            :style="{ color: colors.brand, fontFamily: fonts.heading }"
            @mouseenter="$event.target.style.color = colors.white"
            @mouseleave="$event.target.style.color = colors.brand"
        >
            {{ item.title || item.name }}
        </span>
    </div>
</template>