<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import slider3 from "../../../images/LogoInvestProject.png";
import favoriteActiveIcon from "../../../images/FavoriteActivity.png";

const props = defineProps({
    project: {
        type: Object,
        required: true,
    }
});

// Состояние ошибки загрузки изображения
const imageError = ref(false);

// Проверка, является ли изображение заглушкой
const isPlaceholder = computed(() => {
    return currentImage.value === slider3 || imageError.value;
});

// Текущее изображение с обработкой ошибок
const currentImage = computed(() => {
    if (imageError.value) {
        return slider3;
    }
    
    if (props.project.photos && props.project.photos.length > 0) {
        return `/storage/${props.project.photos[0].photo_path}`;
    }
    
    return slider3;
});

// Обработчик ошибки загрузки
const handleImageError = () => {
    imageError.value = true;
};

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

// Форматирование чисел
const formatNumber = (number) => {
    if (!number) return '0';
    return Number(number).toLocaleString('ru-RU');
};

// Перейти к проекту
const goToProject = () => {
    router.visit(route('projects.show', props.project.id));
};
</script>

<template>
    <div class="w-full h-[450px] sm:h-[500px] lg:h-[550px] flex flex-col rounded-xl relative overflow-hidden transition-shadow duration-300 group" :style="{ backgroundColor: '#4a7a6a', border: `2px solid #886830` }">
        <!-- Изображение проекта -->
        <div 
            @click="goToProject"
            class="flex-1 cursor-pointer flex items-center justify-center overflow-hidden"
        >
            <img 
                :src="currentImage"
                @error="handleImageError"
                class="transition-transform duration-300 group-hover:scale-105"
                :class="isPlaceholder ? 'object-contain w-auto h-auto max-w-full max-h-full' : 'object-cover w-full h-full'"
                :style="isPlaceholder ? 'width: auto; height: auto; max-width: 100%; max-height: 100%;' : ''"
                alt="Проект"
                loading="lazy"
            >
        </div>
        
        <!-- Верхняя панель (статус и избранное) -->
        <div class="absolute top-2 left-2 right-2 z-10 flex justify-between items-start gap-2">
            <!-- Статус проекта -->
            <div class="font-normal text-sm sm:text-base lg:text-xl xl:text-2xl p-1.5 sm:p-2 lg:p-2.5 text-black rounded-xl flex items-center h-min max-w-[60%]">
                <!-- <p class="truncate">{{ project.status }}</p> -->
            </div>
            
            <!-- Кнопка удаления из избранного -->
            <button 
                @click="toggleFavorite" 
                class="cursor-pointer hover:scale-110 transition-transform p-1.5 sm:p-2 rounded-lg"
                title="Удалить из избранного"
            >
                <img 
                    :src="favoriteActiveIcon" 
                    alt="Убрать из избранного"  
                    class="w-6 h-6 sm:w-7 sm:h-7 lg:w-8 lg:h-8 xl:w-10 xl:h-10"
                >
            </button>
        </div>
        
        <!-- Нижняя панель с информацией -->
        <div class="rounded-t-xl border-t-2 flex-shrink-0" :style="{ backgroundColor: '#809076', borderColor: '#886830' }">
            <div class="p-3 sm:p-4 lg:p-5">
                <!-- Информация в 2 колонки на планшетах, в 1 на мобильных -->
                <div class="flex flex-col justify-between overflow-x-auto gap-x-3 gap-y-1.5 text-lg sm:text-xl lg:text-xl w-full">
                    <p class="truncate">
                        <span class="font-semibold block sm:inline" :style="{ color: '#F8D794' }">Название: </span>
                        <span class="text-xl sm:text-xl" :style="{ color: '#ffffff' }">{{ project.title }}</span>
                    </p>
                    
                    <p v-if="project.number_date_realise" class="truncate">
                        <span class="font-semibold block sm:inline" :style="{ color: '#F8D794' }">Срок: </span>
                        <span class="break-words" :style="{ color: '#ffffff' }">{{ project.number_date_realise }} мес.</span>
                    </p>
                    
                    <p v-if="project.total_investment" class="truncate">
                        <span class="font-semibold block sm:inline" :style="{ color: '#F8D794' }">Инвестиции: </span>
                        <span class="break-words" :style="{ color: '#ffffff' }">{{ formatNumber(project.total_investment) }} ₽</span>
                    </p>
                    
                    <p v-if="project.type_build || project.ownership" class="truncate">
                        <span class="font-semibold block sm:inline" :style="{ color: '#F8D794' }">Собственность: </span>
                        <span class="break-words" :style="{ color: '#ffffff' }">{{ project.type_build || project.ownership }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>