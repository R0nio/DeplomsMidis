<script setup>
defineProps({
    img: String,
    alt: {
        type: String,
        default: 'Челябинская область'
    }
})
</script>
<template>
    <div class="mt-6 mb-16 w-full rounded-xl">
        <img :src="img" :alt="alt"
            class="w-full h-[300px] sm:h-[400px] md:h-[500px] lg:h-[600px] xl:h-[700px] 2xl:h-[850px] object-cover rounded-xl">
    </div>
</template>
