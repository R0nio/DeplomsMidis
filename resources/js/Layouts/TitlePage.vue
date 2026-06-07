<script setup>
defineProps({
    value: {
        type: String,
        required: true
    },
    id: {
        type: String,
        default: null
    }
});
</script>

<template>
    <component 
        :is="id ? 'section' : 'div'"
        :id="id"
        class="title-page"
        role="heading"
        :aria-level="id ? 2 : 1"
        tabindex="-1"
    >
        <h1 class="title-text" :aria-label="`Заголовок раздела: ${value}`">
            {{ value }}
        </h1>
        <span v-if="id" class="sr-only">Начало раздела: {{ value }}</span>
    </component>
</template>

<style scoped>
.title-page {
    width: 100%;
}

.title-text {
    font-size: 1.25rem;
    color: #F8D794;
    margin: 0;
    padding: 0;
    position: relative;
    display: inline-block;
}

/* Адаптивные размеры */
@media (min-width: 640px) {
    .title-text {
        font-size: 1.5rem;
    }
}

@media (min-width: 1024px) {
    .title-text {
        font-size: 1.75rem;
    }
}

/* Для скринридеров */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
}
</style>