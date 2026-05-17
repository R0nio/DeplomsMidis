<script setup>
// Main import
import ButtonLink from "@/Components/Main/ButtonLink.vue";
import Img from "@/Components/Main/Img.vue";
import Title from "@/Components/Main/Title.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TitlePage from "@/Layouts/TitlePage.vue";
import { Head } from "@inertiajs/vue3";       
import Slider from "@/Components/Main/Slider.vue";
import CardInMainPage from "@/Components/Main/CardInMainPage.vue";
import CardCategories from "@/Components/Main/CardCategories.vue";


// Images
import slider1 from "../../images/pictures/slider1.svg"
import slider2 from "../../images/pictures/slider2.svg"
import slider3 from "../../images/pictures/slider3.png"
import slider4 from "../../images/pictures/slider4.png"
import slider5 from "../../images/pictures/slider5.png"
import slider6 from "../../images/pictures/slider6.png"
import CardMain from "@/Components/Main/CardMain.vue";
import ContactsInfo from "@/Components/Main/ContactsInfo.vue";
import Vk from "../../images/VK com.png";


// Props
// Authorize
const props = defineProps({
    auth:Object,
});
const isAuthenticated = props.auth && props.auth.user;

//imgSlider
const imagesSlider = [
    { src: slider1, alt: 'Фото 1' },
    { src: slider2, alt: 'Фото 2' },
    { src: slider3, alt: 'Фото 3' },
    { src: slider4, alt: 'Фото 4' },
    { src: slider5, alt: 'Фото 5' },
    { src: slider6, alt: 'Фото 6' },
]

// Card
const InfoCheliabyns = [
    {title: "Челябинская область — это индустриальное сердце Урала", text:"населением почти 3,4 млн человек, где базируются металлургические гиганты, машиностроительные заводы, предприятия робототехники и атомной промышленности."},
    {title: "Для инвесторов и авторов проектов это означает", text:"готовый спрос на модернизацию, логистику, энергоэффективность, туристическую инфраструктуру и субподрядные сервисы при поддержке государства."},
]
const phonesInfo = [
    {number: "+7(999)999-99-99"},
    {number: "+7(999)999-99-98"},
]
const emails = [
    {pochta: "Example1@invesProject.ya"},
    {pochta: "Example2@invesProject.ya"},
]
const socials = [
    {name: "Cheliabinsk_Project_Invest", img: Vk}
]

const cardInfoProjects = [
    {title:"Выбрали нас", info:"500+ проектов"},
    {title:"Выбрали нас", info:"500+ проектов"},
    {title:"Выбрали нас", info:"500+ проектов"},
]

const typeCategories = [
    {title:"Строительство"},
    {title:"Магазин"},
    {title:"Магазин"},
    {title:"Магазин"},
    {title:"Магазин"},
    {title:"Магазин"},

]
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header >
            <TitlePage id="Main" value="Главная"></TitlePage>
        </template>
        <div class="mx-auto py-6 px-4 sm:px-10 lg:px-16">
            <!-- Информация о проектах -->
            <Title  value="Информация о проектах"></Title>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mt-8 mb-11 sm:grid-cols-2">
                <CardInMainPage :infos="cardInfoProjects"></CardInMainPage>
            </div>
            <!-- Категории -->
            <Title  value="Категории"></Title>
            <div class="flex flex-wrap gap-8 mt-8 mb-11">
                <CardCategories :infos="typeCategories"></CardCategories>
            </div>
            <!-- Слайдер -->          
            <Title id="Slider" class="mb-6" value="Вам может понравиться"></Title>
            <Slider class="mb-[120px]" :photos="imagesSlider"></Slider>
            <!-- Что такое Челябинск -->
            <Title id="HowUseSite" value="Описание Челябинска"></Title>
            <div class="flex flex-col gap-12 mt-6 mb-11">
                <CardMain :infos="InfoCheliabyns"></CardMain>
            </div>

            <Title id="Contact" class="mt-24" value="Контакты"></Title>
            <ContactsInfo class="mb-[120px]" :phonesdata="phonesInfo" :emails="emails" :socials="socials"></ContactsInfo>

        </div>
    </AuthenticatedLayout>
</template>
