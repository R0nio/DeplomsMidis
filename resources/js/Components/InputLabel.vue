<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <label class="block text-xl ml-4 rounded-xl font-normal text-black">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
