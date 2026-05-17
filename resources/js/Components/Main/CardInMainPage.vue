<script setup>
defineProps({
   infos: {
    type: Array
   },
});
</script>

<template>
    <div v-for="item in infos">

        <div class="px-6 py-16 flex flex-col border-2 border-[#2D1DC0] bg-[#5B83CD] gap-5 rounded-[20px]">
            <p class="text-xl text-white">{{ item.title }}</p>
            <p class="text-4xl text-white">{{ item.info }}</p>
        </div>
            
    </div>
</template>