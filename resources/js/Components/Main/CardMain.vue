<script setup>
defineProps({
    title: String,
    infos: {
        type: Array,
        required: true
    },
    photo: {
        type: String,
        required: true
    }
})

</script>

<template>
    <div class="m-6 w-1/2 flex flex-col items-center max-lg:mx-auto">
    <h2 class="mb-8 text-3xl">{{ title }}</h2>
    <div class="h-auto w-auto max-2xl:h-[550px] bg-[#E8E8E8] rounded-xl p-2 mb-8 flex max-2xl:flex-col items-center justify-between">
        <div class="flex flex-col w-auto max-xl:w-[350px]  justify-between h-auto">
            <div class="text-start h-auto max-xl:w-[370px] max-xl:pl-6 w-auto p-3 pl-8 max-xl:p-1" v-for="info in infos">
                <li class="w-auto max-2xl:text-xl 2xl:text-2xl">{{ info.text }}</li>
            </div>
        </div>
        <img :src="photo" class="rounded-xl object-cover 2xl:h-[400px] 2xl:w-[400px] max-2xl:w-[300px] max-2xl:h-[300px]" alt="">
    </div>
    </div>
</template>