<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-semibold leading-tight" style="color: #F8D794">
        {{ value }}
    </h2>
</template>