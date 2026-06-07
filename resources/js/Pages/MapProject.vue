<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, usePage } from "@inertiajs/vue3";
import GoogleMapExample from "@/Components/Main/GoogleMapExample.vue";
import { ref, computed, watch, nextTick, onMounted } from 'vue';
import TitlePage from "@/Layouts/TitlePage.vue";
import { FwbSelect, FwbInput, FwbButton } from 'flowbite-vue';
import SearchInput from "@/Components/Main/SearchInput.vue";

// ===== ЦВЕТА И СТИЛИ КОМПОНЕНТА =====
const colors = {
    page: 'var(--color-page)',
    light: 'var(--color-light)',
    brand: 'var(--color-brand)',
    accent: 'var(--color-accent)',
    hover: 'var(--color-hover)',
    border: 'var(--color-border)',
    white: 'var(--color-white)',
    text: 'var(--color-text)',
};

const shadows = {
    sm: 'var(--shadow-sm)',
};

const props = defineProps({
    projects: {
        type: Array,
        default: () => []
    }
});

const page = usePage();
const userRole = computed(() => page.props.auth?.user?.role);

// ==========================================
// ACCESSIBILITY (A11Y)
// ==========================================
const isFiltering = ref(false);
const srAnnouncement = ref('');

const announceResults = (count) => {
    const word = count === 1 ? 'проект' : count < 5 ? 'проекта' : 'проектов';
    srAnnouncement.value = `На карте показано ${count} ${word}`;
    setTimeout(() => { srAnnouncement.value = ''; }, 3000);
};

// ==========================================
// ФИЛЬТРЫ С АВТОПРИМЕНИЕМ
// ==========================================
const searchQuery = ref('');
const sumFrom = ref('');
const sumBefore = ref('');
const jobsFrom = ref('');
const selectedIndustry = ref('');
const selectedOwnership = ref('');
const selectedTypeBuild = ref('');
const selectedCategory = ref('');
const selectedDateRealise = ref('');
const selectedStatus = ref('');

const sumFromInput = ref('');
const sumBeforeInput = ref('');
const jobsFromInput = ref('');

const applyCategoryFromRoute = () => {
    const savedCategory = sessionStorage.getItem('selectedCategory');
    if (savedCategory) {
        try {
            const category = JSON.parse(savedCategory);
            selectedCategory.value = category.name;
            sessionStorage.removeItem('selectedCategory');
            srAnnouncement.value = `Применён фильтр: категория ${category.name}`;
            setTimeout(() => { srAnnouncement.value = ''; }, 3000);
        } catch (e) {
            console.error('Ошибка парсинга категории из sessionStorage', e);
        }
    }
};

onMounted(() => { applyCategoryFromRoute(); });

// ==========================================
// DEBOUNCE ДЛЯ ЧИСЛОВЫХ ПОЛЕЙ
// ==========================================
let sumFromTimeout = null, sumBeforeTimeout = null, jobsFromTimeout = null;

watch(sumFromInput, (newValue) => {
    clearTimeout(sumFromTimeout);
    isFiltering.value = true;
    sumFromTimeout = setTimeout(() => { sumFrom.value = newValue; isFiltering.value = false; }, 500);
});
watch(sumBeforeInput, (newValue) => {
    clearTimeout(sumBeforeTimeout);
    isFiltering.value = true;
    sumBeforeTimeout = setTimeout(() => { sumBefore.value = newValue; isFiltering.value = false; }, 500);
});
watch(jobsFromInput, (newValue) => {
    clearTimeout(jobsFromTimeout);
    isFiltering.value = true;
    jobsFromTimeout = setTimeout(() => { jobsFrom.value = newValue; isFiltering.value = false; }, 500);
});

