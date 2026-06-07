<script setup>
import { computed } from 'vue';

// ===== ЦВЕТА КОМПОНЕНТА =====
const colors = {
    light: 'var(--color-light)',
    accent: 'var(--color-accent)',
    focus: 'var(--color-focus)',
    focusRing: 'var(--color-focus-ring)',
};

const props = defineProps({
    checked: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:checked']);

const model = computed({
    get: () => props.checked,
    set: (value) => emit('update:checked', value),
});
</script>

<template>
    <input
        type="checkbox"
        v-model="model"
        class="rounded focus:ring-2 h-4 w-4"
        :style="{ 
            borderColor: colors.light, 
            accentColor: colors.accent,
            ringColor: colors.focus
        }"
    />
</template>