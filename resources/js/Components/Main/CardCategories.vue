<script setup>
import { router } from '@inertiajs/vue3';

defineProps({
   infos: {
    type: Array,
    required: true
   },
});

// Перейти к проектам с фильтром по категории
// const goToCategory = (categoryTitle) => {
//     router.visit(route('projects', { category: categoryTitle }));
// };
</script>

<template>
    <!-- логика новой фильтрации по категориям -->
    <!-- @click="goToCategory(item.title)" -->
    <div
        v-for="(item, index) in infos" 
        :key="index"
        
        class="p-6 flex items-center border-2 border-[#2D1DC0] bg-[#7390C5] gap-3 rounded-[20px] w-max h-max hover:bg-[#5B83CD] transition-colors duration-300"
        >
            <p class="text-2xl text-white font-semibold">{{ item.title }}</p>
    </div>
</template>