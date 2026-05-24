<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";       
import Slider from "@/Components/Main/Slider.vue";
import CardInMainPage from "@/Components/Main/CardInMainPage.vue";
import CardCategories from "@/Components/Main/CardCategories.vue";
import CardMain from "@/Components/Main/CardMain.vue";
import ContactsInfo from "@/Components/Main/ContactsInfo.vue";
import TitlePage from "@/Layouts/TitlePage.vue";
import Title from "@/Components/Main/Title.vue";
import Vk from "../../images/VK com.png";
import { computed } from 'vue';

const props = defineProps({
    auth: Object,
    statistics: {
        type: Object,
        required: true
    },
    categories: {
        type: Array,
        required: true
    },
    randomProjects: {
        type: Array,
        required: true
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
        info: `${formatNumber(props.statistics.total_projects)}+ проектов`
    },
    {
        title: "Уже собрали", 
        info: `${formatMillions(props.statistics.total_investment)} рублей`
    },
    {
        title: "Создано рабочих мест", 
        info: `${formatNumber(props.statistics.total_jobs)} человек`
    },
]);

// Подготовка изображений для слайдера
const imagesSlider = computed(() => {
    return props.randomProjects.map(project => ({
        id: project.id,
        src: project.image || '/images/no-image.png',
        alt: project.title,
        title: project.title,
        description: project.description
    }));
});

// Информация о Челябинске
const InfoCheliabyns = [
    {
        title: "Челябинская область — это индустриальное сердце Урала", 
        text: "населением почти 3,4 млн человек, где базируются металлургические гиганты, машиностроительные заводы, предприятия робототехники и атомной промышленности."
    },
    {
        title: "Для инвесторов и авторов проектов это означает", 
        text: "готовый спрос на модернизацию, логистику, энергоэффективность, туристическую инфраструктуру и субподрядные сервисы при поддержке государства."
    },
];

// Контакты
const phonesInfo = [
    {number: "+7(999)999-99-99"},
    {number: "+7(999)999-99-98"},
];

const emails = [
    {pochta: "Example1@invesProject.ya"},
    {pochta: "Example2@invesProject.ya"},
];

const socials = [
    {name: "Cheliabinsk_Project_Invest", img: Vk}
];
</script>

<template>
    <Head title="Главная — InvestProject" />

    <AuthenticatedLayout>
        <template #header>
            <TitlePage id="Main" value="Главная"></TitlePage>
        </template>
        
        <div class="mx-auto py-6 px-4 sm:px-10 lg:px-16">
            <!-- Информация о проектах -->
            <Title value="Информация о проектах"></Title>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mt-8 mb-11 sm:grid-cols-2">
                <CardInMainPage :infos="cardInfoProjects"></CardInMainPage>
            </div>

            <!-- Категории -->
            <Title value="Категории"></Title>
            <div v-if="categories.length > 0" class="flex flex-wrap gap-8 mt-8 mb-11">
                <CardCategories :infos="categories"></CardCategories>
            </div>
            <div v-else class="text-center py-8 text-gray-500">
                <p>Категории появятся после добавления проектов</p>
            </div>

            <!-- Слайдер -->
            <Title id="Slider" class="mb-6" value="Вам может понравиться"></Title>
            <div v-if="randomProjects.length > 0">
                <Slider class="mb-[120px]" :photos="imagesSlider"></Slider>
            </div>
            <div v-else class="text-center py-12 bg-gray-100 rounded-xl mb-[120px]">
                <p class="text-gray-500 text-xl">Проекты для отображения появятся скоро</p>
            </div>

            <!-- Что такое Челябинск -->
            <Title id="HowUseSite" value="Описание Челябинска"></Title>
            <div class="flex flex-col gap-12 mt-6 mb-11">
                <CardMain :infos="InfoCheliabyns"></CardMain>
            </div>

            <!-- Контакты -->
            <Title id="Contact" class="mt-24" value="Контакты"></Title>
            <ContactsInfo 
                class="mb-[120px]" 
                :phonesdata="phonesInfo" 
                :emails="emails" 
                :socials="socials"
            ></ContactsInfo>
        </div>
    </AuthenticatedLayout>
</template>