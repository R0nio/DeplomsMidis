<script setup>
defineProps({
   infos: {
    type: Array,
    required: true
   },
});
</script>

<template>
    <article
        v-for="(item, index) in infos"
        :key="index"
        class="px-6 py-16 flex flex-col border-2 border-[#2D1DC0] bg-[#5B83CD] gap-5 rounded-[20px] "
    >
        <h3 class="text-xl text-white font-semibold">{{ item.title }}</h3>
        <p class="text-4xl text-white font-bold" aria-label="Значение">{{ item.info }}</p>
    </article>
</template>
