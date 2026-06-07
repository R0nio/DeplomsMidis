<script setup>
/**
 * Компонент карточки категории
 * Используется для отображения категорий на главной странице
 * Принимает массив объектов с информацией о категориях
 */

const props = defineProps({
   infos: {
    type: Array,
    required: true
   },
});
</script>

<template>
    <div
        v-for="(item, index) in infos" 
        :key="index"
        class="px-6 py-3 flex items-center gap-3 rounded-xl focus:outline-none transition-all duration-300"
        :style="{ backgroundColor: '#284139', border: '2px solid #886830' }"
    >
        <span class="text-lg font-medium" :style="{ color: '#F8D794' }">
            {{ item.title || item.name }}
        </span>
    </div>
</template>
