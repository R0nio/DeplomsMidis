<script setup>
import SearchInput from "@/Components/Main/SearchInput.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { FwbSelect, FwbInput, FwbButton } from 'flowbite-vue';
import CardProjectList from "@/Components/Main/CardProjectList.vue";
import CardBox from "@/Components/Main/CardBox.vue";
import { usePage } from '@inertiajs/vue3';
// image import
import list from "../../images/list.png";
import cards from "../../images/Cards.png";

const props = defineProps({
    projects: {
        type: Array,
        default: () => []
    },
    Adminprojects: {
        type: Array,
        default: () => []
    },
    favorites: {
        type: Array,
        default: () => []
    }
});

const page = usePage();

// Роль пользователя
const userRole = computed(() => page.props.auth.user?.role);

// Определяем какой массив проектов использовать
const currentProjects = computed(() => {
    return userRole.value === 'Admin' ? props.Adminprojects : props.projects;
});

const favoriteIds = computed(() => {
    return new Set(props.favorites.map(f => f.project_id));
});

// Функция проверки, в избранном ли проект
const isProjectFavorited = (projectId) => {
    return favoriteIds.value.has(projectId);
};

const mainColor = "#8EB6FF";
const switcherViewCard = ref(false); // false = карточки, true = список

// Фильтры
const searchQuery = ref('');
const sumFrom = ref('');
const sumBefore = ref('');
const selectedIndustry = ref('');
const selectedStatus = ref('');
const selectedOwnership = ref('');
const selectedTypeBuild = ref('');

// Динамические опции для фильтров (извлекаем уникальные значения из проектов)
const industries = computed(() => {
    const unique = [...new Set(props.projects
        .map(p => p.activity)
        .filter(Boolean)
    )];
    return unique.map(item => ({ 
        value: item, 
        name: item 
    }));
});

const statuses = computed(() => {
    const unique = [...new Set(currentProjects.value 
        .map(p => p.status)
        .filter(Boolean)
    )];
    return unique.map(item => ({ value: item, name: item }));
});

const ownerships = computed(() => {
    const unique = [...new Set(props.projects
        .map(p => p.ownership)
        .filter(Boolean)
    )];
    return unique.map(item => ({ 
        value: item, 
        name: item 
    }));
});

const typeBuilds = computed(() => {
    const unique = [...new Set(props.projects
        .map(p => p.type_build)
        .filter(Boolean)
    )];
    return unique.map(item => ({ 
        value: item, 
        name: item 
    }));
});

// Фильтрация проектов
const filteredProjects = computed(() => {
    return currentProjects.value.filter(project => {
        // Поиск по названию
        if (searchQuery.value && 
            !project.title.toLowerCase().includes(searchQuery.value.toLowerCase())) {
            return false;
        }

        // Фильтр по сумме от
        if (sumFrom.value && 
            (!project.total_investment || project.total_investment < Number(sumFrom.value))) {
            return false;
        }

        // Фильтр по сумме до
        if (sumBefore.value && 
            (!project.total_investment || project.total_investment > Number(sumBefore.value))) {
            return false;
        }

        // Фильтр по отрасли (деятельности)
        if (selectedIndustry.value && project.activity !== selectedIndustry.value) {
            return false;
        }

        // Фильтр по статусу
        if (selectedStatus.value && project.status !== selectedStatus.value) {
            return false;
        }

        // Фильтр по собственности
        if (selectedOwnership.value && project.ownership !== selectedOwnership.value) {
            return false;
        }

        // Фильтр по виду строительства
        if (selectedTypeBuild.value && project.type_build !== selectedTypeBuild.value) {
            return false;
        }

        return true;
    });
});


// Количество проектов
const projectsCount = computed(() => filteredProjects.value.length);

// Очистка фильтров
const clearFilters = () => {
    searchQuery.value = '';
    sumFrom.value = '';
    sumBefore.value = '';
    selectedIndustry.value = '';
    selectedStatus.value = '';
    selectedOwnership.value = '';
    selectedTypeBuild.value = '';
};

// Обработка поиска
const handleSearch = (query) => {
    searchQuery.value = query;
};

// Проверка активности фильтров
const hasActiveFilters = computed(() => {
    return searchQuery.value || 
           sumFrom.value || 
           sumBefore.value || 
           selectedIndustry.value || 
           selectedStatus.value || 
           selectedOwnership.value || 
           selectedTypeBuild.value;
});
</script>

