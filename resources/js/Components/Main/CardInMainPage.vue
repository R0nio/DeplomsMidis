<script setup>
defineProps({
   infos: {
    type: Array,
    required: true
   },
});
</script>

<template>
    <div 
        v-for="(item, index) in infos" 
        :key="index"
        class="px-6 py-16 flex flex-col border-2 border-[#2D1DC0] bg-[#5B83CD] gap-5 rounded-[20px] "
    >
        <p class="text-xl text-white font-semibold">{{ item.title }}</p>
        <p class="text-4xl text-white font-bold">{{ item.info }}</p>
    </div>
</template>