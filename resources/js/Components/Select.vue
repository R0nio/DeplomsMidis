<script setup>
// ===== ЦВЕТА И СТИЛИ КОМПОНЕНТА =====
const colors = {
    brand: 'var(--color-brand)',
    accent: 'var(--color-accent)',
    hover: 'var(--color-hover)',
    light: 'var(--color-light)',
    page: 'var(--color-page)',
    text: 'var(--color-text)',
    textSoft: 'var(--color-text-soft)',
    focusRing: 'var(--color-focus-ring)',
};

const transitions = {
    normal: 'var(--transition-normal)',
};

const shadows = {
    sm: 'var(--shadow-sm)',
};

const model = defineModel({
    type: String,
    required: true,
});

defineProps({
    options: {
        type: Array,
        required: true,
    },
    placeholder: {
        type: String,
        default: 'Выберите вашу роль'
    }
});
</script>

<template>
    <select
        v-model="model"
        class="h-12 w-full rounded-xl px-4 shadow-sm transition-all duration-200 outline-none disabled:cursor-not-allowed disabled:opacity-60 appearance-none cursor-pointer"
        :style="{ 
            border: `1px solid ${colors.light}`, 
            backgroundColor: colors.page, 
            color: colors.brand,
            transition: transitions.normal,
            boxShadow: shadows.sm
        }"
    >
        <option disabled value="">{{ placeholder }}</option>
        <option 
            v-for="option in options" 
            :key="option.value" 
            :value="option.value"
        >
            {{ option.label }}
        </option>
    </select>
</template>

<style scoped>
select:focus {
    border-color: var(--color-accent);
    ring: 2px solid var(--color-focus-ring);
    outline: none;
}
select::placeholder {
    color: var(--color-text-soft);
}
</style>