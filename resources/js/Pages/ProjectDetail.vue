<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";     
import { GoogleMap, Marker, InfoWindow } from "vue3-google-map";
import SliderInDetailPage from "@/Components/Main/SliderInDetailPage.vue";
import BarChart from "@/Components/Main/BarChart.vue";

// Images
import slider1 from "../../images/pictures/slider1.svg"
import slider2 from "../../images/pictures/slider2.svg"
import slider3 from "../../images/pictures/slider3.png"
import slider4 from "../../images/pictures/slider4.png"
import slider5 from "../../images/pictures/slider5.png"
import slider6 from "../../images/pictures/slider6.png"
import FavoriteIcon from "../../images/Favorite.png"
import FavoriteActiveIcon from "../../images/FavoriteActivity.png"
import predrinimatel from "../../images/Предприниматель.jpg";

//imgSlider
const imagesSlider = [
    { src: slider1, alt: 'Фото 1' },
    { src: slider2, alt: 'Фото 2' },
    { src: slider3, alt: 'Фото 3' },
    { src: slider4, alt: 'Фото 4' },
    { src: slider5, alt: 'Фото 5' },
    { src: slider6, alt: 'Фото 6' },
]

const infoProject = {
    id: 1,
    name: "Камаз",
    status: "В процессе",
    dateRealise: "до 2030 года",
    ownership: "Государственное",
    sumInvest: "10 млн рублей",
    organisator:"ООО Бовид-трак",
    indystri:"Торговля и услуги",
    location:"г. Челябинск",
    typeBuild: "Частный",
    countNewWorkPlace:"250",
    desc: `Инвестиционный проект «Станция технического обслуживания (автомобилей) с объектами коммерческого делового назначения», предполагающий строительство высокотехнологичного сервисного центра, успешно реализован в Челябинске при активной государственной поддержке.

В церемонии открытия в сентябре 2025 года приняли участие губернатор Челябинской области Алексей Текслер, глава Челябинска Алексей Лошкин, генеральный директор ПАО «Камаз» Сергей Кogogin, заместитель генерального директора «Камаз» по продажам и сервису Антон Сарайкин, президент Группы компаний «Бовид» Борис Видгоф, генеральный директор Группы компаний «Бовид» Михаил Видгоф, руководители компаний Челябинской области, потенциальные клиенты и потребители автотехники «Камаз». В своих выступлениях они отметили продуктивное партнерство государства и бизнеса, позволившее в итоге сделать новый шаг в сфере развития инфраструктуры для транспорта в Челябинской области.
Инициатором проекта выступила компания «Бовид-трак», которая занимается ремонтом и обслуживанием автотехники и является официальным дилером автопроизводителя «Камаз». 

Инвестор предоставил резюме проекта и бизнес-план вместе с пакетом необходимых документами в инвестиционный блок Фонда развития предпринимательства Челябинской области - Центра «Мой бизнес».  
Эксперты провели предынвестиционный финансово-экономический анализ проекта, проверили соответствие бизнес-планов выбранному земельному участку и возможностям технологического присоединения к инженерным коммуникациям.
Совет при Губернаторе Челябинской области по улучшению инвестиционного климата рассмотрел проект и признал соответствующим критериям статуса масштабного, в целях осуществления которого предоставляется земельный участок в аренду без проведения торгов.

Инвестор реализовал это право при содействии Министерства экономического развития Челябинской области, инвестиционного блока Фонда развития предпринимательства Челябинской области - Центра «Мой бизнес» и Агентства инвестиционного развития при Главе Челябинска, которые в тесном взаимодействии занимались сопровождением инвестпроекта на протяжении всего периода реализации - с 2023 по 2025 г.

В плановый срок был построен   дилерский центр с   ремонтной   зоной   на   24   поста   для   автомобилей   и полуприцепов с экспресс-линией проверки основных систем автомобиля. Для центра было создано 90 новых рабочих мест.  В общей сложности инициатор вложил 1 млрд рублей (с НДС) собственных и заемных средств. `
};

// Функционал избранного
const isFavorite = ref(false);

