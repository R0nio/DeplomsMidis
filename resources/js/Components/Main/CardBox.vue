<script setup>
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import slider3 from "../../../images/LogoInvestProject.png";
import favoriteIcon from "../../../images/Favorite.png";
import favoriteActiveIcon from "../../../images/FavoriteActivity.png";

// ===== ЦВЕТА И СТИЛИ КОМПОНЕНТА =====
const colors = {
    brand: 'var(--color-brand)',
    accent: 'var(--color-accent)',
    hover: 'var(--color-hover)',
    light: 'var(--color-light)',
    white: 'var(--color-white)',
    white70: 'rgba(255, 255, 255, 0.7)',
    white80: 'rgba(255, 255, 255, 0.8)',
    success: 'var(--color-success)',
    error: 'var(--color-error)',
    page: 'var(--color-page)',
};

const fonts = {
    heading: 'var(--font-heading)',
};

const transitions = {
    normal: 'var(--transition-normal)',
};

const shadows = {
    sm: 'var(--shadow-sm)',
    lg: 'var(--shadow-lg)',
};

const props = defineProps({
    project: { type: Object, required: true },
    isFavorited: { type: [Boolean, Array], default: false }
});

const page = usePage();
const userRole = computed(() => page.props.auth?.user?.role ?? null);
const user = computed(() => page.props.auth?.user ?? null);

const imageLoading = ref(true);
const imageError = ref(false);

const hasPhotos = computed(() => {
    return props.project.photos && props.project.photos.length > 0;
});

const getProjectImage = () => {
    if (imageError.value) return slider3;
    if (hasPhotos.value) {
        const photoPath = props.project.photos[0].photo_path;
        if (photoPath && photoPath.startsWith('/')) return photoPath;
        return `/storage/${photoPath}`;
    }
    return slider3;
};

const handleImageLoad = () => { imageLoading.value = false; };
const handleImageError = (event) => {
    imageError.value = true;
    imageLoading.value = false;
    event.target.src = slider3;
};

const checkIsFavorited = () => {
    if (!props.isFavorited) return false;
    if (Array.isArray(props.isFavorited)) return props.isFavorited.some(fav => fav?.project_id === props.project?.id || fav?.id === props.project?.id);
    return Boolean(props.isFavorited);
};
const isFavorite = ref(checkIsFavorited());

const toggleFavorite = async (event) => {
    event.preventDefault();
    event.stopPropagation();
    if (!user.value) { alert('Необходимо авторизоваться'); return; }
    const previousState = isFavorite.value;
    isFavorite.value = !isFavorite.value;
    try {
        const response = await axios.post(route('favorites.toggle', props.project.id));
        if (response.data.success) isFavorite.value = response.data.isFavorited;
        else isFavorite.value = previousState;
    } catch (error) {
        console.error('Ошибка:', error);
        isFavorite.value = previousState;
        if (error.response?.status === 401) alert('Необходимо авторизоваться');
    }
};

const formatNumber = (number) => {
    if (!number) return '0';
    return Number(number).toLocaleString('ru-RU');
};

const goToProject = () => { router.visit(route('projects.show', props.project.id)); };

const progress = computed(() => {
    const collected = Number(props.project.collected_total_investment) || 0;
    const total = Number(props.project.total_investment) || 1;
    return (collected / total) * 100;
});

const firstCategories = computed(() => {
    if (!props.project.category) return [];
    try {
        const cats = Array.isArray(props.project.category) ? props.project.category : JSON.parse(props.project.category);
        return cats.slice(0, 2);
    } catch { return []; }
});

const selectedStatus = ref(props.project.status);
const statusOptions = [
    { value: 'На модерации', label: 'На модерации' },
    { value: 'Одобрен', label: 'Одобрен' },
    { value: 'Отклонен', label: 'Отклонен' },
    { value: 'Заблокирован', label: 'Заблокирован' },
];

const changeStatus = async (event) => {
    event.preventDefault();
    event.stopPropagation();
    if (!confirm('Изменить статус проекта?')) { selectedStatus.value = props.project.status; return; }
    try {
        await axios.patch(route('admin.projects.update-status', props.project.id), { status: selectedStatus.value });
        await router.reload();
    } catch (error) { console.error('Ошибка при изменении статуса:', error); }
};
</script>

