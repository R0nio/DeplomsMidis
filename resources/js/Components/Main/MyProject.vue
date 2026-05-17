<script setup>
import slider3 from "../../../images/pictures/slider6.png"

defineProps({
    project: {
        type: Object,
        required: false,
        default: () => ({
            status: "В процессе",
            name: "Название проекта",
            timeRelise: "20.05.2030",
            summInvest: "2 000 000 ₽",
            typeBuild: "Государственная"
        })
    }
})
</script>

<template>
    <div class="w-full h-[500px] lg:h-[550px] flex flex-col justify-between bg-blue-300 rounded-xl relative">
        <a href="" class="w-full h-full">
            <img :src="slider3" alt="Проект" class="w-full h-full object-cover rounded-xl">
        </a>
        
        <div class="w-full flex absolute top-2 left-2 right-2">
            <div class="font-normal text-xl lg:text-3xl p-2.5 bg-[#7390C5] text-white mr-auto rounded-xl flex items-center h-min border-b-2 border-r-2 border-white">
                {{ project.status }}
            </div>
            <div class="mt-2 mr-2 cursor-pointer hover:opacity-70 transition">
                <img src="../../../images/Email.png" alt="star" class="w-8 h-8 lg:w-auto lg:h-auto">
            </div>
        </div>
        
        <div class="bg-[#267FBE] w-full text-white px-4 lg:px-6 py-5 rounded-xl absolute bottom-0 border-t-2 border-white">
            <div class="grid md:grid-cols-2 grid-cols-1 gap-2 text-sm lg:text-xl mb-4">
                <p>Название: {{ project.name }}</p>
                <p>Срок реализации: {{ project.timeRelise }}</p>
                <p>Сумма инвестиций: {{ project.summInvest }}</p>
                <p>Собственность: {{ project.typeBuild }}</p>
            </div>
            
            <a href="">
                <div class="text-black text-xl lg:text-2xl w-full mt-4 flex items-center justify-center bg-amber-500 rounded-xl p-3 hover:bg-amber-600 hover:text-white transition duration-300">
                    <p>Редактировать</p>
                </div>
            </a>
        </div>
    </div>
</template>