<template>
    <Head title="Проекты — InvestProject" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-2xl sm:text-3xl font-semibold leading-tight text-gray-900">
                Все проекты
            </h1>
        </template>

        <div class="px-4 sm:px-10 lg:px-20 py-6">
            <!-- Фильтрация -->
            <section aria-labelledby="filters-heading" class="w-full border-2 rounded-xl border-white h-full p-4 sm:p-6 shadow-lg" :style="{ backgroundColor: mainColor }">
                <h2 id="filters-heading" class="sr-only">Фильтры проектов</h2>
                <!-- Поиск -->
                <div class="flex items-center mb-4 flex-wrap gap-3">
                    <p class="text-white text-lg sm:text-xl font-semibold min-w-[250px]">
                        Поиск по названию проекта
                    </p>
                    <SearchInput @search="handleSearch" class="flex-1 min-w-[200px]"></SearchInput>
                </div>
                
                <!-- Остальные фильтры -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6  gap-3">
                    <!-- Отрасль -->
                    <fwb-select
                        v-model="selectedIndustry"
                        :options="industries"
                        placeholder="Выберите отрасль"
                        class="w-full"
                    />
                    
                    <!-- Сумма от -->
                    <fwb-input
                        v-model="sumFrom"
                        placeholder="Сумма от (₽)"
                        type="number"
                        class="w-full"
                    />
                    
                    <!-- Сумма до -->
                    <fwb-input
                        v-model="sumBefore"
                        placeholder="Сумма до (₽)"
                        type="number"
                        class="w-full"
                    />

                    <!-- Статус -->
                    <fwb-select
                        v-model="selectedStatus"
                        :options="statuses"
                        placeholder="Выберите статус"
                        class="w-full"
                    />
                    
                    <!-- Собственность -->
                    <fwb-select
                        v-model="selectedOwnership"
                        :options="ownerships"
                        placeholder="Выберите собственность"
                        class="w-full"
                    />
                    
                    <!-- Вид строительства -->
                    <fwb-select
                        v-model="selectedTypeBuild"
                        :options="typeBuilds"
                        placeholder="Выберите вид строительства"
                        class="w-full"
                    />
                    
                    <!-- Кнопка очистки -->
                    <fwb-button 
                        @click="clearFilters"
                        color="light"
                        class="w-full sm:col-span-2 lg:col-span-1 lg:-col-start-1"
                        :class="{ 'opacity-50': !hasActiveFilters }"
                        :disabled="!hasActiveFilters"
                    >
                        Очистить фильтры
                    </fwb-button>
                </div>

                <!-- Активные фильтры (теги) -->
                <div v-if="hasActiveFilters" class="mt-4 flex flex-wrap gap-2" role="group" aria-label="Активные фильтры">
                    <span class="text-white text-sm font-semibold mr-2">Активные фильтры:</span>

                    <span v-if="searchQuery" class="bg-white text-gray-900 px-3 py-1 rounded-full text-sm flex items-center gap-2">
                        Поиск: "{{ searchQuery }}"
                        <button type="button" @click="searchQuery = ''" class="hover:text-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 focus-visible:ring-blue-700 rounded" aria-label="Сбросить фильтр поиска"><span aria-hidden="true">×</span></button>
                    </span>

                    <span v-if="sumFrom" class="bg-white text-gray-900 px-3 py-1 rounded-full text-sm flex items-center gap-2">
                        От: {{ Number(sumFrom).toLocaleString('ru-RU') }} ₽
                        <button type="button" @click="sumFrom = ''" class="hover:text-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 focus-visible:ring-blue-700 rounded" aria-label="Сбросить фильтр «сумма от»"><span aria-hidden="true">×</span></button>
                    </span>

                    <span v-if="sumBefore" class="bg-white text-gray-900 px-3 py-1 rounded-full text-sm flex items-center gap-2">
                        До: {{ Number(sumBefore).toLocaleString('ru-RU') }} ₽
                        <button type="button" @click="sumBefore = ''" class="hover:text-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 focus-visible:ring-blue-700 rounded" aria-label="Сбросить фильтр «сумма до»"><span aria-hidden="true">×</span></button>
                    </span>

                    <span v-if="selectedIndustry" class="bg-white text-gray-900 px-3 py-1 rounded-full text-sm flex items-center gap-2">
                        Отрасль: {{ selectedIndustry }}
                        <button type="button" @click="selectedIndustry = ''" class="hover:text-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 focus-visible:ring-blue-700 rounded" aria-label="Сбросить фильтр «отрасль»"><span aria-hidden="true">×</span></button>
                    </span>

                    <span v-if="selectedStatus" class="bg-white text-gray-900 px-3 py-1 rounded-full text-sm flex items-center gap-2">
                        Статус: {{ selectedStatus }}
                        <button type="button" @click="selectedStatus = ''" class="hover:text-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 focus-visible:ring-blue-700 rounded" aria-label="Сбросить фильтр «статус»"><span aria-hidden="true">×</span></button>
                    </span>

                    <span v-if="selectedOwnership" class="bg-white text-gray-900 px-3 py-1 rounded-full text-sm flex items-center gap-2">
                        Собственность: {{ selectedOwnership }}
                        <button type="button" @click="selectedOwnership = ''" class="hover:text-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 focus-visible:ring-blue-700 rounded" aria-label="Сбросить фильтр «собственность»"><span aria-hidden="true">×</span></button>
                    </span>

                    <span v-if="selectedTypeBuild" class="bg-white text-gray-900 px-3 py-1 rounded-full text-sm flex items-center gap-2">
                        Вид: {{ selectedTypeBuild }}
                        <button type="button" @click="selectedTypeBuild = ''" class="hover:text-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 focus-visible:ring-blue-700 rounded" aria-label="Сбросить фильтр «вид строительства»"><span aria-hidden="true">×</span></button>
                    </span>
                </div>
            </section>
            
            <!-- Проекты -->
            <section aria-labelledby="projects-results-heading" class="w-full border-2 rounded-xl border-white h-max flex flex-col mt-6 shadow-lg" :style="{ backgroundColor: mainColor }">
                <h2 id="projects-results-heading" class="sr-only">Результаты поиска проектов</h2>
                <p class="sr-only" aria-live="polite">Найдено проектов: {{ projectsCount }}</p>
                <!-- Переключатель вида и счетчик -->
                <div class="flex w-full justify-between items-center p-4 sm:p-6 flex-wrap gap-4">
                    <!-- Счетчик проектов -->
                    <div class="text-white text-lg sm:text-xl font-semibold">

                    </div>

                    <!-- Переключатель вида -->
                    <div class="flex bg-white gap-4 p-3 rounded-xl shadow-md" role="group" aria-label="Переключатель вида проектов">
                        <button
                            type="button"
                            :class="`rounded-xl cursor-pointer transition-opacity focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-700 ${!switcherViewCard ? 'opacity-100' : 'opacity-40'}`"
                            @click="switcherViewCard = false"
                            :aria-pressed="!switcherViewCard ? 'true' : 'false'"
                            aria-label="Показать проекты карточками"
                        >
                            <img :src="cards" alt="" aria-hidden="true" class="w-8 h-8">
                        </button>
                        <button
                            type="button"
                            :class="`rounded-xl cursor-pointer transition-opacity focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-700 ${switcherViewCard ? 'opacity-100' : 'opacity-40'}`"
                            @click="switcherViewCard = true"
                            :aria-pressed="switcherViewCard ? 'true' : 'false'"
                            aria-label="Показать проекты списком"
                        >
                            <img :src="list" alt="" aria-hidden="true" class="w-8 h-8">
                        </button>
                    </div>
                </div>

                
                <!-- Если нет проектов -->
                <div v-if="projectsCount === 0" class="text-center py-16 px-4 " role="status">
                    <svg class="mx-auto h-24 w-24 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-4 text-2xl font-medium text-white">Проекты не найдены</h3>
                    <fwb-button
                        v-if="hasActiveFilters"
                        @click="clearFilters"
                        class="mt-4"
                        color="light"
                    >
                        Сбросить фильтры
                    </fwb-button>
                </div>
                <!-- Вид списком -->
                <div v-else-if="switcherViewCard" class="px-4 sm:px-8">
                    <ul :class="`w-full flex flex-col my-5 gap-4 list-none m-0 p-0 ${projectsCount >= 8 ? 'overflow-y-auto max-h-[80vh]' : ''}`" aria-label="Список проектов">
                        <li v-for="project in filteredProjects" :key="project.id">
                            <CardProjectList
                                :project="project"
                                :isFavorited="isProjectFavorited(project.id)"
                            />
                        </li>
                    </ul>
                </div>

                <!-- Вид карточками -->
                <div v-else class="px-4 sm:px-8">
                    <ul :class="`w-full grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 my-5 gap-6 list-none m-0 p-0 ${projectsCount >= 8 ? 'overflow-y-auto max-h-[80vh]' : ''}`" aria-label="Карточки проектов">
                        <li v-for="project in filteredProjects" :key="project.id">
                            <CardBox
                                :project="project"
                                :isFavorited="isProjectFavorited(project.id)"
                            />
                        </li>
                    </ul>
                </div>

            </section>
        </div>
    </AuthenticatedLayout>
</template> 