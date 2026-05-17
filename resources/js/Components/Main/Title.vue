<script setup>
defineProps({
   value: String 
});
</script>

<template>
    <div>
        <p class="text-3xl text-white border-b-2 pb-3 pl-2">{{ value }}</p>
    </div>
</template>