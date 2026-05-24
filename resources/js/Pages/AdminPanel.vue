<script setup>
import SearchInput from "@/Components/Main/SearchInput.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";
import { FwbSelect, FwbInput, FwbButton } from 'flowbite-vue'
import CardProjectList from "@/Components/Main/CardProjectList.vue";
// image import
import list from "../../images/list.png";
import cards from "../../images/Cards.png";
import CardBox from "@/Components/Main/CardBox.vue";

const sumfrom = ref('')
const sumbefore = ref('')
const mainColor = "#8EB6FF";
const switcherViewCard = ref(true);

// первый фильтр
const selectedIndustry = ref('')
const selectedStatus = ref('')
const selectedOwnership = ref('')
const selectedTypeBuild = ref('')

const industry = [
    { value: '1', name: 'Пример' },
    { value: '2', name: 'Пример' },
    { value: '3', name: 'Пример' },
]

const status = [
    { value: '4', name: 'Пример' },
    { value: '5', name: 'Пример' },
    { value: '6', name: 'Пример' },
]

const ownership = [
    { value: '7', name: 'Пример' },
    { value: '8', name: 'Пример' },
    { value: '9', name: 'Пример' },
]

const typeBuild = [
    { value: '10', name: 'Пример' },
    { value: '11', name: 'Пример' },
    { value: '12', name: 'Пример' },
]

const projectdata = [
    {name: "Камаз", summInvest: 2000000, timeRelise: "20.05.2030", typeBuild: "Государство", indystri:"Недвижемость", link:"mapProject", status: "В процессе"},
    {name: "Камаз", summInvest: 2000000, timeRelise: "20.05.2030", typeBuild: "Государство", indystri:"Недвижемость", link:"mapProject", status: "В процессе"},
    {name: "Камаз", summInvest: 2000000, timeRelise: "20.05.2030", typeBuild: "Государство", indystri:"Недвижемость", link:"mapProject", status: "В процессе"},
    {name: "Камаз", summInvest: 2000000, timeRelise: "20.05.2030", typeBuild: "Государство", indystri:"Недвижемость", link:"mapProject", status: "В процессе"},
    {name: "Камаз", summInvest: 2000000, timeRelise: "20.05.2030", typeBuild: "Государство", indystri:"Недвижемость", link:"mapProject", status: "В процессе"},
    {name: "Камаз", summInvest: 2000000, timeRelise: "20.05.2030", typeBuild: "Государство", indystri:"Недвижемость", link:"mapProject", status: "В процессе"},
    {name: "Камаз", summInvest: 2000000, timeRelise: "20.05.2030", typeBuild: "Государство", indystri:"Недвижемость", link:"mapProject", status: "В процессе"},
    {name: "Камаз", summInvest: 2000000, timeRelise: "20.05.2030", typeBuild: "Государство", indystri:"Недвижемость", link:"mapProject", status: "В процессе"},
];
</script>

<template>
    <Head title="Админ-панель — InvestProject" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-semibold leading-tight text-gray-900">
                Админ панель
            </h1>
        </template>

        <div class="px-[80px] max-xl:px-4 py-6">
            <div class="w-full border-2 rounded-xl border-black h-full p-3" :style="{ backgroundColor: mainColor }">
                <!-- search -->
                <div class="flex items-center mb-3 flex-wrap gap-3">
                    <p class="w-[300px] p-3 text-lg">Поиск по названию проекта</p>
                    <SearchInput></SearchInput>
                </div>
                
                <!-- other filter -->
                <div class="flex justify-between flex-wrap gap-3">
                    <fwb-select
                        v-model="selectedIndustry"
                        :options="industry"
                        placeholder="Выбери отрасль"
                        class="flex-1 min-w-[150px]"
                    />
                    
                    <fwb-input
                        v-model="sumfrom"
                        placeholder="Сумма от"
                        class="flex-1 min-w-[120px]"
                    />
                    
                    <fwb-input
                        v-model="sumbefore"
                        placeholder="Сумма до"
                        class="flex-1 min-w-[120px]"
                    />

                    <fwb-select
                        v-model="selectedStatus"
                        :options="status"
                        placeholder="Выбери статус"
                        class="flex-1 min-w-[150px]"
                    />
                    
                    <fwb-select
                        v-model="selectedOwnership"
                        :options="ownership"
                        placeholder="Выбери собственность"
                        class="flex-1 min-w-[150px]"
                    />
                    
                    <fwb-select
                        v-model="selectedTypeBuild"
                        :options="typeBuild"
                        placeholder="Выбери вид строительства"
                        class="flex-1 min-w-[150px]"
                    />
                    
                    <fwb-button color="light">Очистить Х</fwb-button>
                </div>
            </div>
            
            <!-- Проекты -->
            <div class="w-full border-2 rounded-xl border-black h-max flex flex-col mt-3" :style="{ backgroundColor: mainColor }">
                <div class="flex w-full justify-end items-center p-3">
                    <div class="flex bg-white gap-4 p-4 rounded-xl" role="group" aria-label="Переключатель вида проектов">
                        <button
                            type="button"
                            :class="`rounded-xl cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-700 ${switcherViewCard ? 'opacity-50' : ''}`"
                            @click="switcherViewCard = true"
                            :aria-pressed="switcherViewCard ? 'true' : 'false'"
                            aria-label="Показать проекты списком"
                        >
                            <img :src="list" alt="" aria-hidden="true">
                        </button>
                        <button
                            type="button"
                            :class="`rounded-xl cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-700 ${switcherViewCard ? '' : 'opacity-50'}`"
                            @click="switcherViewCard = false"
                            :aria-pressed="!switcherViewCard ? 'true' : 'false'"
                            aria-label="Показать проекты карточками"
                        >
                            <img :src="cards" alt="" aria-hidden="true">
                        </button>
                    </div>
                </div>
                
                <div v-if="switcherViewCard">
                    <div :class="`w-full flex flex-col my-5 gap-4 ${projectdata.length >= 12 ? 'overflow-y-scroll h-[80vh]' : 'h-auto overflow-hidden'}`">
                        <div v-for="project in projectdata" :key="project.name" class="px-8">
                            <div>
                                <CardProjectList :projectData="project"></CardProjectList>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div v-else>
                    <div :class="`w-full grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 my-5 gap-4 ${projectdata.length >= 4 ? 'overflow-y-scroll h-[80vh]' : 'h-auto overflow-hidden'}`">
                        <div v-for="project in projectdata" :key="project.name" class="px-8">
                            <CardBox :project="project"></CardBox>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>