const toggleFavorite = () => {
    isFavorite.value = !isFavorite.value;
    
    // Сохранение в localStorage
    const favorites = JSON.parse(localStorage.getItem('favorites') || '[]');
    
    if (isFavorite.value) {
        if (!favorites.includes(infoProject.id)) {
            favorites.push(infoProject.id);
        }
    } else {
        const index = favorites.indexOf(infoProject.id);
        if (index > -1) {
            favorites.splice(index, 1);
        }
    }
    
    localStorage.setItem('favorites', JSON.stringify(favorites));
};

// Проверка при загрузке
const checkFavoriteStatus = () => {
    const favorites = JSON.parse(localStorage.getItem('favorites') || '[]');
    isFavorite.value = favorites.includes(infoProject.id);
};

// Вызов при монтировании
checkFavoriteStatus();


const api = "AIzaSyC6L3uzc8_Ug01uxrFdYhN_Eo5mIwyaPZY";

const center = { lat: 55.160278, lng: 61.402457 };

const projects = ref([
  {
    id: 1,
    name: "ЖК Сити",
    desс: "Новый жилой комплекс рядом с вашим домом",
    lat: 55.160278,
    lng: 61.402457,
    image: predrinimatel
  },
  {
    id: 2,
    name: "Бизнес центр",
    desс: "Новый жилой комплекс рядом с вашим домом",
    lat: 55.170278,
    lng: 61.412457,
    image: predrinimatel
  }
]);

const selectedProject = ref(null);

const selectProject = (project) => {
  if (selectedProject.value?.id === project.id) {
    selectedProject.value = null;
    setTimeout(() => {
      selectedProject.value = project;
    }, 0);
  } else {
    selectedProject.value = project;
  }
};

const goToProject = (id) => {
  router.visit(`/projects/${id}`);
};

const closeMarker = () =>{
  selectProject.value = null;
}

</script>

