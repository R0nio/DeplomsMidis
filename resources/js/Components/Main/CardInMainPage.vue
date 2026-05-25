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
        class="px-6 py-8 flex flex-col gap-3 rounded-xl text-center"
        style="background-color: #284139; border: 2px solid #886830;"
    >
        <p class="text-lg font-medium" style="color: #F8D794">{{ item.title }}</p>
        <p class="text-3xl font-bold" style="color: white">{{ item.info }}</p>
    </div>
</template>