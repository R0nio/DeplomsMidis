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

// Цветовая схема
const colors = {
    bgDark: '#284139',
    bgLight: '#809076',
    accent: '#F8D794',
    border: '#886830',
    white: '#ffffff',
    white80: 'rgba(255, 255, 255, 0.8)',
};
</script>

<template>
    <div class="px-4 sm:px-10 lg:px-20 py-6">
        <div class="flex xl:flex-row flex-col gap-8">
            <!-- Левая колонка - данные пользователя -->
            <div 
                class="w-full xl:w-[400px] flex flex-col gap-4 xl:pr-8 pb-4 max-xl:pb-10"
                :class="'xl:border-r-2 max-xl:border-b-2'"
            >
                <h1 class="text-2xl sm:text-3xl mx-auto mb-4 font-medium" :style="{ color: colors.accent }">Ваши данные</h1>
                
                <!-- ФИО -->
                <div>
                    <h3 class="text-xl sm:text-2xl mb-1 font-semibold pl-3" :style="{ color: colors.accent }">ФИО</h3>
                    <div 
                        class="text-lg sm:text-xl lg:text-2xl p-3 w-full rounded-xl flex justify-center items-center"
                        :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                    >
                        <p class="">{{ user.middlename }} {{ user.name }} {{ user.lastname }}</p>
                    </div>
                </div>
                
                <ProfileinfoLabel v-if="user.login" title="Логин" :info="user.login" />
                <ProfileinfoLabel title="Почта" :info="user.email" />
                
                <div v-if="user.number">
                    <h3 class="text-xl sm:text-2xl mb-1 font-semibold pl-3" :style="{ color: colors.accent }">Телефон</h3>
                    <div 
                        class="text-lg sm:text-xl lg:text-2xl p-3 w-full rounded-xl flex justify-center items-center"
                        :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                    >
                        <p>{{ user.number }}</p>
                    </div>
                </div>
                
                <!-- role -->
                <div>
                    <h3 class="text-xl sm:text-2xl mb-1 font-semibold pl-3" :style="{ color: colors.accent }">Роль</h3>
                    <div 
                        class="text-lg sm:text-xl lg:text-2xl p-3 w-full rounded-xl flex justify-center items-center"
                        :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                    >
                        <p v-if="user.role === 'Investor'">Инвестор</p>
                        <p v-else-if="user.role === 'Organisator'">Организатор</p>
                        <p v-else>{{ user.role }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Правая колонка - избранные проекты для инвестора -->
            <div v-if="user.role === 'Investor'" class="w-full flex flex-col gap-8">
                <div class="flex items-center justify-between">
                    <h1 class="text-xl sm:text-2xl lg:text-3xl mx-auto font-medium" :style="{ color: colors.accent }">
                        Избранные проекты
                    </h1>
                </div>

                <!-- Если нет избранных -->
                <div v-if="!favoriteProjects || favoriteProjects.length === 0" 
                    class="text-center py-12 rounded-xl"
                    :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}` }"
                >
                    <p class="text-white text-lg sm:text-xl mt-4">У вас пока нет избранных проектов</p>
                </div>

                <!-- Список избранных -->
                <div v-else>
                    <div class="w-full grid md:grid-cols-2 2xl:grid-cols-3 grid-cols-1 gap-6 mx-auto overflow-y-auto max-h-[800px] px-2 pb-10">
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
                <div class="flex items-center justify-between">
                    <h1 class="text-xl sm:text-2xl lg:text-3xl mx-auto font-medium" :style="{ color: colors.accent }">
                        Мои проекты
                    </h1>
                </div>

                <!-- Если нет проектов -->
                <div v-if="!myProjects || myProjects.length === 0" 
                    class="text-center py-12 rounded-xl"
                    :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}` }"
                >
                    <p class="text-white text-lg sm:text-xl mt-4">У вас нет созданных проектов</p>
                </div>

                <!-- Список моих проектов -->
                <div v-else>
                    <div class="w-full grid md:grid-cols-2 2xl:grid-cols-3 grid-cols-1 gap-6 mx-auto overflow-y-auto max-h-[800px] px-2 pb-10">
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
