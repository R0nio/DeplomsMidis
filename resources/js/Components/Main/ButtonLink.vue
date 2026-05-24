<script setup>
defineProps({
    link:String,
    value:String,
});
</script>

<template>
    <div class="mt-6">
        <a
            :href="link"
            class="text-3xl bg-white p-3 rounded-xl inline-block text-black focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-700 hover:bg-blue-100"
        >
            {{ value }}
        </a>
    </div>
</template>