// ==========================================
// АЛГОРИТМЫ УМНОГО ПОИСКА (без изменений)
// ==========================================
const getLevenshteinDistance = (a, b) => {
    if (a.length === 0) return b.length;
    if (b.length === 0) return a.length;
    const matrix = [];
    for (let i = 0; i <= b.length; i++) matrix[i] = [i];
    for (let j = 0; j <= a.length; j++) matrix[0][j] = j;
    for (let i = 1; i <= b.length; i++) {
        for (let j = 1; j <= a.length; j++) {
            const cost = a[j - 1] === b[i - 1] ? 0 : 1;
            matrix[i][j] = Math.min(matrix[i - 1][j] + 1, matrix[i][j - 1] + 1, matrix[i - 1][j - 1] + cost);
        }
    }
    return matrix[b.length][a.length];
};

const isSimilarWord = (word1, word2) => {
    if (word1 === word2) return true;
    if (word1.includes(word2) || word2.includes(word1)) return true;
    const maxLength = Math.max(word1.length, word2.length);
    const distance = getLevenshteinDistance(word1, word2);
    const similarity = (maxLength - distance) / maxLength;
    return similarity > 0.7;
};

const matchesSearch = (project, searchTerm) => {
    if (!searchTerm) return true;
    const searchLower = searchTerm.toLowerCase().trim();
    if (searchLower === '') return true;
    const title = (project.title || '').toLowerCase();
    const address = (project.address || '').toLowerCase();
    if (title.includes(searchLower)) return true;
    if (address.includes(searchLower)) return true;
    const titleWords = title.split(/\s+/);
    const addressWords = address.split(/\s+/);
    const searchWords = searchLower.split(/\s+/);
    for (const searchWord of searchWords) {
        if (searchWord.length < 3) continue;
        for (const titleWord of titleWords) {
            if (titleWord.length < 3) continue;
            if (isSimilarWord(titleWord, searchWord)) return true;
        }
        for (const addressWord of addressWords) {
            if (addressWord.length < 3) continue;
            if (isSimilarWord(addressWord, searchWord)) return true;
        }
    }
    return false;
};

const findSimilarProjects = () => {
    if (!searchQuery.value) return [];
    const searchLower = searchQuery.value.toLowerCase().trim();
    if (searchLower === '') return [];
    const scored = props.projects.map(project => {
        let score = 0;
        const title = (project.title || '').toLowerCase();
        const address = (project.address || '').toLowerCase();
        const titleWords = title.split(/\s+/);
        const addressWords = address.split(/\s+/);
        const searchWords = searchLower.split(/\s+/);
        for (const searchWord of searchWords) {
            if (searchWord.length < 2) continue;
            if (title.includes(searchWord)) score += 100;
            else if (address.includes(searchWord)) score += 80;
            else {
                for (const titleWord of titleWords) {
                    if (titleWord.length < 3) continue;
                    if (isSimilarWord(titleWord, searchWord)) { score += 50; break; }
                }
                for (const addressWord of addressWords) {
                    if (addressWord.length < 3) continue;
                    if (isSimilarWord(addressWord, searchWord)) { score += 40; break; }
                }
            }
        }
        return { project, score };
    });
    return scored.filter(item => item.score > 0).sort((a, b) => b.score - a.score).map(item => item.project);
};

const matchesCategory = (project) => {
    if (!selectedCategory.value) return true;
    try {
        const projectCats = Array.isArray(project.category) ? project.category : JSON.parse(project.category || '[]');
        return projectCats.includes(selectedCategory.value);
    } catch(e) { return false; }
};

const matchesDateRealise = (project) => {
    if (!selectedDateRealise.value) return true;
    const months = Number(project.number_date_realise);
    switch (selectedDateRealise.value) {
        case '0-12': return months <= 12;
        case '12-24': return months >= 12 && months <= 24;
        case '24-36': return months >= 24 && months <= 36;
        case '36+': return months > 36;
        default: return true;
    }
};

