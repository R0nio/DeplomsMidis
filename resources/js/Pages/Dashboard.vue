<script setup>
// Main import
import ButtonLink from "@/Components/Main/ButtonLink.vue";
import Img from "@/Components/Main/Img.vue";
import Title from "@/Components/Main/Title.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TitlePage from "@/Layouts/TitlePage.vue";
import { Head } from "@inertiajs/vue3";       
import Slider from "@/Components/Main/Slider.vue";


// Images
import slider1 from "../../images/pictures/slider1.svg"
import slider2 from "../../images/pictures/slider2.svg"
import slider3 from "../../images/pictures/slider3.png"
import slider4 from "../../images/pictures/slider4.png"
import slider5 from "../../images/pictures/slider5.png"
import slider6 from "../../images/pictures/slider6.png"
import MainChelyabinsk from "../../images/ChelyabinskMainPhoto.png";
import CardMain from "@/Components/Main/CardMain.vue";
import ContactsInfo from "@/Components/Main/ContactsInfo.vue";
import Vk from "../../images/VK com.png";
import investor from "../../images/Инвестор.jpg";
import predrinimatel from "../../images/Предприниматель.jpg";


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
const InformsUsers = [
    {text: "Просматривайте проекты на карте и в списке"},
    {text: "Используйте удобные фильтры"},
    {text: "Добавляйте понравившиеся проекты в избранное"},
    {text: "Связывайтесь с организаторами напрямую"},
]
const InformsOrganisator = [
    {text: "Зарегистрируйтесь как организатор"},
    {text: "Добавьте свой инвестиционный проект"},
    {text: "Пройдите модерацию"},
    {text: "Получайте предложения от инвесторов и обновляйте информацию"},
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
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header >
            <TitlePage id="Main" value="Главная страница"></TitlePage>
        </template>
        <div class="mx-auto py-6 px-4 sm:px-10 lg:px-16">
            <Title  value="Инвестируйте в будущее Челябинской области"></Title>
            <ButtonLink value="Смотреть проекты" :link="route('projects')"></ButtonLink>
            <Img :img="MainChelyabinsk"></Img>
            <TitlePage id="Slider" class="mb-5" value="Возможно вам понравится"></TitlePage>
            <!-- slider -->          
            <Slider class="mb-[120px]" :photos="imagesSlider"></Slider>
            <TitlePage id="HowUseSite" value="Как пользоваться сайтом?"></TitlePage>
            <div class="py-5 pt-[120px] flex justify-around max-lg:flex-col">
                <CardMain title="Для всех пользователей" :infos="InformsUsers" :photo="investor"></CardMain>
                <CardMain title="Для предпинимателей" :infos="InformsOrganisator" :photo="predrinimatel"></CardMain>
            </div>
            <TitlePage id="Contact" class="mt-24" value="Контакты"></TitlePage>
            <ContactsInfo class="mb-[120px]" :phonesdata="phonesInfo" :emails="emails" :socials="socials"></ContactsInfo>

        </div>
    </AuthenticatedLayout>
</template>
