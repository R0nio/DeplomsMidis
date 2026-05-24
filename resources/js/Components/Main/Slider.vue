<script setup>
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

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

// Обработка ошибки загрузки изображения
const imageError = (event) => {
    event.target.src = '/images/free-icon-image-gallery-3342137.png';
};

// Проверка URL изображения
const getImageSrc = (src) => {
    if (!src || src === 'null' || src === 'undefined') {
        return '/images/free-icon-image-gallery-3342137.png';
    }
    return src;
};
</script>

<template>
    <section
        id="indicators-carousel"
        class="relative w-full "
        data-carousel="static"
        aria-roledescription="carousel"
        aria-label="Рекомендованные проекты"
    >
<!-- Carousel wrapper -->
        <div
            class="relative max-sm:h-[400px] sm:h-[500px] md:h-[600px] lg:h-[800px] xl:h-[1000px] overflow-hidden rounded-xl"
            aria-live="polite"
        >

            <div
                v-for="(photo, index) in photos"
                :key="index"
                class="hidden duration-700 ease-in-out"
                :data-carousel-item="index === 0 ? 'active' : ''"
                role="group"
                aria-roledescription="slide"
                :aria-label="`Слайд ${index + 1} из ${photos.length}: ${photo.title || ''}`"
                tabindex="0"
                @click="goToProject(photo.id)"
                @keydown.enter.prevent="goToProject(photo.id)"
                @keydown.space.prevent="goToProject(photo.id)"
            >
                <div
                    class="absolute inset-0 bg-cover bg-center"
                    :style="{ backgroundImage: `url(${getImageSrc(photo.src)})` }"
                    role="img"
                    :aria-label="photo.title || `Изображение проекта ${index + 1}`"
                ></div>
            </div>
        </div>
        <!-- Slider indicators -->
        <div class="absolute z-30 flex -translate-x-1/2 space-x-3 rtl:space-x-reverse bottom-5 left-1/2" role="tablist" aria-label="Индикаторы слайдов">
            <button
                v-for="(photo, index) in photos"
                :key="index"
                type="button"
                class="rounded-[4px] w-3 h-3 rounded-base focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-white"
                :aria-current="index === 0 ? 'true' : 'false'"
                :aria-label="`Перейти к слайду ${index + 1}`"
                :data-carousel-slide-to="index"
            ></button>
        </div>
        <!-- Slider controls -->
        <button type="button"
            class="absolute rounded-xl top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none hover:opacity-30 hover:bg-gray-800"
            data-carousel-prev
            aria-label="Предыдущий слайд">
            <span
                class="rounded-xl inline-flex items-center justify-center w-10 h-10 rounded-base bg-white/40 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none"
                aria-hidden="true">
                <svg class="w-7 h-7 text-white rtl:rotate-180" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"
                    width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m15 19-7-7 7-7" />
                </svg>
            </span>
        </button>
        <button type="button"
            class="absolute rounded-xl top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none hover:opacity-30 hover:bg-gray-800"
            data-carousel-next
            aria-label="Следующий слайд">
            <span
                class="rounded-xl inline-flex items-center justify-center w-10 h-10 rounded-base bg-white/40 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none"
                aria-hidden="true">
                <svg class="w-7 h-7 text-white rtl:rotate-180" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"
                    width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m9 5 7 7-7 7" />
                </svg>
            </span>
        </button>
    </section>
</template>
