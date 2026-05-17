<script setup>
defineProps({
    projectData:{
        type: Array,
        required: true,
    }
});
const cardColor = "#5B83CD";
</script>

<template>
    <div :style="{backgroundColor: cardColor}" class="flex p-3 justify-between w-full text-xl text-white rounded-xl items-center">
        <p>{{ projectData.name }}</p>
        <p>{{ projectData.summInvest }}</p>
        <p>{{ projectData.timeRelise }}</p>
        <p>{{ projectData.typeBuild }}</p>
        <p>{{ projectData.indystri }}</p>
        <a :href="projectData.link" class="px-4 py-2 border-b-2 bg-[#7390C5] text-2xl rounded-xl hover:border-none hover:bg-[#267FBE] hover:text-black focus:bg-white focus:text-black">Смотреть подробно</a>
    </div>
</template>