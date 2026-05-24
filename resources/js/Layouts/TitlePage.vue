<script setup>
defineProps({
    value: {
        type: String,
    },
    level: {
        type: [String, Number],
        default: 1,
    },
});
</script>

<template>
    <component :is="`h${level}`" class="text-3xl font-semibold leading-tight text-gray-900">
        {{ value }}
    </component>
</template>
