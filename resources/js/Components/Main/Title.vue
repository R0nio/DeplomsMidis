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
    <div>
        <p 
            :id="id"
            class="text-3xl text-white border-b-2 pb-3 pl-2 focus:outline-none"
            tabindex="-1"
            role="heading"
            aria-level="1"
        >
            {{ value }}
            <span v-if="id" class="sr-only">Начало раздела: {{ value }}</span>
        </p>
    </div>
</template>
