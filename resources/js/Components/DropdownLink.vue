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
        class="block w-full p-3 px-6 mb-0 text-start text-base leading-5 transition-all duration-300 ease-in-out border-b border-[#886830] text-white/80 hover:bg-[#284139] hover:text-white focus:outline-none"
    >
        <slot />
    </Link>
</template>