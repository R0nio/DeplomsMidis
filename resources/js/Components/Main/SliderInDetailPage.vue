<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import sliderFallback from "../../../images/LogoInvestProject.png";

const props = defineProps({
    photos: {
        type: Array,
        required: true
    }
});

const currentSlide = ref(0);
const autoPlayInterval = ref(null);
const sliderRef = ref(null);
const imageLoaded = ref({});
const imageDimensions = ref({});

// Переключение слайдов
const nextSlide = () => {
    if (props.photos.length > 0) {
        currentSlide.value = (currentSlide.value + 1) % props.photos.length;
    }
};

const prevSlide = () => {
    if (props.photos.length > 0) {
        currentSlide.value = (currentSlide.value - 1 + props.photos.length) % props.photos.length;
    }
};

const goToSlide = (index) => {
    currentSlide.value = index;
};

// Автопрокрутка
const startAutoPlay = () => {
    if (autoPlayInterval.value) clearInterval(autoPlayInterval.value);
    if (props.photos.length > 1) {
        autoPlayInterval.value = setInterval(() => {
            nextSlide();
        }, 5000);
    }
};

const stopAutoPlay = () => {
    if (autoPlayInterval.value) {
        clearInterval(autoPlayInterval.value);
        autoPlayInterval.value = null;
    }
};

// Обработка ошибок изображений
const imageError = ref({});

const handleImageError = (index) => {
    imageError.value[index] = true;
};

const handleImageLoad = (index, event) => {
    imageLoaded.value[index] = true;
    const img = event.target;
    // Сохраняем пропорции изображения
    imageDimensions.value[index] = {
        width: img.naturalWidth,
        height: img.naturalHeight,
        isPortrait: img.naturalHeight > img.naturalWidth
    };
};

const getImageSrc = (photo, index) => {
    if (imageError.value[index]) {
        return sliderFallback;
    }
    if (photo && photo.src) {
        return photo.src;
    }
    return sliderFallback;
};

// Проверка, является ли изображение заглушкой
const isPlaceholder = (photo, index) => {
    return !photo.src || photo.src === 'null' || photo.src === 'undefined' || imageError.value[index];
};

// Определяем класс для изображения в зависимости от пропорций
const getImageClass = (index) => {
    if (imageError.value[index]) return 'object-contain';
    if (!imageDimensions.value[index]) return 'object-cover';
    
    const dims = imageDimensions.value[index];
    // Если портретное изображение (выше, чем шире) или слишком вытянутое
    if (dims.isPortrait || (dims.height / dims.width > 1.2)) {
        return 'object-contain';
    }
    return 'object-cover';
};

// Стили для контейнера изображения
const getContainerStyle = (index) => {
    if (imageError.value[index] || !imageDimensions.value[index]) {
        return {};
    }
    
    const dims = imageDimensions.value[index];
    // Для портретных изображений центрируем и добавляем фон
    if (dims.isPortrait || (dims.height / dims.width > 1.2)) {
        return {
            backgroundColor: 'var(--color-brand)',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center'
        };
    }
    return {};
};

// Переход к проекту
const goToProject = (photoId) => {
    if (photoId) {
        router.visit(route('projects.show', photoId));
    }
};

// Обработка клавиатуры
const handleKeydown = (event) => {
    if (event.key === 'ArrowLeft') {
        prevSlide();
        stopAutoPlay();
        startAutoPlay();
    } else if (event.key === 'ArrowRight') {
        nextSlide();
        stopAutoPlay();
        startAutoPlay();
    }
};

onMounted(() => {
    startAutoPlay();
    window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    stopAutoPlay();
    window.removeEventListener('keydown', handleKeydown);
});
</script>

