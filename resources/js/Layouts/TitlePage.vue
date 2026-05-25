<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <component 
        :is="level"
        :id="id"
        class="text-2xl sm:text-3xl lg:text-4xl font-semibold leading-tight focus:outline-none"
        style="color: #F8D794"
        tabindex="-1"
    >
        {{ value }}
        <span v-if="id" class="sr-only">Начало раздела: {{ value }}</span>
    </component>
</template>