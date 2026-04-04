<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="rounded-md shadow-sm border-none focus:border-blue-200 focus:ring-blue-200  bg-[#E8E8E8] h-12"
        v-model="model"
        ref="input"
    />
</template>
