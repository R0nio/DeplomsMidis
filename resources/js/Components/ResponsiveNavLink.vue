<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

// ===== ЦВЕТА КОМПОНЕНТА =====
const colors = {
    brand: 'var(--color-brand)',
    accent: 'var(--color-accent)',
    hover: 'var(--color-hover)',
    white: 'var(--color-white)',
    white70: 'rgba(255, 255, 255, 0.7)',
    white5: 'rgba(255, 255, 255, 0.05)',
};

const transitions = {
    normal: 'var(--transition-normal)',
};

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() => {
    if (props.active) {
        return `block w-full px-4 py-3 rounded-xl border-2 bg-brand/50 text-white/80 text-base font-medium leading-5 transition-all duration-300`;
    }
    return `block w-full px-4 py-3 rounded-xl border-2 border-transparent text-white text-base font-normal  leading-5 transition-all duration-300 hover:border-hover/30 hover:bg-white/5 hover:text-white`;
});
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>