<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";       
import SliderInDetailPage from "@/Components/Main/SliderInDetailPage.vue";


// Images
import slider1 from "../../images/pictures/slider1.svg"
import slider2 from "../../images/pictures/slider2.svg"
import slider3 from "../../images/pictures/slider3.png"
import slider4 from "../../images/pictures/slider4.png"
import slider5 from "../../images/pictures/slider5.png"
import slider6 from "../../images/pictures/slider6.png"
import BarChart from "@/Components/Main/BarChart.vue";

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
    name: "Камаз",
    status: "В процессе",
    dateRealise: "до 2030 года",
    ownership: "Государственное",
    sumInvest: "10 млн рублей",
    organisator:"ООО “Бовид-трак”",
    indystri:"Торговля и услуги",
    location:"г. Челябинск",
    typeBuild: "Частный",
    countNewWorkPlace:"250",
    desc: `Инвестиционный проект «Станция технического обслуживания (автомобилей) с объектами коммерческого делового назначения», предполагающий строительство высокотехнологичного сервисного центра, успешно реализован в Челябинске при активной государственной поддержке.

В церемонии открытия в сентябре 2025 года приняли участие губернатор Челябинской области Алексей Текслер, глава Челябинска Алексей Лошкин, генеральный директор ПАО «Камаз» Сергей Когогин, заместитель генерального директора «Камаз» по продажам и сервису Антон Сарайкин, президент Группы компаний «Бовид» Борис Видгоф, генеральный директор Группы компаний «Бовид» Михаил Видгоф, руководители компаний Челябинской области, потенциальные клиенты и потребители автотехники «Камаз». В своих выступлениях они отметили продуктивное партнерство государства и бизнеса, позволившее в итоге сделать новый шаг в сфере развития инфраструктуры для транспорта в Челябинской области.
Инициатором проекта выступила компания «Бовид-трак», которая занимается ремонтом и обслуживанием автотехники и является официальным дилером автопроизводителя «Камаз». 

Инвестор предоставил резюме проекта и бизнес-план вместе с пакетом необходимых документами в инвестиционный блок Фонда развития предпринимательства Челябинской области - Центра «Мой бизнес».  
Эксперты провели предынвестиционный финансово-экономический анализ проекта, проверили соответствие бизнес-планов выбранному земельному участку и возможностям технологического присоединения к инженерным коммуникациям.
Совет при Губернаторе Челябинской области по улучшению инвестиционного климата рассмотрел проект и признал соответствующим критериям статуса масштабного, в целях осуществления которого предоставляется земельный участок в аренду без проведения торгов.

Инвестор реализовал это право при содействии Министерства экономического развития Челябинской области, инвестиционного блока Фонда развития предпринимательства Челябинской области - Центра «Мой бизнес» и Агентства инвестиционного развития при Главе Челябинска, которые в тесном взаимодействии занимались сопровождением инвестпроекта на протяжении всего периода реализации - с 2023 по 2025 г.

В плановый срок был построен   дилерский центр с   ремонтной   зоной   на   24   поста   для   автомобилей   и полуприцепов с экспресс-линией проверки основных систем автомобиля. Для центра было создано 90 новых рабочих мест.  В общей сложности инициатор вложил 1 млрд рублей (с НДС) собственных и заемных средств. `
};
</script>

<template>
    <Head title="Contact" />

    <AuthenticatedLayout>
        <div class="mx-auto py-6 px-4 sm:px-10 lg:px-16">
            <div class="flex border-b border-black pb-6">
                <div class="flex w-8/12 max-xl:w-auto h-[600px] max-xl:flex-col ">
                    <SliderInDetailPage class="mb-[120px]" :photos="imagesSlider"></SliderInDetailPage>
                </div>
                <div class="flex flex-col w-4/12 px-3 ml-8 border-l border-black">
                    <div class="flex items-center justify-between w-full">
                        <h1 class="font-bold text-3xl">{{ infoProject.name }}</h1>
                        <p class="bg-[#D5D5D5] text-2xl p-3 rounded-xl">{{ infoProject.status }}</p>
                    </div>
                    <div class="flex flex-col h-[500px] justify-between mt-10">
                        <p class="text-2xl ">Срок реализации: {{ infoProject.dateRealise }}</p>
                        <p class="text-2xl ">Собственность: {{ infoProject.ownership }}</p>
                        <p class="text-2xl ">Сумма инвестиций: {{ infoProject.sumInvest }}</p>
                        <p class="text-2xl ">Организатор: {{ infoProject.organisator }}</p>
                        <p class="text-2xl ">Деятельность: {{ infoProject.indystri }}</p>
                        <p class="text-2xl ">Расположение: {{ infoProject.location }}</p>
                        <p class="text-2xl ">Вид строительства: {{ infoProject.typeBuild }}</p>
                        <p class="text-2xl ">Кол-во создаваемых рабочих мест: {{ infoProject.countNewWorkPlace }}</p>
                    </div>
                </div>                
            </div>
            <!-- desc -->
            <div class="flex flex-col border-b border-black pb-6 mt-8">
                <h1 class="text-3xl mb-6">Описание</h1>
                <p class="text-2xl whitespace-pre-line leading-normal">{{ infoProject.desc }}</p>
            </div>

            <!-- grafics -->
             <div class="my-8">
                <h2 class="text-3xl mb-4">Графики выгодности</h2>
                <div class="grid grid-cols-2 gap-8">
                    <div class="border border-black w-full h-[600px] rounded-xl p-6">
                        <div class="border border-black h-full">
                            <BarChart></BarChart>
                        </div>
                    </div>
                    <div class="border border-black w-full h-[600px] rounded-xl">
                        первый блок
                    </div>
                    <div class="border border-black w-full h-[600px] rounded-xl">
                        первый блок
                    </div>
                    <div class="w-full h-[600px] grid grid-cols-2 gap-4">
                        <div class="border border-black rounded-xl">
                            первый индикатор
                        </div>
                        <div class="border border-black rounded-xl">
                            первый индикатор
                        </div>
                        <div class="border border-black rounded-xl">
                            первый индикатор
                        </div>
                        <div class="border border-black rounded-xl">
                            первый индикатор
                        </div>
                    </div>
                    
                </div>
             </div>
        </div>
    </AuthenticatedLayout>
</template>
