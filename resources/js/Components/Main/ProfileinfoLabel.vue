<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
    info: {
        type: String,
        required: true
    }
})
</script>

<template>
    <div tabindex="0" class="focus:outline-none rounded-lg p-1">
        <h3 class="text-xl sm:text-2xl mb-1 font-semibold pl-3" :style="{ color: '#F8D794' }">{{ title }}</h3>
        <div 
            class="text-lg sm:text-xl lg:text-2xl p-3 w-auto rounded-xl flex justify-center items-center"
            :style="{ backgroundColor: '#284139', border: `2px solid #886830`, color: '#ffffff' }"
        >
            <p>{{ info }}</p>
        </div>
    </div>
</template>