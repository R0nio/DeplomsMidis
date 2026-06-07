<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import CardInMainPage from "@/Components/Main/CardInMainPage.vue";
import CardCategories from "@/Components/Main/CardCategories.vue";
import TitlePage from "@/Layouts/TitlePage.vue";
import Title from "@/Components/Main/Title.vue";
import { computed } from 'vue';
import CardBoxInDashboard from "@/Components/Main/CardBoxInDashboard.vue";

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

const isAuthenticated = props.auth && props.auth.user;

/**
 * Форматирование чисел с разделителями тысяч
 * @param {number} number - число для форматирования
 * @returns {string} - отформатированное число
 */
const formatNumber = (number) => {
    return Number(number).toLocaleString('ru-RU');
};

/**
 * Форматирование больших чисел в удобочитаемый формат (млн, млрд)
 * @param {number} number - число для форматирования
 * @returns {string} - отформатированное число с единицей измерения
 */
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

/**
 * Функция для перехода на страницу проектов с выбранной категорией
 * Сохраняет категорию в sessionStorage для применения фильтра
 * @param {Object} category - объект категории с id и name
 */
const handleCategoryClick = (category) => {
    // Сохраняем выбранную категорию в sessionStorage
    sessionStorage.setItem('selectedCategory', JSON.stringify({
        id: category.id,
        name: category.name || category.title
    }));
    
    // Переходим на страницу проектов
    router.visit(route('projects'), {
        preserveState: false,
        preserveScroll: false,
    });
};

/**
 * Карточки статистики с реальными данными
 * Формируются из пропса statistics
 */
const cardInfoProjects = computed(() => [
    {
        title: "Выбрали нас", 
        info: `${formatNumber(props.statistics?.total_projects || 0)}+ проектов`,
        ariaLabel: `На платформе размещено ${formatNumber(props.statistics?.total_projects || 0)} проектов`
    },
    {
        title: "Уже собрали", 
        info: `${formatMillions(props.statistics?.total_investment || 0)} рублей`,
        ariaLabel: `Общая сумма инвестиций: ${formatMillions(props.statistics?.total_investment || 0)} рублей`
    },
    {
        title: "Создано рабочих мест", 
        info: `${formatNumber(props.statistics?.total_jobs || 0)} человек`,
        ariaLabel: `Создано ${formatNumber(props.statistics?.total_jobs || 0)} новых рабочих мест`
    },
]);
</script>

<template>
    <Head title="Главная">
        <meta name="description" content="Инвестиционная платформа для поиска и финансирования проектов">
    </Head>

    <AuthenticatedLayout>
        <template #header>
            <TitlePage id="Main" value="Главная"></TitlePage>
        </template>
        
        <div class="mx-auto py-6 px-4 sm:px-10 lg:px-16">
            <!-- Информация о проектах -->
            <section aria-labelledby="projects-info-title">
                <Title id="projects-info-title" value="Информация о проектах"></Title>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mt-8 mb-11 sm:grid-cols-2">
                    <CardInMainPage :infos="cardInfoProjects"></CardInMainPage>
                </div>
            </section>

            <!-- Категории -->
            <section aria-labelledby="categories-title">
                <Title id="categories-title" value="Категории"></Title>
                <div 
                    v-if="categories && categories.length > 0" 
                    class="flex flex-wrap gap-4 mt-8 mb-11"
                    role="list"
                    aria-label="Список категорий проектов"
                >
                    <div 
                        v-for="category in categories" 
                        :key="category.id"
                        class="cursor-pointer transition-transform hover:scale-105 focus:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 rounded-xl"
                        @click="handleCategoryClick(category)"
                        @keydown.enter="handleCategoryClick(category)"
                        @keydown.space.prevent="handleCategoryClick(category)"
                        tabindex="0"
                        role="listitem"
                        :aria-label="`Перейти к проектам в категории ${category.name || category.title}. Количество проектов: ${category.count || 0}`"
                    >
                        <CardCategories :infos="[category]"></CardCategories>
                    </div>
                </div>
                <div 
                    v-else 
                    class="text-center py-8 rounded-xl" 
                    style="background-color: #284139; border: 2px solid #886830;" 
                    role="status" 
                    aria-label="Категории не найдены"
                >
                    <p class="text-white/60">Категории появятся после добавления проектов</p>
                </div>
            </section>

            <!-- Рекомендуемые проекты -->
            <section aria-labelledby="recommended-projects-title">
                <Title id="recommended-projects-title" class="mb-6" value="Вам может понравиться"></Title>
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
                    ></CardBoxInDashboard>
                </div>
                <div 
                    v-else 
                    class="text-center py-12 rounded-xl mb-[120px]" 
                    style="background-color: #284139; border: 2px solid #886830" 
                    role="status" 
                    aria-label="Проекты не найдены"
                >
                    <p class="text-white/80 text-xl">Проекты для отображения появятся скоро</p>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
