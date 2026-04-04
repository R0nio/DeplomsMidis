<script setup>
defineProps({
    modelValue: String,
    type: {
        type: String,
        default: 'text'
    },
    mask: String,
    placeholder: String
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
            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
            @input="$emit('update:modelValue', $event.target.value)"
        />
    </div>
</template>