const findPartiallyMatchingProjects = () => {
    const scored = props.projects.map(project => {
        let score = 0;
        if (selectedIndustry.value && project.activity === selectedIndustry.value) score += 3;
        if (selectedOwnership.value && project.ownership === selectedOwnership.value) score += 2;
        if (selectedTypeBuild.value && project.type_build === selectedTypeBuild.value) score += 2;
        if (selectedCategory.value && matchesCategory(project)) score += 2;
        if (selectedStatus.value && project.status === selectedStatus.value) score += 1;
        if (sumFrom.value) {
            const diff = project.total_investment - Number(sumFrom.value);
            if (diff >= 0) score += 3;
            else if (Math.abs(diff) / Number(sumFrom.value) < 0.3) score += 1;
        }
        if (sumBefore.value) {
            const diff = Number(sumBefore.value) - project.total_investment;
            if (diff >= 0) score += 3;
            else if (Math.abs(diff) / Number(sumBefore.value) < 0.3) score += 1;
        }
        if (jobsFrom.value) {
            if (project.count_new_job >= Number(jobsFrom.value)) score += 2;
            else if (project.count_new_job >= Number(jobsFrom.value) * 0.7) score += 1;
        }
        return { project, score };
    });
    return scored.filter(item => item.score > 0).sort((a, b) => b.score - a.score).map(item => item.project);
};

// ==========================================
// ДИНАМИЧЕСКИЕ ОПЦИИ ДЛЯ ФИЛЬТРОВ
// ==========================================
const industries = computed(() => {
    const unique = [...new Set(props.projects.map(p => p.activity).filter(Boolean))];
    return [{ value: '', name: 'Все отрасли' }, ...unique.map(item => ({ value: item, name: item }))];
});
const ownerships = computed(() => {
    const unique = [...new Set(props.projects.map(p => p.ownership).filter(Boolean))];
    return [{ value: '', name: 'Вся собственность' }, ...unique.map(item => ({ value: item, name: item }))];
});
const typeBuilds = computed(() => {
    const unique = [...new Set(props.projects.map(p => p.type_build).filter(Boolean))];
    return [{ value: '', name: 'Все виды' }, ...unique.map(item => ({ value: item, name: item }))];
});
const allCategories = computed(() => {
    const catsSet = new Set();
    props.projects.forEach(project => {
        if (project.category) {
            try {
                const cats = Array.isArray(project.category) ? project.category : JSON.parse(project.category);
                cats.forEach(cat => catsSet.add(cat));
            } catch(e) {}
        }
    });
    return [{ value: '', name: 'Все категории' }, ...Array.from(catsSet).map(cat => ({ value: cat, name: cat }))];
});
const dateRealiseOptions = [
    { value: '', name: 'Все сроки' },
    { value: '0-12', name: 'до 12 месяцев' },
    { value: '12-24', name: '12-24 месяца' },
    { value: '24-36', name: '24-36 месяцев' },
    { value: '36+', name: 'более 36 месяцев' }
];
const statusOptions = computed(() => {
    const unique = [...new Set(props.projects.map(p => p.status).filter(Boolean))];
    return [{ value: '', name: 'Все статусы' }, ...unique.map(item => ({ value: item, name: item }))];
});

// ==========================================
// ФИЛЬТРАЦИЯ ПРОЕКТОВ
// ==========================================
const exactFilteredProjects = computed(() => {
    return props.projects.filter(project => {
        if (!matchesSearch(project, searchQuery.value)) return false;
        if (userRole.value === 'Admin' && selectedStatus.value && project.status !== selectedStatus.value) return false;
        if (sumFrom.value && (!project.total_investment || project.total_investment < Number(sumFrom.value))) return false;
        if (sumBefore.value && (!project.total_investment || project.total_investment > Number(sumBefore.value))) return false;
        if (jobsFrom.value && (!project.count_new_job || project.count_new_job < Number(jobsFrom.value))) return false;
        if (selectedIndustry.value && project.activity !== selectedIndustry.value) return false;
        if (selectedOwnership.value && project.ownership !== selectedOwnership.value) return false;
        if (selectedTypeBuild.value && project.type_build !== selectedTypeBuild.value) return false;
        if (!matchesCategory(project)) return false;
        if (!matchesDateRealise(project)) return false;
        return true;
    });
});

