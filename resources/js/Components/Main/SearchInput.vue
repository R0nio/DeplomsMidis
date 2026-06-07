<script setup>
import { ref, watch } from 'vue';

// ===== ЦВЕТА И СТИЛИ КОМПОНЕНТА =====
const colors = {
    accent: 'var(--color-accent)',
    light: 'var(--color-light)',
    page: 'var(--color-page)',
    white: 'var(--color-white)',
    brand: 'var(--color-brand)',
    hover: 'var(--color-hover)',
    text: 'var(--color-text)',
    textSoft: 'var(--color-text-soft)',
};

const transitions = {
    normal: 'var(--transition-normal)',
};

const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    },
    placeholder: {
        type: String,
        default: 'Введите название или адрес проекта...'
    }
});

const emit = defineEmits(['update:modelValue']);
const localValue = ref(props.modelValue);

watch(() => props.modelValue, (newVal) => {
    if (localValue.value !== newVal) localValue.value = newVal;
});

watch(localValue, (newValue) => {
    emit('update:modelValue', newValue);
});
</script>

<template>
    <div class="relative w-full">
        <label for="search-input" class="sr-only">Поиск проектов</label>
        <input
            id="search-input"
            v-model="localValue"
            type="search"
            :placeholder="placeholder"
            class="w-full px-4 py-2 text-lg border-2 rounded-xl focus:outline-none focus:ring-2 transition-all"
            :style="{ 
                borderColor: colors.light, 
                backgroundColor: colors.white, 
                color: colors.brand,
                transition: transitions.normal
            }"
            :class="`focus:ring-${colors.accent} focus:border-${colors.accent}`"
            :styleFocus="{ ringColor: colors.accent }"
            aria-label="Введите название или адрес проекта..."
            autocomplete="off"
        />
    </div>
</template>