<template>
    <div 
        class="relative w-full group"
        @mouseenter="stopAutoPlay"
        @mouseleave="startAutoPlay"
        ref="sliderRef"
    >
        <!-- Слайды -->
        <div class="relative h-[400px] sm:h-[500px] md:h-[650px] lg:h-[750px] xl:h-[900px] overflow-hidden rounded-xl bg-gradient-to-br from-hover/10 to-hover/5">
            <div 
                v-for="(photo, index) in photos" 
                :key="index"
                class="absolute inset-0 transition-opacity duration-500 ease-in-out"
                :class="currentSlide === index ? 'opacity-100' : 'opacity-0 pointer-events-none'"
                :aria-hidden="currentSlide !== index"
            >
                <div 
                    class="w-full h-full cursor-pointer overflow-hidden"
                    :style="getContainerStyle(index)"
                    @click="goToProject(photo.id)"
                    @keydown.enter="goToProject(photo.id)"
                    @keydown.space.prevent="goToProject(photo.id)"
                    role="button"
                    tabindex="0"
                    :aria-label="`Перейти к проекту, фото ${index + 1} из ${photos.length}`"
                >
                    <!-- Для обычных фото -->
                    <img 
                        v-if="!isPlaceholder(photo, index)"
                        :src="getImageSrc(photo, index)"
                        :class="[
                            'w-full h-full transition-transform duration-300 group-hover:scale-105',
                            getImageClass(index)
                        ]"
                        :style="getImageClass(index) === 'object-contain' ? 'object-position: center;' : ''"
                        :alt="photo.alt || `Фото проекта ${index + 1}`"
                        loading="lazy"
                        @load="(event) => handleImageLoad(index, event)"
                        @error="() => handleImageError(index)"
                    >
                    <!-- Для заглушки (логотипа) -->
                    <div 
                        v-else
                        class="flex items-center justify-center w-full h-full"
                        :style="{ backgroundColor: 'var(--color-brand)' }"
                    >
                        <img 
                            :src="sliderFallback"
                            alt="Логотип проекта"
                            class="max-w-[40%] max-h-[40%] sm:max-w-[35%] sm:max-h-[35%] object-contain opacity-60"
                        >
                    </div>
                </div>
            </div>
        </div>

        <!-- Индикаторы (dots) -->
        <div v-if="photos.length > 1" class="absolute z-30 flex -translate-x-1/2 space-x-2 rtl:space-x-reverse bottom-4 left-1/2">
            <button 
                v-for="(photo, index) in photos" 
                :key="index"
                type="button" 
                class="rounded-full transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-accent"
                :class="index === currentSlide ? 'bg-white w-6 h-2' : 'bg-white/50 hover:bg-white/80 w-2 h-2'"
                :aria-current="index === currentSlide ? 'true' : 'false'"
                :aria-label="`Перейти к слайду ${index + 1}`"
                @click="goToSlide(index)"
                @keydown.enter="goToSlide(index)"
                tabindex="0"
            ></button>
        </div>

        <!-- Кнопка "Предыдущий" -->
        <button 
            v-if="photos.length > 1"
            type="button"
            class="absolute rounded-xl top-0 start-0 z-30 flex items-center justify-center h-full px-3 cursor-pointer opacity-0 group-hover:opacity-100 focus:opacity-100 transition-opacity duration-300"
            @click="prevSlide"
            @keydown.enter="prevSlide"
            aria-label="Предыдущий слайд"
            tabindex="0"
        >
            <span class="rounded-xl inline-flex items-center justify-center w-10 h-10 bg-black/40 hover:bg-black/60 transition-all duration-300 backdrop-blur-sm">
                <svg class="w-6 h-6 text-white rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7" />
                </svg>
            </span>
        </button>
        
        <!-- Кнопка "Следующий" -->
        <button 
            v-if="photos.length > 1"
            type="button"
            class="absolute rounded-xl top-0 end-0 z-30 flex items-center justify-center h-full px-3 cursor-pointer opacity-0 group-hover:opacity-100 focus:opacity-100 transition-opacity duration-300"
            @click="nextSlide"
            @keydown.enter="nextSlide"
            aria-label="Следующий слайд"
            tabindex="0"
        >
            <span class="rounded-xl inline-flex items-center justify-center w-10 h-10 bg-black/40 hover:bg-black/60 transition-all duration-300 backdrop-blur-sm">
                <svg class="w-6 h-6 text-white rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7" />
                </svg>
            </span>
        </button>
    </div>
</template>

<style scoped>
/* Анимация для слайдов */
.absolute {
    transition: opacity 0.5s ease-in-out;
}

/* Адаптивные кнопки на мобильных */
@media (max-width: 640px) {
    button span {
        width: 32px;
        height: 32px;
    }
    
    button svg {
        width: 20px;
        height: 20px;
    }
    
    .absolute {
        transition: opacity 0.3s ease-in-out;
    }
}

/* Для изображений с object-contain добавляем мягкий фон */
img[class*="object-contain"] {
    background-color: var(--color-brand);
}
</style>