const similarProjects = computed(() => {
    if (exactFilteredProjects.value.length > 0) return [];
    if (searchQuery.value) {
        const filtered = props.projects.filter(project => {
            if (userRole.value === 'Admin' && selectedStatus.value && project.status !== selectedStatus.value) return false;
            if (sumFrom.value && (!project.total_investment || project.total_investment < Number(sumFrom.value))) return false;
            if (sumBefore.value && (!project.total_investment || project.total_investment > Number(sumBefore.value))) return false;
            if (jobsFrom.value && (!project.count_new_job || project.count_new_job < Number(jobsFrom.value))) return false;
            if (selectedIndustry.value && project.activity !== selectedIndustry.value) return false;
            if (selectedOwnership.value && project.ownership !== selectedOwnership.value) return false;
            if (selectedTypeBuild.value && project.type_build !== selectedTypeBuild.value) return false;
            if (!matchesCategory(project)) return false;
            if (!matchesDateRealise(project)) return false;
            return true;
        });
        if (filtered.length > 0) return filtered;
        const byText = findSimilarProjects();
        return byText.length > 0 ? byText : props.projects;
    }
    const partial = findPartiallyMatchingProjects();
    return partial.length > 0 ? partial : props.projects;
});

const displayProjects = computed(() => {
    if (exactFilteredProjects.value.length > 0) return exactFilteredProjects.value;
    return similarProjects.value;
});

const isShowingRecommendations = computed(() => exactFilteredProjects.value.length === 0 && similarProjects.value.length > 0);
const messageText = computed(() => {
    if (exactFilteredProjects.value.length === 0 && hasActiveFilters.value) {
        if (searchQuery.value) return `По запросу «${searchQuery.value}» ничего не найдено, показаны похожие проекты`;
        return 'По заданным фильтрам ничего не найдено. Показаны похожие проекты';
    }
    return '';
});

const mapCenter = computed(() => {
    const projects = displayProjects.value.filter(p => p.latitude && p.longitude);
    if (projects.length === 0) return { lat: 55.160278, lng: 61.402457 };
    const avgLat = projects.reduce((sum, p) => sum + parseFloat(p.latitude), 0) / projects.length;
    const avgLng = projects.reduce((sum, p) => sum + parseFloat(p.longitude), 0) / projects.length;
    return { lat: avgLat, lng: avgLng };
});

const projectsCount = computed(() => displayProjects.value.length);
const hasActiveFilters = computed(() => {
    return searchQuery.value || sumFrom.value || sumBefore.value || jobsFrom.value ||
        selectedIndustry.value || selectedOwnership.value || selectedTypeBuild.value ||
        selectedCategory.value || selectedDateRealise.value ||
        (userRole.value === 'Admin' && selectedStatus.value);
});

const handleFilterChange = () => { nextTick(() => announceResults(projectsCount.value)); };
watch(searchQuery, handleFilterChange);
watch(sumFrom, handleFilterChange);
watch(sumBefore, handleFilterChange);
watch(jobsFrom, handleFilterChange);
watch(selectedIndustry, handleFilterChange);
watch(selectedOwnership, handleFilterChange);
watch(selectedTypeBuild, handleFilterChange);
watch(selectedCategory, handleFilterChange);
watch(selectedDateRealise, handleFilterChange);
watch(selectedStatus, handleFilterChange);

const clearFilters = () => {
    searchQuery.value = '';
    sumFrom.value = '';
    sumBefore.value = '';
    jobsFrom.value = '';
    sumFromInput.value = '';
    sumBeforeInput.value = '';
    jobsFromInput.value = '';
    selectedIndustry.value = '';
    selectedOwnership.value = '';
    selectedTypeBuild.value = '';
    selectedCategory.value = '';
    selectedDateRealise.value = '';
    if (userRole.value === 'Admin') selectedStatus.value = '';
    srAnnouncement.value = 'Фильтры сброшены';
    setTimeout(() => { srAnnouncement.value = ''; }, 2000);
};

const applyFiltersManually = () => {
    sumFrom.value = sumFromInput.value;
    sumBefore.value = sumBeforeInput.value;
    jobsFrom.value = jobsFromInput.value;
    srAnnouncement.value = 'Фильтры применены';
    setTimeout(() => { srAnnouncement.value = ''; }, 2000);
};
</script>

