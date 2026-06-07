<template>
    <button
        class="inline-flex justify-center items-center h-12 w-full rounded-xl border-none font-semibold transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
        :style="{ 
            backgroundColor: colors.accent, 
            color: colors.white,
            transition: transitions.normal
        }"
        @mouseenter="$event.target.style.backgroundColor = colors.hover"
        @mouseleave="$event.target.style.backgroundColor = colors.accent"
        :disabled="disabled"
        :aria-disabled="disabled"
    >
        <slot />
    </button>
</template>

<script setup>
// ===== ЦВЕТА И СТИЛИ КОМПОНЕНТА =====
const colors = {
    accent: 'var(--color-accent)',
    hover: 'var(--color-hover)',
    white: 'var(--color-white)',
    focus: 'var(--color-focus)',
    focusRing: 'var(--color-focus-ring)',
};

const transitions = {
    normal: 'var(--transition-normal)',
};

defineProps({
    disabled: {
        type: Boolean,
        default: false
    }
});
</script>

<style scoped>
button:focus {
    outline: none;
    ring: 2px solid var(--color-focus);
    ring-offset: 2px;
}
</style>