<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <h2 class="text-xl font-semibold leading-tight text-black" >
        {{value}}
    </h2>
</template>