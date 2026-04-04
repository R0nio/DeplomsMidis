<script setup>
defineProps({
    link:String,
    value:String,
});
</script>

<template>
    <div class="mt-6">
        <a :href="link" class="text-3xl bg-white p-3 rounded-xl">
            {{ value }}
        </a>
    </div>
</template>