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
    success: 'var(--color-success)',
    error: 'var(--color-error)',
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

// Категории: первые 2 + счётчик
const displayCategories = computed(() => {
    if (!props.project.category) return [];
    try {
        const cats = Array.isArray(props.project.category) 
            ? props.project.category 
            : JSON.parse(props.project.category);
        
        if (cats.length === 0) return [];
        
        // Если категорий 2 или меньше - показываем все
        if (cats.length <= 2) return cats;
        
        // Если больше 2 - показываем первые 2 + счётчик
        return [
            ...cats.slice(0, 2),
            `+${cats.length - 2}`
        ];
    } catch { 
        return []; 
    }
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
        class="rounded-xl overflow-hidden transition-all duration-300 hover:shadow-xl group relative cursor-pointer flex flex-col"
        :style="{ backgroundColor: colors.brand, border: `1px solid ${colors.accent}`, transition: transitions.normal, height: '100%', minHeight: '440px' }"
        @click="goToProject"
        @keydown.enter="goToProject"
        @keydown.space.prevent="goToProject"
        role="button"
        tabindex="0"
        :aria-label="`Карточка проекта: ${project.title}. Нажмите для подробностей`"
    >
    
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

        <!-- Блок с изображением -->
        <div class="relative aspect-video w-full overflow-hidden flex-shrink-0" :style="{ backgroundImage: `linear-gradient(to bottom right, ${colors.hover}30, ${colors.hover}10)` }">
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

        <!-- Контент с фиксированной минимальной высотой -->
        <div class="p-4 flex flex-col flex-grow" style="min-height: 200px;">
            
            <!-- Категории -->
            <div class="flex flex-wrap gap-2 min-h-[36px]">
                <span 
                    v-for="cat in displayCategories" 
                    :key="cat" 
                    class="px-2 h-max py-1 text-base font-medium rounded-full max-w-[170px]  max-2xl:max-w-[130px] truncate"
                    :style="{ backgroundColor: colors.accent, color: colors.white }"
                    :title="cat"
                >{{ cat }}</span>
            </div>
            
            <!-- Заголовок -->
            <h3 class="font-heading font-semibold text-base line-clamp-2 truncate" style="min-height: 48px;" :style="{ color: colors.white, fontFamily: fonts.heading }">{{ project.title }}</h3>
            
            <!-- Адрес -->
            <p class="text-base line-clamp-1" style="min-height: 24px;" :style="{ color: colors.white }">{{ project.address || 'Адрес не указан' }}</p>
            
            <!-- Прогресс сбора -->
            <div class="mt-auto pt-2">
                <div class="flex justify-between text-base mb-1">
                    <span :style="{ color: colors.white }">Собрано</span>
                    <span class="font-semibold" :style="{ color: colors.accent }">{{ Math.min(progress, 100).toFixed(0) }}%</span>
                </div>
                <div class="w-full h-2 rounded-full overflow-hidden" :style="{ backgroundColor: colors.page }">
                    <div class="h-full rounded-full transition-all" :style="{ width: `${Math.min(progress, 100)}%`, backgroundColor: colors.accent }"></div>
                </div>
            </div>
            
            <!-- Инвестиции и рабочие места -->
            <div class="grid grid-cols-2 gap-3 text-base mt-2">
                <div>
                    <p class="text-base" :style="{ color: colors.white }">Требуется</p>
                    <p class="font-semibold text-base truncate" :style="{ color: colors.white }">{{ (Number(project.total_investment) / 1000000).toFixed(1) }} млн ₽</p>
                </div>
                <div>
                    <p class="text-base" :style="{ color: colors.white }">Рабочих мест</p>
                    <p class="font-semibold text-base" :style="{ color: colors.white }">{{ project.count_new_job || '—' }}</p>
                </div>
            </div>
            
            <!-- Кнопка редактирования -->
            <div v-if="project.status !== 'Отклонен' && project.status !== 'Заблокирован'" class="mt-2 pt-1" @click.stop>
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