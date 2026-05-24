<script setup>
defineProps({
    infos: {
        type: Array,
        required: true
    }
})

</script>

<template>
    <article v-for="(info, index) in infos" :key="index" class="rounded-[56px] bg-[#5B83CD] border border-black px-6 py-16 flex flex-col gap-8">
        <h3 class="text-white text-5xl">{{ info.title }}</h3>
        <p class="text-white text-2xl">{{ info.text }}</p>
    </article>
</template>
