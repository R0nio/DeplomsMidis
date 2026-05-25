<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
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

// Форматирование чисел
const formatNumber = (number) => {
    return Number(number).toLocaleString('ru-RU');
};

// Форматирование миллионов
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

// Карточки статистики с реальными данными
const cardInfoProjects = computed(() => [
    {
        title: "Выбрали нас", 
        info: `${formatNumber(props.statistics?.total_projects || 0)}+ проектов`
    },
    {
        title: "Уже собрали", 
        info: `${formatMillions(props.statistics?.total_investment || 0)} рублей`
    },
    {
        title: "Создано рабочих мест", 
        info: `${formatNumber(props.statistics?.total_jobs || 0)} человек`
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
                <div v-if="categories && categories.length > 0" class="flex flex-wrap gap-4 mt-8 mb-11">
                    <CardCategories :infos="categories"></CardCategories>
                </div>
                <div v-else class="text-center py-8 rounded-xl" style="background-color: #284139; border: 2px solid #886830;" role="status" aria-label="Категории не найдены">
                    <p class="text-white/60">Категории появятся после добавления проектов</p>
                </div>
            </section>

            <!-- Рекомендуемые проекты -->
            <section aria-labelledby="recommended-projects-title">
                <Title id="recommended-projects-title" class="mb-6" value="Вам может понравиться"></Title>
                <div v-if="randomProjects && randomProjects.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <CardBoxInDashboard 
                        v-for="project in randomProjects" 
                        :key="project.id"
                        :project="project" 
                        :isFavorited="favoriteProjects"
                    ></CardBoxInDashboard>
                </div>
                <div v-else class="text-center py-12 rounded-xl mb-[120px]" style="background-color: #284139; border: 2px solid #886830" role="status" aria-label="Проекты не найдены">
                    <p class="text-white/80 text-xl">Проекты для отображения появятся скоро</p>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>