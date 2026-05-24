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
            <section aria-labelledby="user-info-heading" class="w-full xl:w-auto flex flex-col gap-4 xl:border-r-2 xl:pr-16 pb-4 xl:border-white max-xl:border-b-2 border-white max-xl:pb-10">
                <h2 id="user-info-heading" class="text-2xl sm:text-3xl mx-auto mb-4 font-medium text-white">Ваши данные</h2>
                
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
            </section>

            <!-- Правая колонка - избранные проекты для инвестора -->
            <section v-if="user.role === 'Investor'" aria-labelledby="favorite-projects-heading" class="w-full flex flex-col gap-8">
                <div class="flex items-center justify-between">
                    <h2 id="favorite-projects-heading" class="text-xl sm:text-2xl lg:text-3xl mx-auto font-medium text-white">
                        Избранные проекты
                    </h2>
                </div>

                <!-- Если нет избранных -->
                <div v-if="!favoriteProjects || favoriteProjects.length === 0"
                     class="text-center py-12 bg-white/10 rounded-xl backdrop-blur-sm" role="status">
                    <p class="text-white text-lg sm:text-xl mt-4">У вас пока нет избранных проектов</p>
                </div>

                <!-- Список избранных -->
                <ul v-else class="w-full grid md:grid-cols-2 grid-cols-1 gap-6 mx-auto overflow-y-auto max-h-[800px] px-2 pb-10 list-none m-0 p-0" aria-label="Избранные проекты">
                    <li v-for="project in favoriteProjects" :key="project.id">
                        <FavoriteCard :project="project" />
                    </li>
                </ul>
            </section>

            <!-- Правая колонка - мои проекты для организатора -->
            <section v-else-if="user.role === 'Organisator'" aria-labelledby="my-projects-heading" class="w-full flex flex-col gap-8">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <h2 id="my-projects-heading" class="text-xl sm:text-2xl lg:text-3xl font-medium text-white">
                        Мои проекты
                    </h2>
                </div>

                <!-- Если нет проектов -->
                <div v-if="!myProjects || myProjects.length === 0"
                     class="text-center py-12 bg-white/10 rounded-xl backdrop-blur-sm" role="status">
                    <p class="text-white text-lg sm:text-xl mt-4">У вас нет созданных проектов</p>
                </div>

                <!-- Список моих проектов -->
                <ul v-else class="w-full grid md:grid-cols-2 grid-cols-1 gap-6 mx-auto overflow-y-auto max-h-[800px] px-2 pb-10 list-none m-0 p-0" aria-label="Мои проекты">
                    <li v-for="project in myProjects" :key="project.id">
                        <MyProject :project="project" />
                    </li>
                </ul>
            </section>
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