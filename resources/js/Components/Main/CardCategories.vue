<script setup>
import { router } from '@inertiajs/vue3';

defineProps({
   infos: {
    type: Array,
    required: true
   },
});
</script>

<template>
    <ul class="contents list-none m-0 p-0" aria-label="Категории проектов">
        <li
            v-for="(item, index) in infos"
            :key="index"
            class="p-6 flex items-center border-2 border-[#2D1DC0] bg-[#7390C5] gap-3 rounded-[20px] w-max h-max hover:bg-[#5B83CD] transition-colors duration-300"
        >
            <span class="text-2xl text-white font-semibold">{{ item.title }}</span>
        </li>
    </ul>
</template>
