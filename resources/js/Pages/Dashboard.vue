<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import CardInMainPage from "@/Components/Main/CardInMainPage.vue";
import CardCategories from "@/Components/Main/CardCategories.vue";
import TitlePage from "@/Layouts/TitlePage.vue";
import Title from "@/Components/Main/Title.vue";
import { computed } from 'vue';
import CardBoxInDashboard from "@/Components/Main/CardBoxInDashboard.vue";

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
                    class="flex flex-wrap gap-4 mt-8"
                    role="list"
                    aria-label="Список категорий проектов"
                >
                    <div 
                        v-for="category in categories" 
                        :key="category.id"
                        class="cursor-pointer"
                        @click="handleCategoryClick(category)"
                        @keydown.enter="handleCategoryClick(category)"
                        @keydown.space.prevent="handleCategoryClick(category)"
                        tabindex="0"
                        role="listitem"
                        :aria-label="`Перейти к проектам в категории ${category.name || category.title}`"
                    >
                        <CardCategories :infos="[category]" />
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