<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import CardInMainPage from "@/Components/Main/CardInMainPage.vue";
import CardCategories from "@/Components/Main/CardCategories.vue";
import TitlePage from "@/Layouts/TitlePage.vue";
import Title from "@/Components/Main/Title.vue";
import { computed } from 'vue';
import CardBoxInDashboard from "@/Components/Main/CardBoxInDashboard.vue";
import { ref, onMounted, onUnmounted } from 'vue';
// ===== ЦВЕТА И СТИЛИ КОМПОНЕНТА =====
const colors = {
    page: 'var(--color-page)',
    light: 'var(--color-light)',
    hover: 'var(--color-hover)',
    brand: 'var(--color-brand)',
};

const props = defineProps({
    auth: Object,
    statistics: {
        type: Object,
        required: true
    },
    categories: {
        type: Array,
        default: () => []
    },
    randomProjects: {
        type: Array,
        default: () => []
    },
    favoriteProjects: {
        type: Array,
        default: () => []
    }
});

const formatNumber = (number) => {
    return Number(number).toLocaleString('ru-RU');
};

const formatMillions = (number) => {
    const num = Number(number) || 0;
    if (num >= 1000000000) {
        return `${(num / 1000000000).toFixed(1)} млрд`;
    } else if (num >= 1000000) {
        return `${(num / 1000000).toFixed(1)} млн`;
    } else if (num >= 1000) {
        return `${(num / 1000).toFixed(0)} тыс`;
    }
    return formatNumber(num);
};

const handleCategoryClick = (category) => {
    sessionStorage.setItem('selectedCategory', JSON.stringify({
        id: category.id,
        name: category.name || category.title
    }));
    router.visit(route('projects'), {
        preserveState: false,
        preserveScroll: false,
    });
};

const cardInfoProjects = computed(() => [
    {
        title: "Выбрали нас", 
        info: `${formatNumber(props.statistics?.total_projects || 0)}+ проектов`,
    },
    {
        title: "Уже собрали", 
        info: `${formatMillions(props.statistics?.total_investment || 0)} рублей`,
    },
    {
        title: "Создано рабочих мест", 
        info: `${formatNumber(props.statistics?.total_jobs || 0)} человек`,
    },
]);

const scrollContainer = ref(null);
const showLeftArrow = ref(false);
const showRightArrow = ref(true);

const checkScroll = () => {
    if (!scrollContainer.value) return;
    const { scrollLeft, scrollWidth, clientWidth } = scrollContainer.value;
    showLeftArrow.value = scrollLeft > 10;
    showRightArrow.value = scrollLeft + clientWidth < scrollWidth - 10;
};

const scrollLeft = () => {
    if (scrollContainer.value) {
        scrollContainer.value.scrollBy({ left: -300, behavior: 'smooth' });
    }
};

const scrollRight = () => {
    if (scrollContainer.value) {
        scrollContainer.value.scrollBy({ left: 300, behavior: 'smooth' });
    }
};

const handleResize = () => {
    setTimeout(checkScroll, 100);
};

onMounted(() => {
    checkScroll();
    window.addEventListener('resize', handleResize);
});

onUnmounted(() => {
    window.removeEventListener('resize', handleResize);
});
</script>

