<script setup>
defineProps({
    projectData:{
        type:Array,
        required: true,
    }
});
const cardColor = "#E8E8E8";
</script>

<template>
    <div :class="`flex p-3 justify-between w-full bg-[${cardColor}] text-lg rounded-xl items-center`">
        <p>{{ projectData.name }}</p>
        <p>{{ projectData.summInvest }}</p>
        <p>{{ projectData.timeRelise }}</p>
        <p>{{ projectData.typeBuild }}</p>
        <p>{{ projectData.indystri }}</p>
        <a :href="projectData.link" class="p-2 bg-white rounded-xl">Смотреть подробно</a>
    </div>
</template>