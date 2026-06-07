<script setup>
defineProps({
    value: {
        type: String,
        required: true
    },
    id: {
        type: String,
        default: null
    },
    level: {
        type: String,
        default: 'h2',
        validator: (value) => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'].includes(value)
    }
});
</script>

<template>
    <component 
        :is="level"
        :id="id"
        class="text-2xl sm:text-3xl lg:text-4xl font-semibold leading-tight focus:outline-none   rounded-lg"
        style="color: #F8D794"
        tabindex="0"
    >
        {{ value }}
        <span v-if="id" class="sr-only">Начало раздела: {{ value }}</span>
    </component>
</template>

<style scoped>
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border-width: 0;
}
</style>