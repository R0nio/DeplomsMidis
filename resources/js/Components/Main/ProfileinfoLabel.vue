<script setup>
defineProps({
    title:{
        type: String,
        required: true,
    },
    info:{
        type: String,
        required: true
    }
})
</script>

<template>
    <div class="">
        <h3 class="text-2xl text-white ml-3 mb-1">{{title}}</h3>
        <div class="text-2xl p-3 bg-white w-auto rounded-xl flex justify-center items-center">
            <p>{{ info }}</p>
        </div>
    </div>
</template>