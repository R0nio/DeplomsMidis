<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex justify-center items-center h-12 w-full rounded-xl border-none bg-[#267FBE] text-xl font-normal text-white hover:text-black focus:text-white hover:bg-blue-200 focus:bg-blue-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
    >
        <slot />
    </button>
</template>