<template>
    <article 
        class="h-full flex flex-col rounded-xl overflow-hidden transition-all duration-300 group relative cursor-pointer"
        :style="{ 
            backgroundColor: colors.brand, 
            border: `1px solid ${colors.hover}`,
            transition: transitions.normal,
            boxShadow: shadows.sm
        }"
        @mouseenter="$event.currentTarget.style.boxShadow = shadows.lg"
        @mouseleave="$event.currentTarget.style.boxShadow = shadows.sm"
        @click="goToProject"
        @keydown.enter="goToProject"
        @keydown.space.prevent="goToProject"
        role="button"
        tabindex="0"
        :aria-label="`Карточка проекта: ${project.title}`">
        
        <!-- Панель администратора -->
        <div v-if="userRole === 'Admin'" class="absolute top-2 right-2 z-20 flex items-center gap-2">
            <div v-if="project.is_moderated === true" class="px-2 py-1 rounded-lg text-base text-center whitespace-nowrap" :style="{ backgroundColor: colors.success, color: colors.white }">Прошла модерацию</div>
            <div v-else-if="project.is_moderated === false" class="px-2 py-1 rounded-lg text-base text-center whitespace-nowrap" :style="{ backgroundColor: colors.error, color: colors.white }">Не прошла модерацию</div>
            <div v-else class="px-2 py-1 rounded-lg text-base text-center whitespace-nowrap" :style="{ backgroundColor: colors.light, color: colors.brand }">Ожидает модерации</div>
            <select v-model="selectedStatus" @change="changeStatus" class="px-2 py-1 rounded-lg text-base focus:outline-none cursor-pointer" :style="{ backgroundColor: colors.light, color: colors.brand, border: `1px solid ${colors.hover}` }" @click.stop aria-label="Изменить статус проекта">
                <option disabled value="">Статус</option>
                <option v-for="option in statusOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
            </select>
        </div>

        <!-- Кнопка избранного -->
        <div v-if="user && userRole === 'Investor'" class="absolute top-2 right-2 z-20">
            <button @click.stop="toggleFavorite" class="cursor-pointer p-1.5 sm:p-2 rounded-full transition-all hover:scale-110 shadow-sm" :style="{ backgroundColor: colors.brand, border: `1px solid ${colors.hover}` }" :title="isFavorite ? 'Удалить из избранного' : 'Добавить в избранное'" :aria-label="isFavorite ? 'Удалить из избранного' : 'Добавить в избранное'">
                <img :src="isFavorite ? favoriteActiveIcon : favoriteIcon" alt="" class="w-4 h-4 sm:w-5 sm:h-5" aria-hidden="true">
            </button>
        </div>

        <!-- Изображение -->
        <div class="relative aspect-video w-full overflow-hidden" :style="{ backgroundImage: `linear-gradient(to bottom right, ${colors.hover}30, ${colors.hover}10)` }">
            <div v-if="imageLoading && !imageError && hasPhotos" class="absolute inset-0 flex items-center justify-center z-10" :style="{ backgroundColor: colors.brand + '80' }">
                <svg class="animate-spin w-6 h-6" :style="{ color: colors.accent }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            
            <img 
                v-if="hasPhotos && !imageError"
                :src="getProjectImage()" 
                :alt="`Изображение проекта ${project.title}`"
                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                loading="lazy"
                @load="handleImageLoad"
                @error="handleImageError"
            >
            
            <div v-if="!hasPhotos || imageError" class="absolute inset-0 flex items-center justify-center" :style="{ backgroundColor: colors.hover }">
                <img :src="slider3" alt="Логотип проекта" class="w-[60%] h-[60%] object-contain opacity-60">
            </div>
        </div>

        <!-- Контент -->
        <div class="p-3 sm:p-4 flex flex-col gap-2">
            <div class="flex flex-wrap gap-1">
                <span v-for="cat in firstCategories" :key="cat" class="px-2 py-0.5 text-base font-medium rounded-full" :style="{ backgroundColor: colors.accent, color: colors.white }">{{ cat }}</span>
            </div>
            <h3 class="font-semibold text-base sm:text-base line-clamp-2" :style="{ color: colors.white, fontFamily: fonts.heading }">{{ project.title }}</h3>
            <p class="text-base line-clamp-1" :style="{ color: colors.white80 }">{{ project.address || 'Адрес не указан' }}</p>
            <div class="mt-1">
                <div class="flex justify-between text-base mb-1"><span :style="{ color: colors.white80 }">Собрано</span><span class="font-semibold" :style="{ color: colors.page }">{{ Math.min(progress, 100).toFixed(0) }}%</span></div>
                <div class="w-full h-1.5 rounded-full overflow-hidden" :style="{ backgroundColor: colors.page }"><div class="h-full rounded-full transition-all" :style="{ width: `${Math.min(progress, 100)}%`, backgroundColor: colors.accent }"></div></div>
            </div>
            <div class="grid grid-cols-2 gap-2 text-base pt-1">
                <div><p class="text-base" :style="{ color: colors.white80 }">Требуется</p><p class="font-semibold text-base" :style="{ color: colors.white }">{{ (Number(project.total_investment) / 1000000).toFixed(1) }} млн ₽</p></div>
                <div><p class="text-base" :style="{ color: colors.white80 }">Рабочих мест</p><p class="font-semibold text-base" :style="{ color: colors.white }">{{ project.count_new_job || '—' }}</p></div>
            </div>
        </div>
    </article>
</template>