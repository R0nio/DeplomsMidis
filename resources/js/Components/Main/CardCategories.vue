<script setup>
import { router } from '@inertiajs/vue3';

defineProps({
   infos: {
    type: Array,
    required: true
   },
});

// Перейти к проектам с фильтром по категории
const goToCategory = (categoryTitle) => {
    router.visit(route('projects', { category: categoryTitle }));
};
</script>

<template>
    <p
        v-for="(item, index) in infos" 
        :key="index"
        class="px-6 py-3 flex items-center gap-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#F8D794]"
        style="background-color: #284139; border: 2px solid #886830;"
        role="button"
        :aria-label="`Категория: ${item.title}`"
        tabindex="0"
    >
        <p class="text-lg font-medium" style="color: #F8D794">{{ item.title }}</p>
</p>
</template>