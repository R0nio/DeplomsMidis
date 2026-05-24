<script setup>
defineProps({
    modelValue: String,
    type: {
        type: String,
        default: 'text'
    },
    mask: String,
    placeholder: String,
    invalid: {
        type: Boolean,
        default: false,
    },
    describedBy: {
        type: String,
        default: null,
    },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div x-data="{ value: modelValue }">
        <input
            :type="type"
            :placeholder="placeholder"
            x-model="value"
            :x-mask="mask"
            class="mt-1 block w-full border-gray-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-600 rounded-md shadow-sm"
            :class="invalid ? 'ring-2 ring-red-600' : ''"
            :aria-invalid="invalid || undefined"
            :aria-describedby="describedBy || undefined"
            @input="$emit('update:modelValue', $event.target.value)"
        />
    </div>
</template>
