<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

// ===== ЦВЕТА КОМПОНЕНТА =====
const colors = {
    brand: 'var(--color-brand)',
    brandDark: 'var(--color-brand-dark)',
    accent: 'var(--color-accent)',
    hover: 'var(--color-hover)',
    white: 'var(--color-white)',
    white70: 'rgba(255, 255, 255, 0.7)',
    white10: 'rgba(255, 255, 255, 0.1)',
};

const transitions = {
    fast: 'var(--transition-fast)',
    normal: 'var(--transition-normal)',
    slow: 'var(--transition-slow)',
};

const props = defineProps({
    href: { type: String, required: true },
    active: { type: Boolean },
});

const classes = computed(() => {
    if (props.active) {
        return `inline-flex items-center justify-center px-1 py-3 rounded-xl max-sm:p-2 max-sm:w-full h-12 w-max font-medium leading-5 transition-all duration-300 bg-brand/50 text-white/80 border-2 border-brand`;
    }
    return `inline-flex items-center justify-center px-1 py-3 rounded-xl max-sm:p-2 max-sm:w-full w-max h-12 font-normal leading-5 transition-all duration-300 text-white hover:text-accent border-2 border-transparent hover:border-hover`;
});
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>