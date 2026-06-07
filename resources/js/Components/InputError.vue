<script setup>
// ===== ЦВЕТА КОМПОНЕНТА =====
const colors = {
    error: 'var(--color-error)',
};

defineProps({
    message: {
        type: String,
    },
});
</script>

<template>
    <p
        v-show="message"
        class="mt-1 text-sm font-medium"
        :style="{ color: colors.error }"
        role="alert"
        aria-live="polite"
    >
        {{ message }}
    </p>
</template>