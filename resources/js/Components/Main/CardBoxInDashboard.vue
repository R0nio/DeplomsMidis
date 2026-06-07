<script setup>
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import slider3 from "../../../images/LogoInvestProject.png";
import favoriteIcon from "../../../images/Favorite.png";
import favoriteActiveIcon from "../../../images/FavoriteActivity.png";

const colors = {
    bgPage: '#436343',
    bgLight: '#809076',
    bgDark: '#284139',
    bgImage: '#4a7a6a',
    accent: '#F8D794',
    border: '#886830',
    textLight: '#e8f0ee',
    textDark: '#111A19',
    white: '#ffffff',
    white80: 'rgba(255, 255, 255, 0.8)',
    white60: 'rgba(255, 255, 255, 0.6)',
};

const props = defineProps({
    project: {
        type: Object,
        required: true,
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

// Изображение
const imageError = ref(false);
const handleImageError = () => { imageError.value = true; };

const isPlaceholder = computed(() => currentImage.value === slider3 || imageError.value);

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

// Прогресс сбора
const progress = computed(() => {
    const collected = Number(props.project.collected_total_investment) || 0;
    const total = Number(props.project.total_investment) || 1;
    return (collected / total) * 100;
});

// Категории (первые 2)
const firstCategories = computed(() => {
    if (!props.project.category) return [];
    try {
        const cats = Array.isArray(props.project.category)
            ? props.project.category
            : JSON.parse(props.project.category);
        return cats.slice(0, 2);
    } catch {
        return [];
    }
});
</script>

<template>
    <article 
        class="h-full flex flex-col rounded-xl overflow-hidden transition-all duration-300 hover:shadow-xl group relative"
        :style="{ backgroundColor: colors.bgDark, border: `1px solid ${colors.border}` }"
        :aria-label="`Карточка проекта: ${project.title}`"
    >

        <!-- Изображение -->
        <div 
            @click="goToProject"
            @keydown.enter="goToProject"
            @keydown.space.prevent="goToProject"
            class="aspect-video w-full overflow-hidden cursor-pointer"
            role="button"
            tabindex="0"
            :aria-label="`Перейти к проекту ${project.title}`"
        >
            <img 
                :src="currentImage"
                @error="handleImageError"
                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                :alt="`Изображение проекта ${project.title}`"
                loading="lazy"
            >
        </div>

        <!-- Контент -->
        <div 
            class="p-4 flex flex-col gap-3 cursor-pointer"
            @click="goToProject"
            @keydown.enter="goToProject"
            @keydown.space.prevent="goToProject"
            role="button"
            tabindex="0"
            :aria-label="`Подробнее о проекте ${project.title}`"
        >
            <!-- Категории -->
            <div class="flex flex-wrap gap-2">
                <span 
                    v-for="cat in firstCategories" 
                    :key="cat"
                    class="px-2 py-0.5 text-xs font-medium rounded-full"
                    :style="{ backgroundColor: colors.bgLight, color: colors.white }"
                >
                    {{ cat }}
                </span>
            </div>

            <!-- Название -->
            <h3 class="font-semibold text-base line-clamp-2" :style="{ color: colors.white }">
                {{ project.title }}
            </h3>

            <!-- Адрес -->
            <p class="text-xs line-clamp-1" :style="{ color: colors.white80 }">
                {{ project.address || 'Адрес не указан' }}
            </p>

            <!-- Прогресс -->
            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span :style="{ color: colors.white80 }">Собрано</span>
                    <span class="font-semibold" :style="{ color: colors.accent }">{{ Math.min(progress, 100).toFixed(0) }}%</span>
                </div>
                <div class="w-full h-2 rounded-full overflow-hidden" :style="{ backgroundColor: colors.border }">
                    <div 
                        class="h-full rounded-full transition-all"
                        :style="{ width: `${Math.min(progress, 100)}%`, backgroundColor: colors.accent }"
                    ></div>
                </div>
            </div>

            <!-- Показатели -->
            <div class="grid grid-cols-2 gap-3 text-sm pt-1">
                <div>
                    <p class="text-xs" :style="{ color: colors.white80 }">Требуется</p>
                    <p class="font-semibold text-sm" :style="{ color: colors.white }">
                        {{ (Number(project.total_investment) / 1000000).toFixed(1) }} млн ₽
                    </p>
                </div>
                <div>
                    <p class="text-xs" :style="{ color: colors.white80 }">Рабочих мест</p>
                    <p class="font-semibold text-sm" :style="{ color: colors.white }">
                        {{ project.count_new_job || '—' }}
                    </p>
                </div>
            </div>
        </div>
    </article>
</template>