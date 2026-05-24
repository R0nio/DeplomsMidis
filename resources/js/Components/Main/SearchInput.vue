<script setup>
import { ref, watch } from 'vue';

defineProps({
    label: {
        type: String,
        default: 'Поиск по проектам',
    },
    id: {
        type: String,
        default: 'project-search',
    },
});

const emit = defineEmits(['search']);

const searchValue = ref('');

let timeout = null;
watch(searchValue, (newValue) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        emit('search', newValue);
    }, 300);
});
</script>

<template>
    <div role="search" class="w-full">
        <label :for="id" class="sr-only">{{ label }}</label>
        <input
            :id="id"
            v-model="searchValue"
            type="search"
            :placeholder="label"
            :aria-label="label"
            class="w-full px-4 py-2 text-lg border-2 border-gray-400 rounded-xl bg-white text-black focus:border-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-700"
        >
    </div>
</template>
