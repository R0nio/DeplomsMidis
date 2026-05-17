<script setup>
import { ref } from 'vue'
import slider3 from "../../../images/pictures/slider6.png"
import favoriteIcon from "../../../images/Favorite.png"
import favoriteActiveIcon from "../../../images/FavoriteActivity.png"

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

// Состояние избранного
const isFavorite = ref(false)

// Переключение избранного
const toggleFavorite = () => {
    isFavorite.value = !isFavorite.value
    
    // Здесь можно добавить логику сохранения в localStorage или отправку на сервер
    // Например:
    // if (isFavorite.value) {
    //     addToFavorites(project.value)
    // } else {
    //     removeFromFavorites(project.value)
    // }
}
</script>

<template>
    <div class="w-full h-[450px] lg:h-[500px] flex flex-col justify-between bg-blue-300 rounded-xl relative">
        <a href="" class="w-full h-full">
            <img :src="slider3" alt="Проект" class="w-full h-full object-cover rounded-xl">
        </a>
        
        <div class="w-full flex absolute top-2 left-2 right-2">
            <div class="font-normal text-xl lg:text-3xl p-2.5 bg-[#7390C5] text-white mr-auto rounded-xl flex items-center h-min border-b-2 border-r-2 border-white">
                {{ project.status }}
            </div>
            <div 
                @click="toggleFavorite" 
                class="mt-2 mr-8 cursor-pointer hover:opacity-80 transition"
            >
                <img 
                    :src="isFavorite ? favoriteActiveIcon : favoriteIcon" 
                    alt="star" 
                    class="w-12 h-12 transition-transform"
                >
            </div>
        </div>
        
        <div class="bg-[#267FBE] w-full text-white px-4 lg:px-6 py-5 rounded-xl absolute bottom-0 border-t-2 border-white">
            <div class="grid md:grid-cols-2 grid-cols-1 gap-2 text-sm lg:text-xl">
                <p>Название: {{ project.name }}</p>
                <p>Срок реализации: {{ project.timeRelise }}</p>
                <p>Сумма инвестиций: {{ project.summInvest }}</p>
                <p>Собственность: {{ project.typeBuild }}</p>
            </div>
        </div>
    </div>
</template>