<script setup>
import { ref, watch } from 'vue';

const emit = defineEmits(['search']);

const searchValue = ref('');

// Дебаунс для поиска
let timeout = null;
watch(searchValue, (newValue) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        emit('search', newValue);
    }, 300); // Задержка 300ms
});
</script>

<template>
    <input 
        v-model="searchValue"
        type="text" 
        placeholder="Введите название проекта..."
        class="w-full px-4 py-2 text-lg border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:outline-none"
    >
</template>