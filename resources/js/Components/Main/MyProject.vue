<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import slider3 from "../../../images/LogoInvestProject.png";

const props = defineProps({
    project: {
        type: Object,
        required: true
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

// Форматирование чисел
const formatNumber = (number) => {
    if (!number) return '0';
    return Number(number).toLocaleString('ru-RU');
};

// Перейти к проекту
const goToProject = () => {
    router.visit(route('projects.show', props.project.id));
};

// Редактировать проект
const editProject = (event) => {
    event.preventDefault();
    event.stopPropagation();
    router.visit(route('projects.edit', props.project.id));
};
// bgImage: '#4a7a6a',
</script>

<template>
    <div class="w-full h-[450px] sm:h-[500px] lg:h-[550px] flex flex-col rounded-xl relative overflow-hidden transition-shadow duration-300 group" :style="{ backgroundColor: '#4a7a6a', border: `2px solid #886830` }">
        <!-- Изображение проекта -->
        <div 
            @click="goToProject"
            @keydown.enter="goToProject"
            @keydown.space.prevent="goToProject"
            class="flex-1 cursor-pointer flex items-center justify-center overflow-hidden"
            role="button"
            tabindex="0"
            :aria-label="`Перейти к проекту ${project.title}`"
        >
            <img 
                :src="currentImage"
                @error="handleImageError"
                class="w-full h-full transition-transform duration-300 group-hover:scale-105"
                :class="isPlaceholder ? 'object-contain' : 'object-cover'"
                :style="isPlaceholder ? 'max-width: 100%; max-height: 100%; width: auto; height: auto;' : ''"
                :alt="`Изображение проекта ${project.title}`"
                loading="lazy"
            >
        </div>
        
        <!-- Верхняя панель (статус) -->
        <div class="absolute top-2 left-2 right-2 z-10">
            <div class="font-normal text-base sm:text-xl lg:text-2xl p-2 sm:p-2.5 text-black rounded-xl inline-flex items-center border-b-2 border-r-2 border-white" :style="{ backgroundColor: '#F8D794', border: `2px solid #886830` }">
                {{ project.status }}
            </div>
        </div>
        
        <!-- Нижняя панель с информацией -->
        <div class="rounded-t-xl border-t-2 flex-shrink-0" :style="{ backgroundColor: '#809076', borderColor: '#886830' }">
            <div class="p-3 sm:p-4 lg:p-5">
                <div class="grid grid-cols-1 gap-x-4 gap-y-2 text-lg sm:text-xl lg:text-xl">
                    <p class="truncate">
                        <span class="font-semibold" :style="{ color: '#F8D794' }">Название:</span>
                        <span class="sr-only"> - </span>
                        <span class="text-xs sm:text-lg" :style="{ color: '#ffffff' }">{{ project.title }}</span>
                    </p>
                    
                    <p v-if="project.number_date_realise" class="truncate">
                        <span class="font-semibold" :style="{ color: '#F8D794' }">Срок:</span>
                        <span class="sr-only"> - </span>
                        <span :style="{ color: '#ffffff' }">{{ project.number_date_realise }} мес.</span>
                    </p>
                    
                    <p v-if="project.total_investment" class="truncate">
                        <span class="font-semibold" :style="{ color: '#F8D794' }">Инвестиции:</span>
                        <span class="sr-only"> - </span>
                        <span :style="{ color: '#ffffff' }">{{ formatNumber(project.total_investment) }} ₽</span>
                    </p>
                    
                    <p v-if="project.type_build" class="truncate">
                        <span class="font-semibold" :style="{ color: '#F8D794' }">Собственность:</span>
                        <span class="sr-only"> - </span>
                        <span :style="{ color: '#ffffff' }">{{ project.type_build }}</span>
                    </p>
                </div>
                
                <!-- Кнопка редактирования -->
                <div v-if="project.status !== 'Отклонен' && project.status !== 'Заблокирован'" class="mt-3 sm:mt-4">
                    <button
                        @click="editProject"
                        class="w-full text-black text-sm sm:text-base lg:text-lg py-2 sm:py-2.5 lg:py-3 px-4 rounded-xl transition-all duration-200 font-medium flex items-center justify-center gap-2 hover:opacity-90"
                        :style="{ backgroundColor: '#F8D794', color: '#284139', border: `2px solid #886830` }"
                        :aria-label="`Редактировать проект ${project.title}`"
                    >
                        <span>Редактировать</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>