<template>
    <Head title="Карта проектов">
        <meta name="description" content="Интерактивная карта инвестиционных проектов с умной фильтрацией">
    </Head>

    <AuthenticatedLayout>
        <template #header>
            <TitlePage id="Main" value="Карта проектов" />
        </template>

        <div class="mx-auto py-6 px-4 sm:px-10 lg:px-16 min-h-screen" :style="{ backgroundColor: colors.page }">
            <!-- Скрытая область для объявлений screen reader -->
            <div role="status" aria-live="polite" aria-atomic="true" class="sr-only">{{ srAnnouncement }}</div>

            <section aria-labelledby="map-title">
                <h2 id="map-title" class="sr-only">Карта инвестиционных проектов с фильтрацией</h2>
                
                <!-- Фильтрация -->
                <div class="w-full rounded-xl p-4 sm:p-6 mb-6 shadow-sm" :style="{ backgroundColor: colors.light, border: `1px solid ${colors.border}`, boxShadow: shadows.sm }" role="search" aria-label="Фильтрация проектов на карте" :aria-busy="isFiltering">
                    
                    <!-- 1 СТРОКА: Поиск + кнопки -->
                    <div class="flex flex-col sm:flex-row items-center gap-3 mb-4">
                        <div class="flex-1 w-full">
                            <SearchInput v-model="searchQuery" placeholder="Введите название или адрес проекта..." class="w-full" />
                        </div>
                        <div class="flex gap-2 w-full sm:w-auto">
                            <fwb-button @click="applyFiltersManually" class="flex-1 sm:flex-none h-[46px] transition-colors" :style="{ backgroundColor: colors.accent, color: colors.white }" @mouseenter="$event.target.style.backgroundColor = colors.hover" @mouseleave="$event.target.style.backgroundColor = colors.accent">Применить</fwb-button>
                            <fwb-button @click="clearFilters" color="light" class="flex-1 sm:flex-none h-[46px] transition-colors" :style="{ backgroundColor: colors.light, color: colors.brand, border: `1px solid ${colors.border}` }" :class="{ 'opacity-50': !hasActiveFilters }" :disabled="!hasActiveFilters">Сбросить</fwb-button>
                        </div>
                    </div>

                    <!-- 2 СТРОКА: Числовые фильтры -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
                        <fwb-input v-model="sumFromInput" placeholder="Сумма от (₽)" type="number" class="w-full" />
                        <fwb-input v-model="sumBeforeInput" placeholder="Сумма до (₽)" type="number" class="w-full" />
                        <fwb-input v-model="jobsFromInput" placeholder="Рабочих мест от" type="number" class="w-full" />
                        <fwb-select v-model="selectedTypeBuild" :options="typeBuilds" placeholder="Вид строительства" class="w-full" />
                    </div>

                    <!-- 3 СТРОКА: Категориальные фильтры -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
                        <fwb-select v-model="selectedCategory" :options="allCategories" placeholder="Категория" class="w-full" />
                        <fwb-select v-model="selectedDateRealise" :options="dateRealiseOptions" placeholder="Срок реализации" class="w-full" />
                        <fwb-select v-model="selectedIndustry" :options="industries" placeholder="Отрасль" class="w-full" />
                        <fwb-select v-model="selectedOwnership" :options="ownerships" placeholder="Собственность" class="w-full" />
                    </div>

                    <!-- 4 СТРОКА: Статус (только для админа) -->
                    <div v-if="userRole === 'Admin'" class="mb-4">
                        <fwb-select v-model="selectedStatus" :options="statusOptions" placeholder="Статус проекта" class="w-full max-w-[250px]" />
                    </div>

                    <!-- Сообщение о похожих проектах -->
                    <div v-if="messageText" class="mb-4 p-3 rounded-lg text-center shadow-sm" :style="{ backgroundColor: colors.brand }" role="status" aria-live="polite">
                        <p class="text-lg font-medium" :style="{ color: colors.accent }">{{ messageText }}</p>
                    </div>

                    <!-- Активные фильтры (чипсы) -->
                    <div v-if="hasActiveFilters" class="flex flex-wrap gap-2" role="list" aria-label="Активные фильтры">
                        <span class="text-sm font-semibold mr-2" :style="{ color: colors.brand }" aria-hidden="true">Активные фильтры:</span>
                        
                        <span v-if="searchQuery" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.brand, color: colors.white, border: `1px solid ${colors.accent}` }" role="listitem">
                            Поиск: "{{ searchQuery }}"
                            <button @click="searchQuery = ''" class="hover:text-accent transition-colors" aria-label="Удалить фильтр поиска">×</button>
                        </span>
                        <span v-if="selectedCategory" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.brand, color: colors.white, border: `1px solid ${colors.accent}` }" role="listitem">
                            Категория: {{ selectedCategory }}
                            <button @click="selectedCategory = ''" class="hover:text-accent transition-colors" aria-label="Удалить фильтр категории">×</button>
                        </span>
                        <span v-if="selectedDateRealise" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.brand, color: colors.white, border: `1px solid ${colors.accent}` }" role="listitem">
                            Срок: {{ dateRealiseOptions.find(o => o.value === selectedDateRealise)?.name }}
                            <button @click="selectedDateRealise = ''" class="hover:text-accent transition-colors" aria-label="Удалить фильтр срока">×</button>
                        </span>
                        <span v-if="selectedStatus && userRole === 'Admin'" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.brand, color: colors.white, border: `1px solid ${colors.accent}` }" role="listitem">
                            Статус: {{ selectedStatus }}
                            <button @click="selectedStatus = ''" class="hover:text-accent transition-colors" aria-label="Удалить фильтр статуса">×</button>
                        </span>
                        <span v-if="sumFrom" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.brand, color: colors.white, border: `1px solid ${colors.accent}` }" role="listitem">
                            Сумма от: {{ Number(sumFrom).toLocaleString('ru-RU') }} ₽
                            <button @click="sumFrom = ''; sumFromInput = ''" class="hover:text-accent transition-colors" aria-label="Удалить фильтр минимальной суммы">×</button>
                        </span>
                        <span v-if="sumBefore" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.brand, color: colors.white, border: `1px solid ${colors.accent}` }" role="listitem">
                            Сумма до: {{ Number(sumBefore).toLocaleString('ru-RU') }} ₽
                            <button @click="sumBefore = ''; sumBeforeInput = ''" class="hover:text-accent transition-colors" aria-label="Удалить фильтр максимальной суммы">×</button>
                        </span>
                        <span v-if="jobsFrom" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.brand, color: colors.white, border: `1px solid ${colors.accent}` }" role="listitem">
                            Рабочих мест от: {{ jobsFrom }}
                            <button @click="jobsFrom = ''; jobsFromInput = ''" class="hover:text-accent transition-colors" aria-label="Удалить фильтр рабочих мест">×</button>
                        </span>
                        <span v-if="selectedIndustry" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.brand, color: colors.white, border: `1px solid ${colors.accent}` }" role="listitem">
                            Отрасль: {{ selectedIndustry }}
                            <button @click="selectedIndustry = ''" class="hover:text-accent transition-colors" aria-label="Удалить фильтр отрасли">×</button>
                        </span>
                        <span v-if="selectedOwnership" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.brand, color: colors.white, border: `1px solid ${colors.accent}` }" role="listitem">
                            Собственность: {{ selectedOwnership }}
                            <button @click="selectedOwnership = ''" class="hover:text-accent transition-colors" aria-label="Удалить фильтр собственности">×</button>
                        </span>
                        <span v-if="selectedTypeBuild" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.brand, color: colors.white, border: `1px solid ${colors.accent}` }" role="listitem">
                            Вид строительства: {{ selectedTypeBuild }}
                            <button @click="selectedTypeBuild = ''" class="hover:text-accent transition-colors" aria-label="Удалить фильтр вида строительства">×</button>
                        </span>
                    </div>
                </div>
                
                <!-- Карта -->
                <div class="rounded-xl overflow-hidden shadow-lg" role="region" aria-label="Интерактивная карта с метками проектов">
                    <GoogleMapExample :projects="displayProjects" :center="mapCenter" />
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>