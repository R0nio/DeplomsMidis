<script setup>
import { onMounted, ref, useAttrs } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

defineProps({
    invalid: {
        type: Boolean,
        default: false,
    },
    describedBy: {
        type: String,
        default: null,
    },
});

const input = ref(null);
const attrs = useAttrs();

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="rounded-2xl shadow-sm border-none focus:border-blue-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-600 bg-[#E8E8E8] h-12 px-3 text-black placeholder-gray-600 disabled:opacity-60 disabled:cursor-not-allowed"
        :class="invalid ? 'ring-2 ring-red-600' : ''"
        v-model="model"
        ref="input"
        :aria-invalid="invalid || undefined"
        :aria-describedby="describedBy || undefined"
        :aria-required="attrs.required !== undefined ? 'true' : undefined"
    />
</template>
