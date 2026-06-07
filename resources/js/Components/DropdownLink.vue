<script setup>
import { Link } from '@inertiajs/vue3';

// ===== ЦВЕТА КОМПОНЕНТА =====
const colors = {
    brand: 'var(--color-brand)',
    hover: 'var(--color-hover)',
    light: 'var(--color-light)',
    white: 'var(--color-white)',
    divider: 'var(--color-divider)',
};

const transitions = {
    normal: 'var(--transition-normal)',
};

defineProps({
    href: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Link
        :href="href"
        class="block w-full px-6 py-3 text-start text-base transition-all duration-200 border-b last:border-b-0"
        :style="{ 
            borderColor: colors.divider, 
            color: colors.white,
            transition: transitions.normal
        }"
        @mouseenter="$event.target.style.backgroundColor = colors.light; $event.target.style.color = colors.brand"
        @mouseleave="$event.target.style.backgroundColor = 'transparent'; $event.target.style.color = colors.white"
    >
        <slot />
    </Link>
</template>