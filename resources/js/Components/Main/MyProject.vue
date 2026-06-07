<script setup>
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import slider3 from "../../../images/LogoInvestProject.png";

// ===== ЦВЕТА И СТИЛИ КОМПОНЕНТА =====
const colors = {
    brand: 'var(--color-brand)',
    brandDark: 'var(--color-brand-dark)',
    accent: 'var(--color-accent)',
    hover: 'var(--color-hover)',
    white: 'var(--color-white)',
    page: 'var(--color-page)',
    white70: 'rgba(255, 255, 255, 0.7)',
    white60: 'rgba(255, 255, 255, 0.6)',
};

const fonts = {
    heading: 'var(--font-heading)',
};

const transitions = {
    normal: 'var(--transition-normal)',
};

const props = defineProps({
    project: {
        type: Object,
        required: true
    }
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

const formatNumber = (number) => {
    if (!number) return '0';
    return Number(number).toLocaleString('ru-RU');
};

const goToProject = () => {
    router.visit(route('projects.show', props.project.id));
};

const editProject = (event) => {
    event.preventDefault();
    event.stopPropagation();
    router.visit(route('projects.edit', props.project.id));
};

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
</script>

<template>
    <article 
        class="h-full flex flex-col rounded-xl overflow-hidden transition-all duration-300 hover:shadow-xl group relative cursor-pointer"
        :style="{ backgroundColor: colors.brand, border: `1px solid ${colors.accent}`, transition: transitions.normal }"
        @click="goToProject"
        @keydown.enter="goToProject"
        @keydown.space.prevent="goToProject"
        role="button"
        tabindex="0"
        :aria-label="`Карточка проекта: ${project.title}. Нажмите для подробностей`"
    >
        <!-- Бейдж статуса -->
        <div class="absolute top-3 left-3 z-10" @click.stop>
            <div class="font-normal text-base px-3 py-2 rounded-full" :style="{ backgroundColor: colors.accent, color: colors.white }">{{ project.status }}</div>
        </div>

        <!-- Блок с изображением -->
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
        <div class="p-4 flex flex-col gap-3">
            <div class="flex flex-wrap gap-2">
                <span v-for="cat in firstCategories" :key="cat" class="px-2 py-0.5 text-base font-medium rounded-full" :style="{ backgroundColor: colors.accent, color: colors.white }">{{ cat }}</span>
            </div>
            <h3 class="font-heading font-semibold text-base line-clamp-2" :style="{ color: colors.white, fontFamily: fonts.heading }">{{ project.title }}</h3>
            <p class="text-base line-clamp-1" :style="{ color: colors.white }">{{ project.address || 'Адрес не указан' }}</p>
            <div>
                <div class="flex justify-between text-base mb-1"><span :style="{ color: colors.white }">Собрано</span><span class="font-semibold" :style="{ color: colors.accent }">{{ Math.min(progress, 100).toFixed(0) }}%</span></div>
                <div class="w-full h-2 rounded-full overflow-hidden" :style="{ backgroundColor: colors.page }"><div class="h-full rounded-full transition-all" :style="{ width: `${Math.min(progress, 100)}%`, backgroundColor: colors.accent }"></div></div>
            </div>
            <div class="grid grid-cols-2 gap-3 text-base pt-1">
                <div><p class="text-base" :style="{ color: colors.white }">Требуется</p><p class="font-semibold text-base" :style="{ color: colors.white }">{{ (Number(project.total_investment) / 1000000).toFixed(1) }} млн ₽</p></div>
                <div><p class="text-base" :style="{ color: colors.white }">Рабочих мест</p><p class="font-semibold text-base" :style="{ color: colors.white }">{{ project.count_new_job || '—' }}</p></div>
            </div>
            <!-- Кнопка редактирования -->
            <div v-if="project.status !== 'Отклонен' && project.status !== 'Заблокирован'" class="mt-2" @click.stop>
                <button 
                    @click="editProject" 
                    class="w-full text-base py-2 rounded-xl transition-all duration-200 font-medium hover:opacity-90"
                    :style="{ backgroundColor: colors.accent, color: colors.white, border: `1px solid ${colors.accent}` }"
                    :aria-label="`Редактировать проект ${project.title}`"
                >
                    Редактировать
                </button>
            </div>
        </div>
    </article>
</template>