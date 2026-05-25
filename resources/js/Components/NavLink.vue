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

const classes = computed(() => {
    if (props.active) {
        return 'inline-flex items-center text-center justify-center px-5 py-3 rounded-xl max-sm:p-2 max-sm:w-full h-12 w-max border-2 border-[#886830] bg-white/30 text-white text-base font-medium leading-5 transition-all duration-300 ease-in-out';
    }
    return 'inline-flex items-center text-center justify-center px-5 py-3 rounded-xl max-sm:p-2 max-sm:w-full w-max h-12 border-2 border-[#886830] bg-transparent text-white/80 text-base font-normal leading-5 transition-all duration-300 ease-in-out hover:bg-white/10 hover:text-white';
});
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>