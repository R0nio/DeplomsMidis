<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import slider3 from "../../../images/pictures/slider6.png";
import favoriteActiveIcon from "../../../images/FavoriteActivity.png";

const props = defineProps({
    project: {
        type: Object,
        required: true,
    }
});

// Для избранных всегда true
const isFavorite = ref(true);

// Удаление из избранного
const toggleFavorite = async (event) => {
    event.preventDefault();
    event.stopPropagation();

    if (!confirm('Удалить проект из избранного?')) {
        return;
    }

    try {
        const response = await axios.post(route('favorites.toggle', props.project.id));
        
        if (response.data.success) {
            // Перезагружаем страницу профиля
            router.reload();
        }
    } catch (error) {
        console.error('Ошибка при удалении из избранного:', error);
    }
};

// Получить первое фото проекта или заглушку
const projectImage = computed(() => {
    if (props.project.photos && props.project.photos.length > 0) {
        return `/storage/${props.project.photos[0].photo_path}`;
    }
    return slider3;
});

// Форматирование чисел
const formatNumber = (number) => {
    return Number(number).toLocaleString('ru-RU');
};

// Перейти к проекту
const goToProject = () => {
    router.visit(route('projects.show', props.project.id));
};
</script>

<template>
    <div class="w-full h-[450px] lg:h-[500px] flex flex-col justify-between bg-blue-300 rounded-xl relative overflow-hidden transition-shadow duration-300">
        <!-- Изображение проекта -->
        <div 
            @click="goToProject"
            class="w-full h-full cursor-pointer"
        >
            <img 
                :src="projectImage" 
                alt="Проект" 
                class="w-full h-full object-cover rounded-xl hover:scale-105 transition-transform duration-300"
            >
        </div>
        
        <!-- Верхняя панель (статус и избранное) -->
        <div class="w-full flex absolute top-2 left-2 right-2 z-10">
            <!-- Статус проекта -->
            <div class="font-normal text-xl lg:text-3xl p-2.5 bg-[#7390C5] text-white mr-auto rounded-xl flex items-center h-min border-b-2 border-r-2 border-white ">
                {{ project.status }}
            </div>
            
            <!-- Кнопка удаления из избранного -->
            <button 
                @click="toggleFavorite" 
                class="mt-2 mr-8 cursor-pointer hover:scale-110 transition-transform p-2 rounded-lg "
                title="Удалить из избранного"
            >
                <img 
                    :src="favoriteActiveIcon" 
                    alt="Убрать из избранного" 
                    class="w-8 h-8 lg:w-10 lg:h-10"
                >
            </button>
        </div>
        
        <!-- Нижняя панель с информацией -->
        <div class="bg-[#267FBE] w-full text-white px-4 lg:px-6 py-5 rounded-xl absolute bottom-0 border-t-2 border-white  backdrop-blur-sm">
            <div class="grid md:grid-cols-2 grid-cols-1 gap-2 text-sm lg:text-xl">
                <p><span class="font-semibold">Название:</span> {{ project.title }}</p>
                <p v-if="project.number_date_realise">
                    <span class="font-semibold">Срок:</span> {{ project.number_date_realise }} мес.
                </p>
                <p v-if="project.total_investment">
                    <span class="font-semibold">Инвестиции:</span> {{ formatNumber(project.total_investment) }} ₽
                </p>
                <p v-if="project.ownership">
                    <span class="font-semibold">Собственность:</span> {{ project.ownership }}
                </p>
            </div>
        </div>
    </div>
</template>