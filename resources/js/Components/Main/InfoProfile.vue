<script setup>
import { router } from '@inertiajs/vue3';
import FavoriteCard from './FavoriteCard.vue';
import MyProject from './MyProject.vue';
import ProfileinfoLabel from './ProfileinfoLabel.vue';

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
    favoriteProjects: {
        type: Array,
        default: null,
    },
    myProjects: {
        type: Array,
        default: null,
    },
});

// Перейти на страницу всех проектов пользователя
const goToMyProjects = () => {
    router.visit(route('projects.my'));
};

// Перейти на страницу всех проектов
const goToAllProjects = () => {
    router.visit(route('projects.index'));
};

</script>

<template>
    <div class="px-4 sm:px-10 lg:px-20 py-6">
        <div class="flex xl:flex-row flex-col gap-8">
            <!-- Левая колонка - данные пользователя -->
            <div class="w-full xl:w-auto flex flex-col gap-4 xl:border-r-2 xl:pr-16 pb-4 xl:border-white max-xl:border-b-2 border-white max-xl:pb-10">
                <h1 class="text-2xl sm:text-3xl mx-auto mb-4 font-medium text-white">Ваши данные</h1>
                
                <!-- FIO -->
                <div>
                    <h3 class="text-xl sm:text-2xl text-white ml-3 mb-1 font-semibold">ФИО</h3>
                    <div class="text-lg sm:text-xl lg:text-2xl p-3 bg-white w-auto rounded-xl flex justify-center items-center shadow-md">
                        <p>{{ user.middlename }} {{ user.name }} {{ user.lastname }}</p>
                    </div>
                </div>
                
                <ProfileinfoLabel v-if="user.login" title="Логин" :info="user.login" />
                <ProfileinfoLabel title="Почта" :info="user.email" />
                
                <div v-if="user.number">
                    <h3 class="text-xl sm:text-2xl text-white ml-3 mb-1 font-semibold">Телефон</h3>
                    <div class="text-lg sm:text-xl lg:text-2xl p-3 px-10 bg-white xl:w-max w-full rounded-xl flex justify-center items-center shadow-md">
                        <p>{{ user.number }}</p>
                    </div>
                </div>
                
                <!-- role -->
                <div>
                    <h3 class="text-xl sm:text-2xl text-white ml-3 mb-1 font-semibold">Роль</h3>
                    <div class="text-lg sm:text-xl lg:text-2xl p-3 bg-white w-auto rounded-xl flex justify-center items-center shadow-md">
                        <p v-if="user.role === 'Investor'">Инвестор</p>
                        <p v-else-if="user.role === 'Organisator'">Организатор</p>
                        <p v-else>{{ user.role }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Правая колонка - избранные проекты для инвестора -->
            <div v-if="user.role === 'Investor'" class="w-full flex flex-col gap-8">
                <div class="flex items-center justify-between">
                    <h1 class="text-xl sm:text-2xl lg:text-3xl mx-auto font-medium text-white">
                        Избранные проекты
                    </h1>
                </div>

                <!-- Если нет избранных -->
                <div v-if="!favoriteProjects || favoriteProjects.length === 0" 
                     class="text-center py-12 bg-white/10 rounded-xl backdrop-blur-sm">
                    <p class="text-white text-lg sm:text-xl mt-4">У вас пока нет избранных проектов</p>
                </div>

                <!-- Список избранных -->
                <div v-else>
                    <div class="w-full grid md:grid-cols-2 grid-cols-1 gap-6 mx-auto overflow-y-auto max-h-[800px] px-2 pb-10">
                        <FavoriteCard 
                            v-for="project in favoriteProjects" 
                            :key="project.id"
                            :project="project"
                        />
                    </div>
                </div>
            </div>
            
            <!-- Правая колонка - мои проекты для организатора -->
            <div v-else-if="user.role === 'Organisator'" class="w-full flex flex-col gap-8">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-medium text-white">
                        Мои проекты
                    </h1>
                </div>

                <!-- Если нет проектов -->
                <div v-if="!myProjects || myProjects.length === 0" 
                     class="text-center py-12 bg-white/10 rounded-xl backdrop-blur-sm">
                    <p class="text-white text-lg sm:text-xl mt-4">У вас нету созданных проектов</p>
                </div>

                <!-- Список моих проектов -->
                <div v-else>
                    <div class="w-full grid md:grid-cols-2 grid-cols-1 gap-6 mx-auto overflow-y-auto max-h-[800px] px-2 pb-10">
                        <MyProject 
                            v-for="project in myProjects" 
                            :key="project.id"
                            :project="project"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Кастомный скроллбар */
.overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: rgba(255, 255, 255, 0.5) transparent;
}

.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: rgba(255, 255, 255, 0.5);
    border-radius: 20px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background-color: rgba(255, 255, 255, 0.7);
}
</style>