<template>
    <Head title="Главная">
        <meta name="description" content="Инвестиционная платформа для поиска и финансирования проектов">
    </Head>

    <AuthenticatedLayout>
        <template #header>
            <TitlePage id="Main" value="Главная" />
        </template>
        
        <div class="mx-auto py-6 px-4 sm:px-10 lg:px-16 min-h-screen" :style="{ backgroundColor: colors.page }">
            <!-- Информация о проектах -->
            <section aria-labelledby="projects-info-title" class="mb-12">
                <Title id="projects-info-title" value="Информация о проектах" />
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mt-8 sm:grid-cols-2">
                    <CardInMainPage :infos="cardInfoProjects" />
                </div>
            </section>

            <!-- Категории -->
            <section aria-labelledby="categories-title" class="mb-12">
                <Title id="categories-title" value="Категории" />
                    <div 
                        v-if="categories && categories.length > 0" 
                        class="mt-8 w-full relative"
                        role="list"
                        aria-label="Список категорий проектов"
                    >
                        <div class="hidden md:flex flex-wrap gap-3">
                            <div 
                                v-for="category in categories" 
                                :key="category.id"
                                class="cursor-pointer"
                                @click="handleCategoryClick(category)"
                            >
                                <CardCategories :infos="[category]" />
                            </div>
                        </div>
                        <div class="md:hidden relative flex items-center gap-2">
                            <!-- Кнопка влево -->
                            <button 
                                v-if="showLeftArrow"
                                @click="scrollLeft"
                                class="absolute -left-6 top-1/2 -translate-y-1/2 z-20 w-14 h-28 rounded-2xl flex items-center justify-center shadow-xl transition-all duration-300 hover:scale-105 active:scale-95 backdrop-blur-sm group"
                                :style="{ 
                                    backgroundColor: colors.white,
                                    color: colors.brand,
                                    border: `2px solid ${colors.brand}`,
                                    boxShadow: `0 4px 15px ${colors.brand}40`
                                }"
                                @mouseenter="$event.currentTarget.style.backgroundColor = colors.brand; $event.currentTarget.style.color = colors.white"
                                @mouseleave="$event.currentTarget.style.backgroundColor = colors.white; $event.currentTarget.style.color = colors.brand"
                                aria-label="Прокрутить категории влево"
                            >
                                <svg class="w-8 h-8 transition-transform duration-300 group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>

                            <!-- Контейнер с категориями -->
                            <div 
                                ref="scrollContainer"
                                class="overflow-x-auto pb-4 scrollbar-thin scrollbar-thumb-brand scrollbar-track-gray-200"
                                @scroll="checkScroll"
                            >
                                <div class="grid grid-rows-2 grid-flow-col gap-3 min-w-max px-4">
                                    <div 
                                        v-for="category in categories" 
                                        :key="category.id"
                                        class="cursor-pointer"
                                        @click="handleCategoryClick(category)"
                                    >
                                        <CardCategories :infos="[category]" />
                                    </div>
                                </div>
                            </div>

                            <!-- Кнопка вправо -->
                            <button 
                                v-if="showRightArrow"
                                @click="scrollRight"
                                class="absolute -right-6 top-1/2 -translate-y-1/2 z-20 w-14 h-28 rounded-2xl flex items-center justify-center shadow-xl transition-all duration-300 hover:scale-105 active:scale-95 backdrop-blur-sm group"
                                :style="{ 
                                    backgroundColor: colors.white,
                                    color: colors.brand,
                                    border: `2px solid ${colors.brand}`,
                                    boxShadow: `0 4px 15px ${colors.brand}40`
                                }"
                                @mouseenter="$event.currentTarget.style.backgroundColor = colors.brand; $event.currentTarget.style.color = colors.white"
                                @mouseleave="$event.currentTarget.style.backgroundColor = colors.white; $event.currentTarget.style.color = colors.brand"
                                aria-label="Прокрутить категории вправо"
                            >
                                <svg class="w-8 h-8 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                <div 
                    v-else 
                    class="text-center py-8 rounded-xl"
                    :style="{ backgroundColor: colors.light, border: `1px solid ${colors.hover}` }"
                    role="status" 
                    aria-label="Категории не найдены"
                >
                    <p :style="{ color: colors.brand }">Категории появятся после добавления проектов</p>
                </div>
            </section>

            <!-- Рекомендуемые проекты -->
            <section aria-labelledby="recommended-projects-title">
                <Title id="recommended-projects-title" class="mb-6" value="Вам может понравиться" />
                <div 
                    v-if="randomProjects && randomProjects.length > 0" 
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                    role="list"
                    aria-label="Рекомендуемые проекты"
                >
                    <CardBoxInDashboard 
                        v-for="project in randomProjects" 
                        :key="project.id"
                        :project="project" 
                        :isFavorited="favoriteProjects"
                        role="listitem"
                    />
                </div>
                <div 
                    v-else 
                    class="text-center py-12 rounded-xl mb-[120px]"
                    :style="{ backgroundColor: colors.light, border: `1px solid ${colors.hover}` }"
                    role="status" 
                    aria-label="Проекты не найдены"
                >
                    <p :style="{ color: colors.brand, fontSize: 'var(--text-xl)' }">Проекты для отображения появятся скоро</p>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>