<script setup>
defineProps({
    message: {
        type: String,
    },
    id: {
        type: String,
        default: null,
    },
});
</script>

<template>
    <div v-show="message" :id="id" role="alert" aria-live="polite">
        <p class="text-sm text-red-700 font-medium">
            {{ message }}
        </p>
    </div>
</template>
