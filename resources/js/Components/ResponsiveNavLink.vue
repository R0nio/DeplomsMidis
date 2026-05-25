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

// Ваши цвета
const accentColor = "#F8D794";
const borderColor = "#886830";
const bgDark = "#284139";
const bgLight = "#809076";

const classes = computed(() => {
    if (props.active) {
        return `block w-full px-4 py-3 rounded-xl border-2 bg-white/30 border-[${borderColor}] text-white text-base font-medium leading-5 transition-all duration-300 ease-in-out`;
    }
    return `block w-full px-4 py-3 rounded-xl border-2 border-[${borderColor}] bg-transparent text-white/80 text-base font-normal leading-5 transition-all duration-300 ease-in-out hover:bg-white/10 hover:text-white`;
});
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>