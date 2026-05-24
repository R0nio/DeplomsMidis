<script setup>
defineProps({
    value: {
        type: String,
    },
    for: {
        type: String,
        default: null,
    },
    required: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <label
        :for="$props.for"
        class="block text-xl ml-2 rounded-xl font-normal text-black"
    >
        <span v-if="value">{{ value }}<span v-if="required" class="text-red-700" aria-hidden="true">&nbsp;*</span></span>
        <span v-else><slot /><span v-if="required" class="text-red-700" aria-hidden="true">&nbsp;*</span></span>
        <span v-if="required" class="sr-only">обязательное поле</span>
    </label>
</template>
