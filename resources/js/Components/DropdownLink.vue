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
        role="menuitem"
        class="block w-full p-3 px-6 mb-0 text-start text-base leading-5 text-gray-900 transition duration-150 ease-in-out border-b-2 hover:bg-[#267FBE] hover:text-white focus:text-black focus:bg-gray-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-blue-700"
    >
        <slot />
    </Link>
</template>