<template>
    <Head title="Contact" />

    <AuthenticatedLayout>
        <div class="mx-auto py-6 px-4 sm:px-10 lg:px-16">
            <!-- Основной контент -->
            <div class="flex flex-col xl:flex-row border-b-2 border-white pb-6">
                <!-- Слайдер -->
                <div class="w-full xl:w-8/12 h-[400px] sm:h-[500px] xl:h-[600px]">
                    <SliderInDetailPage :photos="imagesSlider"></SliderInDetailPage>
                </div>

                <!-- Информация о проекте -->
                <div class="flex flex-col w-full xl:w-4/12 px-4 sm:px-6 xl:ml-8 xl:border-l-2 border-white mt-6 xl:mt-0">
                    <!-- Заголовок с избранным -->
                    <div class="flex items-start justify-between w-full gap-4 mb-6">
                        <h1 class="font-bold text-2xl sm:text-3xl flex-1">
                            {{ infoProject.name }}
                        </h1>
                        
                        <!-- Кнопка избранного -->
                        <button 
                            @click="toggleFavorite"
                            class="flex-shrink-0 cursor-pointer"
                        >
                            <img 
                                :src="isFavorite ? FavoriteActiveIcon : FavoriteIcon" 
                                alt="Избранное"
                                class="w-8 h-8 sm:w-10 sm:h-10"
                            >
                        </button>
                    </div>

                    <!-- Статус -->
                    <div class="mb-6">
                        <span class="inline-block bg-[#7390C5] text-white text-lg sm:text-xl lg:text-2xl px-4 py-3 rounded-xl">
                            {{ infoProject.status }}
                        </span>
                    </div>

                    <!-- Детали проекта -->
                    <div class="flex flex-col h-max gap-4 justify-between space-y-4">
                        <p class="text-lg sm:text-xl lg:text-2xl">Срок реализации: {{ infoProject.dateRealise }}</p>
                        <p class="text-lg sm:text-xl lg:text-2xl">Собственность: {{ infoProject.ownership }}</p>
                        <p class="text-lg sm:text-xl lg:text-2xl">Сумма инвестиций: {{ infoProject.sumInvest }}</p>
                        <p class="text-lg sm:text-xl lg:text-2xl">Организатор: {{ infoProject.organisator }}</p>
                        <p class="text-lg sm:text-xl lg:text-2xl">Деятельность: {{ infoProject.indystri }}</p>
                        <p class="text-lg sm:text-xl lg:text-2xl">Расположение: {{ infoProject.location }}</p>
                        <p class="text-lg sm:text-xl lg:text-2xl">Вид строительства: {{ infoProject.typeBuild }}</p>
                        <p class="text-lg sm:text-xl lg:text-2xl">Кол-во создаваемых рабочих мест: {{ infoProject.countNewWorkPlace }}</p>
                    </div>
                </div>                
            </div>

            <!-- Описание -->
            <div class="flex flex-col border-b-2 border-white pb-6 mt-8">
                <h1 class="text-2xl sm:text-3xl mb-6 text-white">Описание</h1>
                <p class="text-lg sm:text-xl lg:text-2xl whitespace-pre-line leading-normal">{{ infoProject.desc }}</p>
            </div>

            <!-- Графики -->
            <div class="my-8">
                <h2 class="text-2xl sm:text-3xl mb-4 text-white">Графики выгодности</h2>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
                    <div class="border-2 border-white w-full h-[400px] sm:h-[500px] lg:h-[600px] rounded-xl p-4 sm:p-6">
                        <div class="border-2 border-white h-full">
                            <BarChart></BarChart>
                        </div>
                    </div>
                    <div class="border-2 border-white w-full h-[400px] sm:h-[500px] lg:h-[600px] rounded-xl flex items-center justify-center">
                        <p class="text-xl">График 2</p>
                    </div>
                    <div class="border-2 border-white w-full h-[400px] sm:h-[500px] lg:h-[600px] rounded-xl flex items-center justify-center">
                        <p class="text-xl">График 3</p>
                    </div>
                    <div class="w-full h-[400px] sm:h-[500px] lg:h-[600px] grid grid-cols-2 gap-4">
                        <div class="border-2 border-white rounded-xl flex items-center justify-center">
                            <p>Индикатор 1</p>
                        </div>
                        <div class="border-2 border-white rounded-xl flex items-center justify-center">
                            <p>Индикатор 2</p>
                        </div>
                        <div class="border-2 border-white rounded-xl flex items-center justify-center">
                            <p>Индикатор 3</p>
                        </div>
                        <div class="border-2 border-white rounded-xl flex items-center justify-center">
                            <p>Индикатор 4</p>
                        </div>
                    </div>
                </div>
            </div>
                        <!-- Графики -->
            <div class="my-8 pt-8 border-t-2 border-white">
                <h2 class="text-2xl sm:text-3xl mb-4 text-white">Место расположение</h2>
                <div  :style="{backgroundColor: mainColor}" class="mx-auto py-6 px-4 sm:px-10 lg:px-16 ">
                <GoogleMap
                    :api-key="api"
                    class="w-full h-[1000px] rounded-xl"
                    :center="center"
                    :zoom="15"
                    @click="closeMarker"
                >
                    <Marker
                    v-for="project in projects"
                    :key="project.id"
                    :options="{ position: { lat: project.lat, lng: project.lng } }"
                    @click="selectProject(project)"
                    />

                    <InfoWindow
                    v-if="selectedProject"
                    :options="{ position: { lat: selectedProject.lat, lng: selectedProject.lng } }"
                    >
                    <div
                        class="w-52 bg-gray-200 overflow-hidden cursor-pointer"
                        @click="goToProject(selectedProject.id)"
                    >
                        <img
                        :src="selectedProject.image"
                        class="w-full h-32 object-cover"
                        />

                        <div class="p-3">
                        <h3 class="text-lg font-semibold text-gray-800">
                            {{ selectedProject.name }}
                        </h3>
                        <p class="text-sm font-semibold  mt-4">
                            {{ selectedProject.desс }}
                        </p>
                        </div>
                    </div>
                    </InfoWindow>

                </GoogleMap>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>