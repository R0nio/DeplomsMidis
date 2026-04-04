<script setup>
import SearchInput from "@/Components/Main/SearchInput.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";       
import { ref } from "vue";
import { FwbSelect, FwbInput, FwbButton } from 'flowbite-vue'
import CardProjectList from "@/Components/Main/CardProjectList.vue";

// image
import list from "../../images/list.png";
import cards from "../../images/Cards.png";
import CardBox from "@/Components/Main/CardBox.vue";



const sumfrom = ref('')
const sumbefore = ref('')
    

const mainColor = "#C9D8EF";
const footerColor = "#A9BCDB";

const switcherViewCard = ref(true);

console.log(switcherViewCard);

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
const ownership  = [
  { value: '7', name: 'Пример' },
  { value: '8', name: 'Пример' },
  { value: '9', name: 'Пример' },
]
const typeBuild = [
  { value: '10', name: 'Пример' },
  { value: '11', name: 'Пример' },
  { value: '12', name: 'Пример' },
]
// 


// List Card
const projectdata =[
    {name: "Камаз", summInvest: 2000000, timeRelise: "20.05.2030", typeBuild: "Государство", indystri:"Недвижемость", link:"mapProject", status: "В процессе"},
    {name: "Камаз", summInvest: 2000000, timeRelise: "20.05.2030", typeBuild: "Государство", indystri:"Недвижемость", link:"mapProject", status: "В процессе"},
    {name: "Камаз", summInvest: 2000000, timeRelise: "20.05.2030", typeBuild: "Государство", indystri:"Недвижемость", link:"mapProject", status: "В процессе"},
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
    <Head title="Projects" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Проекты
            </h2>
        </template>

        <div :class="`mx-auto py-6 px-4 sm:px-10 lg:px-16 bg-[${mainColor}] bg-[#C9D8EF]`">
            <div class="w-full border-2 rounded-xl border-black h-auto p-3">
                <!-- search -->
                 <div class="flex items-center mb-3">
                    <p class="w-[300px] p-3 text-lg">Поиск по названию проекта</p>
                    <SearchInput></SearchInput>
                 </div>
                <!-- other filter -->
                 <div class="flex justify-between">
                    <fwb-select
                        v-model="selectedIndustry"
                        :options="industry"
                        placeholder="Выбери отрасль"
                    />
                    
                    <fwb-input
                    v-model="sumfrom"
                    placeholder="Сумма от"
                    />
                    <fwb-input
                    v-model="sumbefore"
                    placeholder="Сумма до"
                    />

                    <fwb-select
                        v-model="selectedStatus"
                        :options="status"
                        placeholder="Выбери статус"
                    />
                    <fwb-select
                        v-model="selectedOwnership"
                        :options="ownership"
                        placeholder="Выбери собственность"
                    />
                    <fwb-select
                        v-model="selectedTypeBuild"
                        :options="typeBuild"
                        placeholder="Выбери вид строительства"
                    />
                    <fwb-button color="light" >Очистить Х</fwb-button>
                 </div>
            </div>
            <div :class="`w-full border-2 rounded-xl border-black flex flex-col mt-3`">
                <div class="flex w-full justify-end items-center">
                    <div class="flex bg-white gap-4 p-4 rounded-xl">
                        <div :class="`rounded-xl cursor-pointer ${switcherViewCard ? 'opacity-50 pointer-events-none' : ' '}`" @click="switcherViewCard = true">
                            <img :src="list" alt="Список">
                        </div>
                        <div :class="`rounded-xl cursor-pointer ${switcherViewCard ? '' : 'opacity-50 pointer-events-none'}`" @click="switcherViewCard = false">
                            <img :src="cards" alt="Карточки">
                        </div>
                    </div>
                </div>
                <div v-if="switcherViewCard">
                    <div :class="`w-full flex flex-col my-5 gap-4 ${projectdata.length >= 12 ? 'overflow-y-scroll h-[80vh]' : 'h-auto overflow-hidden'} `">
                        <div v-for="project in projectdata" class="px-8">
                            <CardProjectList :projectData="project"></CardProjectList>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <div :class="`w-full grid grid-cols-2 my-5 gap-4 ${projectdata.length >= 4 ? 'overflow-y-scroll h-[80vh]' : 'h-auto overflow-hidden'} `">
                        <div v-for="project in projectdata" class="px-8">
                            <CardBox :project="project"></CardBox>
                        </div>
                    </div>
                </div>                
            </div>
        </div>
    </AuthenticatedLayout>
</template>
