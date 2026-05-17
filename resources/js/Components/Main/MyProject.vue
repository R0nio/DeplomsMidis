<script setup>
import slider3 from "../../../images/pictures/slider6.png"

defineProps({
    project:{
        type: Object,
        required: true,
    }
})
</script>

<template>
    <div class="w-full max-xl:h-max h-[500px] pb-20 flex flex-col justify-between bg-blue-300 rounded-xl relative">
        <a href="" class="w-full h-full">
            <img :src="slider3" alt="" class="rounded-xl">
        </a>
        <div class="w-full flex absolute">
            <div class="font-normal text-3xl p-2.5 bg-[#7390C5] text-white mr-auto rounded-xl flex items-center h-min border-b-2 border-r-2 border-white">
                <!-- Статус {{ project.status }}-->
                В процессе
            </div>
            <div class="mt-2 mr-2">
                <img src="../../../images/Email.png" alt="star">
            </div>
        </div>
        <div class="bg-[#267FBE] w-full text-white px-6 py-5  rounded-xl text-2xl absolute bottom-0 border-t-2 border-white">
            <div class="grid xl:grid-cols-2 grid-cols-1 xl:text-xl text-base">

                <!-- Название {{ project.name }}-->
                <p>Название: </p>

                <!-- Время реализации {{ project.name }}-->
                <p>Срок реализации: </p>

                <!-- Сумма инвестиций {{ project.name }}-->
                <p>Сумма инвестиций: </p>

                <!-- Собственность {{ project.name }}-->
                <p>Собственность: </p>
                            
            </div>
            <a href="">
                <div class="text-black text-2xl w-full mt-4 flex items-center justify-center bg-amber-500 rounded-xl p-3 hover:bg-amber-700 hover:text-white ">
                    <p>Редактировать</p>
                </div>
            </a>
        </div>
    </div>
</template>