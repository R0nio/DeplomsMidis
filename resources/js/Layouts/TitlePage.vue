<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <h2 class="text-3xl font-semibold leading-tight text-gray-800" >
        {{value}}
    </h2>
</template>