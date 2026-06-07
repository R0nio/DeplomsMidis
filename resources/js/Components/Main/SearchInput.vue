<script setup>
import { ref, watch } from 'vue';

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

// Следим за внешними изменениями
watch(() => props.modelValue, (newVal) => {
    if (localValue.value !== newVal) {
        localValue.value = newVal;
    }
});

// Мгновенно эмитим при вводе (без debounce)
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
            class="w-full px-4 py-2 text-lg border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:outline-none"
            aria-label="Введите название или адрес проекта..."
            autocomplete="off"
        />
    </div>
</template>