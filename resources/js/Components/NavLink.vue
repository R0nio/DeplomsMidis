<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'inline-flex items-center text-center justify-center px-4 py-3 rounded-2xl max-sm:p-2 max-sm:w-full h-14 w-max border-2 bg-[#88B2FF] border-[#FFFFFF] text-lg font-medium leading-5 text-black focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-700 transition duration-700 ease-in-out'
        : 'inline-flex items-center text-center justify-center px-4 py-3 rounded-3xl max-sm:p-2 max-sm:w-full w-max h-max bg-[#EEEEEE] border-transparent text-base font-normal leading-5 text-black hover:text-[#267FBE] hover:border-2 hover:border-[#2D1DC0] focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-700 focus:text-gray-800 focus:text-medium focus:bg-[#88B2FF] focus:border-white transition duration-700 ease-in-out',
);
</script>

<template>
    <Link :href="href" :class="classes" :aria-current="active ? 'page' : undefined">
        <slot />
    </Link>
</template>
