<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Link
        :href="href"
        class="block w-full p-3 px-6 mb-0 text-start text-base leading-5 text-gray-700 transition duration-150 ease-in-out border-b-2 hover:bg-[#267FBE] hover:text-white focus:text-black focus:bg-gray-400 focus:outline-none"
    >
        <slot />
    </Link>
</template>
