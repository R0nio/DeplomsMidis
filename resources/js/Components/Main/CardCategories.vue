<script setup>
defineProps({
   infos: {
    type: Array
   },
});
</script>

<template>
    <div v-for="item in infos">
        <div class="p-6 flex border-2 border-[#2D1DC0] bg-[#7390C5] gap-5 rounded-[20px] w-max h-max">
            <p class="text-2xl text-white">{{ item.title }}</p>
        </div>  
            
    </div>
</template>