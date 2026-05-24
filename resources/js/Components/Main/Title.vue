<script setup>
defineProps({
   value: String,
   level: {
       type: [String, Number],
       default: 2,
   },
});
</script>

<template>
    <component :is="`h${level}`" class="text-3xl text-white border-b-2 pb-3 pl-2">{{ value }}</component>
</template>
