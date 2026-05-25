<script setup>
import { router } from '@inertiajs/vue3';

const props = defineProps({
    photos: {
        type: Array,
        required: true
    }
});

// Перейти к проекту
const goToProject = (photoId) => {
    if (photoId) {
        router.visit(route('projects.show', photoId));
    }
};

// Проверка URL изображения
const getImageSrc = (photo, index) => {
    if (imageErrors.value[index]) {
        return defaultImage;
    }
    
    if (!photo || !photo.src || photo.src === 'null' || photo.src === 'undefined') {
        return defaultImage;
    }
    return photo.src;
};
</script>

<template>
    <div id="indicators-carousel" class="relative w-full " data-carousel="static">
    <!-- Carousel wrapper -->
        <div class="relative max-sm:h-[400px] sm:h-[500px] md:h-[600px] lg:h-[800px] xl:h-[1000px] overflow-hidden rounded-xl bg-gray-100">
            <div 
                v-for="(photo, index) in photos" 
                :key="index"
                class="hidden duration-700 ease-in-out w-full h-full"
                :data-carousel-item="index === 0 ? 'active' : ''"
                @click="goToProject(photo.id)"
            >
                <div class="w-full h-full flex items-center justify-center">
                    <img 
                        :src="getImageSrc(photo, index)"
                        @error="() => handleImageError(index)"
                        class="w-full h-full rounded-xl transition-transform duration-300 hover:scale-105"
                        :class="imageErrors[index] || !photo.src || photo.src === 'null' || photo.src === 'undefined' ? 'object-contain' : 'object-cover'"
                        :style="imageErrors[index] || !photo.src || photo.src === 'null' || photo.src === 'undefined' ? 'width: auto; height: auto; max-width: 100%; max-height: 100%;' : ''"
                        :alt="`Фото проекта ${index + 1}`"
                    >
                </div>
            </div>
        </div>
        <!-- Slider indicators -->
        <div class="absolute z-30 flex -translate-x-1/2 space-x-3 rtl:space-x-reverse bottom-5 left-1/2">
            <button 
                v-for="(photo, index) in photos" 
                :key="index"
                type="button" 
                class="rounded-[4px] w-3 h-3 rounded-base"
                :aria-current="index === 0 ? 'true' : 'false'"
                :aria-label="`Slide ${index + 1}`"
                :data-carousel-slide-to="index"
            ></button>
        </div>
        <!-- Slider controls -->
        <button type="button"
            class="absolute rounded-xl top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none hover:opacity-30 hover:bg-gray-800"
            data-carousel-prev>
            <span
                class="rounded-xl inline-flex items-center justify-center w-10 h-10 rounded-base bg-white/40 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                <svg class="w-7 h-7 text-white rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m15 19-7-7 7-7" />
                </svg>
                <span class="sr-only">Предыдущая</span>
            </span>
        </button>
        <button type="button"
            class="absolute rounded-xl top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none hover:opacity-30 hover:bg-gray-800"
            data-carousel-next>
            <span
                class="rounded-xl inline-flex items-center justify-center w-10 h-10 rounded-base bg-white/40 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                <svg class="w-7 h-7 text-white rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m9 5 7 7-7 7" />
                </svg>
                <span class="sr-only">Следующая</span>
            </span>
        </button>
    </div>
</template>
