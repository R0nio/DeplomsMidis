<script setup>
defineProps({
    infos: {
        type: Array,
        required: true
    }
})

</script>

<template>
    <div v-for="(info) in infos" class="rounded-[56px] bg-[#5B83CD] border border-black px-6 py-16 flex flex-col gap-8">
        <h1 class="text-white text-5xl">{{ info.title }}</h1>
        <p class="text-white text-2xl">{{ info.text }}</p>
    </div>
</template>