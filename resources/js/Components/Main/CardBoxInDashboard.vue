<script setup>
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import slider3 from "../../../images/LogoInvestProject.png";
import favoriteIcon from "../../../images/Favorite.png";
import favoriteActiveIcon from "../../../images/FavoriteActivity.png";

// Цветовая схема
const colors = {
    bgDark: '#284139',
    bgLight: '#809076',
    bgImage: '#4a7a6a',
    accent: '#F8D794',
    border: '#886830',
    white: '#ffffff',
    white80: 'rgba(255, 255, 255, 0.8)',
};

const props = defineProps({
    project: {
        type: Object,
        required: true
    },
    isFavorited: {
        type: [Boolean, Array],
        default: false
    }
});

const page = usePage();
const userRole = computed(() => page.props.auth?.user?.role ?? null);
const user = computed(() => page.props.auth?.user ?? null);

// Проверка избранного
const checkIsFavorited = () => {
    if (!props.isFavorited) return false;
    if (Array.isArray(props.isFavorited)) {
        return props.isFavorited.some(fav => fav?.project_id === props.project?.id || fav?.id === props.project?.id);
    }
    return Boolean(props.isFavorited);
};

const isFavorite = ref(checkIsFavorited());

// Переключение избранного
const toggleFavorite = async (event) => {
    event.preventDefault();
    event.stopPropagation();

    if (!user.value) {
        alert('Необходимо авторизоваться');
        return;
    }

    const previousState = isFavorite.value;
    isFavorite.value = !isFavorite.value;

    try {
        const response = await axios.post(route('favorites.toggle', props.project.id));
        if (response.data.success) {
            isFavorite.value = response.data.isFavorited;
        } else {
            isFavorite.value = previousState;
        }
    } catch (error) {
        console.error('Ошибка:', error);
        isFavorite.value = previousState;
    }
};

// Изображение
const imageError = ref(false);
const handleImageError = () => { imageError.value = true; };

const isPlaceholder = computed(() => {
    return currentImage.value === slider3 || imageError.value;
});

const currentImage = computed(() => {
    if (imageError.value) return slider3;
    if (props.project?.photos && props.project.photos.length > 0) {
        return `/storage/${props.project.photos[0].photo_path}`;
    }
    return slider3;
});

const formatNumber = (number) => {
    if (!number) return '0';
    return Number(number).toLocaleString('ru-RU');
};

const goToProject = () => {
    router.visit(route('projects.show', props.project.id));
};

</script>

<template>
    <article 
        class="w-full h-[450px] sm:h-[500px] lg:h-[550px] flex flex-col rounded-2xl relative overflow-hidden transition-all duration-300 group focus-within:ring-2 focus-within:ring-[#F8D794]"
        :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}` }"
        role="article"
        :aria-label="`Проект: ${project.title}`"
    >
        <!-- Изображение проекта -->
        <div 
            @click="goToProject"
            class="flex-1 cursor-pointer flex items-center justify-center overflow-hidden rounded-t-xl"
            :style="{ backgroundColor: colors.bgImage }"
            role="button"
            tabindex="0"
            :aria-label="`Перейти к проекту ${project.title}`"
            @keydown.enter="goToProject"
            @keydown.space.prevent="goToProject"
        >
            <img 
                :src="currentImage"
                @error="handleImageError"
                class="transition-transform duration-300 group-hover:scale-105"
                :class="isPlaceholder ? 'object-contain' : 'object-cover w-full h-full'"
                :style="isPlaceholder ? 'max-width: 100%; max-height: 100%; width: auto; height: auto;' : ''"
                :alt="`Изображение проекта ${project.title}`"
                loading="lazy"
            >
        </div>
        
        <!-- Нижняя панель с информацией -->
        <div class="rounded-b-xl flex-shrink-0" :style="{ backgroundColor: colors.bgLight }">
            <div class="p-3 sm:p-4">
                <!-- Информация о проекте -->
                <div class="grid grid-cols-1 gap-x-4 gap-y-2 text-lg sm:text-xl lg:text-xl">
                    <p class="truncate">
                        <span class="font-semibold" :style="{ color: colors.accent }">Название:</span>
                        <span class="sr-only"> - </span>
                        <span class="break-words" :style="{ color: colors.white }">{{ project.title }}</span>
                    </p>
                    
                    <p v-if="project.number_date_realise" class="truncate">
                        <span class="font-semibold" :style="{ color: colors.accent }">Срок:</span>
                        <span class="sr-only"> - </span>
                        <span :style="{ color: colors.white }">{{ project.number_date_realise }} мес.</span>
                    </p>
                    
                    <p v-if="project.total_investment" class="truncate">
                        <span class="font-semibold" :style="{ color: colors.accent }">Инвестиции:</span>
                        <span class="sr-only"> - </span>
                        <span :style="{ color: colors.white }">{{ formatNumber(project.total_investment) }} ₽</span>
                    </p>
                    
                    <p v-if="project.type_build" class="truncate">
                        <span class="font-semibold" :style="{ color: colors.accent }">Собственность:</span>
                        <span class="sr-only"> - </span>
                        <span :style="{ color: colors.white }">{{ project.type_build }}</span>
                    </p>
                </div>               
            </div>
        </div>
    </article>
</template>
