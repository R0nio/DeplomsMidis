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
        class="px-6 py-8 flex flex-col gap-3 rounded-xl text-center focus-within:ring-2 focus-within:ring-[#F8D794]"
        style="background-color: #284139; border: 2px solid #886830;"
        role="article"
        :aria-label="`Статистика: ${item.title} — ${item.info}`"
    >
        <p class="text-lg font-medium" style="color: #F8D794" :id="`stat-title-${index}`">
            {{ item.title }}
        </p>
        <p class="text-3xl font-bold" style="color: white" aria-labelledby="`stat-title-${index}`">
            {{ item.info }}
        </p>
    </div>
</template>