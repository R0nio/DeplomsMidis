<script setup>
import SearchInput from "@/Components/Main/SearchInput.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, computed, watch, onMounted } from "vue";
import { FwbSelect, FwbInput, FwbButton } from 'flowbite-vue';
import CardProjectList from "@/Components/Main/CardProjectList.vue";
import CardBox from "@/Components/Main/CardBox.vue";
import { usePage, router } from '@inertiajs/vue3';
// image import
import list from "../../images/list.png";
import cards from "../../images/Cards.png";
import TitlePage from "@/Layouts/TitlePage.vue";
import Paginate from "@/Components/Main/paginate.vue";

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
    textMuted: 'var(--color-text-muted)',
};

const shadows = {
    sm: 'var(--shadow-sm)',
    md: 'var(--shadow-md)',
    lg: 'var(--shadow-lg)',
};

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
const srAnnouncement = ref('');

// ==========================================
// ОБРАБОТКА КАТЕГОРИИ ИЗ URL ИЛИ SESSIONSTORAGE
// ==========================================
onMounted(() => {
    // 1. Проверяем параметры в URL
    const urlParams = new URLSearchParams(window.location.search);
    const categoryId = urlParams.get('category_id');
    const categoryName = urlParams.get('category_name');
    
    if (categoryId && categoryName) {
        selectedCategory.value = categoryName;
        const newUrl = window.location.pathname;
        window.history.replaceState({}, '', newUrl);
        srAnnouncement.value = `Применён фильтр: категория ${categoryName}`;
        setTimeout(() => { srAnnouncement.value = ''; }, 3000);
    } else {
        const savedCategory = sessionStorage.getItem('selectedCategory');
        if (savedCategory) {
            try {
                const category = JSON.parse(savedCategory);
                selectedCategory.value = category.name;
                sessionStorage.removeItem('selectedCategory');
                srAnnouncement.value = `Применён фильтр: категория ${category.name}`;
                setTimeout(() => { srAnnouncement.value = ''; }, 3000);
            } catch(e) {
                console.error('Error parsing saved category:', e);
            }
        }
    }
});

// ==========================================
// ПОЛЬЗОВАТЕЛЬ И ПРОЕКТЫ
// ==========================================
const userRole = computed(() => page.props.auth.user?.role);
const allProjects = computed(() => {
    return userRole.value === 'Admin' ? props.Adminprojects : props.projects;
});
const favoriteIds = computed(() => {
    return new Set(props.favorites.map(f => f.project_id));
});
const isProjectFavorited = (projectId) => {
    return favoriteIds.value.has(projectId);
};

// ==========================================
// UI СОСТОЯНИЕ
// ==========================================
const switcherViewCard = ref(false);
const currentPage = ref(1);
const itemsPerPage = 12;
const resetPage = () => { currentPage.value = 1; };

// ==========================================
// ФИЛЬТРЫ
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

let sumFromTimeout = null;
let sumBeforeTimeout = null;
let jobsFromTimeout = null;

watch(sumFromInput, (newValue) => {
    clearTimeout(sumFromTimeout);
    sumFromTimeout = setTimeout(() => { sumFrom.value = newValue; }, 500);
});
watch(sumBeforeInput, (newValue) => {
    clearTimeout(sumBeforeTimeout);
    sumBeforeTimeout = setTimeout(() => { sumBefore.value = newValue; }, 500);
});
watch(jobsFromInput, (newValue) => {
    clearTimeout(jobsFromTimeout);
    jobsFromTimeout = setTimeout(() => { jobsFrom.value = newValue; }, 500);
});

// ==========================================
// АЛГОРИТМЫ ПОИСКА (без изменений)
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
    const scored = allProjects.value.map(project => {
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
    return scored.filter(item => item.score > 0).sort((a, b) => b.score - a.score).slice(0, 8).map(item => item.project);
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
    const scored = allProjects.value.map(project => {
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
    return scored.filter(item => item.score > 0).sort((a, b) => b.score - a.score).slice(0, 8).map(item => item.project);
};

// ==========================================
// ДИНАМИЧЕСКИЕ ОПЦИИ ДЛЯ ФИЛЬТРОВ
// ==========================================
const industries = computed(() => {
    const unique = [...new Set(allProjects.value.map(p => p.activity).filter(Boolean))];
    return [{ value: '', name: 'Все деятельности' }, ...unique.map(item => ({ value: item, name: item }))];
});
const ownerships = computed(() => {
    const unique = [...new Set(allProjects.value.map(p => p.ownership).filter(Boolean))];
    return [{ value: '', name: 'Вся собственность' }, ...unique.map(item => ({ value: item, name: item }))];
});
const typeBuilds = computed(() => {
    const unique = [...new Set(allProjects.value.map(p => p.type_build).filter(Boolean))];
    return [{ value: '', name: 'Все виды' }, ...unique.map(item => ({ value: item, name: item }))];
});
const allCategories = computed(() => {
    const catsSet = new Set();
    allProjects.value.forEach(project => {
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
    const unique = [...new Set(allProjects.value.map(p => p.status).filter(Boolean))];
    return [{ value: '', name: 'Все статусы' }, ...unique.map(item => ({ value: item, name: item }))];
});

// ==========================================
// ФИЛЬТРАЦИЯ ПРОЕКТОВ
// ==========================================
const exactFilteredProjects = computed(() => {
    return allProjects.value.filter(project => {
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
        const filtered = allProjects.value.filter(project => {
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
        if (filtered.length > 0) return filtered.slice(0, 8);
        const byText = findSimilarProjects();
        return byText.length > 0 ? byText : allProjects.value.slice(0, 8);
    }
    const partial = findPartiallyMatchingProjects();
    return partial.length > 0 ? partial : allProjects.value.slice(0, 8);
});

const displayProjects = computed(() => {
    if (exactFilteredProjects.value.length > 0) return exactFilteredProjects.value;
    return similarProjects.value;
});

const paginatedProjects = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return displayProjects.value.slice(start, end);
});

const totalDisplayCount = computed(() => displayProjects.value.length);
const totalPages = computed(() => Math.ceil(totalDisplayCount.value / itemsPerPage));
const isShowingRecommendations = computed(() => exactFilteredProjects.value.length === 0 && similarProjects.value.length > 0);
const messageText = computed(() => {
    if (exactFilteredProjects.value.length === 0 && hasActiveFilters.value) {
        if (searchQuery.value) return `По запросу «${searchQuery.value}» ничего не найдено, но возможно вам понравится:`;
        return 'По заданным фильтрам ничего не найдено. Похожие проекты:';
    }
    return '';
});

watch(searchQuery, () => resetPage());
watch(sumFrom, () => resetPage());
watch(sumBefore, () => resetPage());
watch(jobsFrom, () => resetPage());
watch(selectedIndustry, () => resetPage());
watch(selectedOwnership, () => resetPage());
watch(selectedTypeBuild, () => resetPage());
watch(selectedCategory, () => resetPage());
watch(selectedDateRealise, () => resetPage());
watch(selectedStatus, () => resetPage());

const hasActiveFilters = computed(() => {
    return searchQuery.value || sumFrom.value || sumBefore.value || jobsFrom.value ||
        selectedIndustry.value || selectedOwnership.value || selectedTypeBuild.value ||
        selectedCategory.value || selectedDateRealise.value ||
        (userRole.value === 'Admin' && selectedStatus.value);
});

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
    resetPage();
    srAnnouncement.value = 'Все фильтры сброшены';
    setTimeout(() => { srAnnouncement.value = ''; }, 3000);
};

const applyFiltersManually = () => {
    sumFrom.value = sumFromInput.value;
    sumBefore.value = sumBeforeInput.value;
    jobsFrom.value = jobsFromInput.value;
    resetPage();
    srAnnouncement.value = 'Фильтры применены';
    setTimeout(() => { srAnnouncement.value = ''; }, 3000);
};

const goToPage = (page) => {
    if (page >= 1 && page <= totalPages.value && page !== currentPage.value) {
        currentPage.value = page;
        setTimeout(() => {
            const activeButton = document.querySelector('[aria-current="page"]');
            if (activeButton) activeButton.focus();
        }, 100);
    }
};
</script>

<template>
    <Head title="Проекты">
        <meta name="description" content="Каталог инвестиционных проектов. Фильтрация по деятельностям, сумме инвестиций, статусу и другим параметрам.">
    </Head>
    <AuthenticatedLayout>
        <template #header>
            <TitlePage value="Все проекты" />
        </template>
        
        <div class="mx-auto py-6 px-4 sm:px-10 lg:px-16 min-h-screen" :style="{ backgroundColor: colors.page }">
            <!-- Объявления для скринридера -->
            <div role="status" aria-live="polite" class="sr-only">{{ srAnnouncement }}</div>
            
            <!-- Фильтрация -->
            <section aria-labelledby="filters-title">
                <div class="w-full rounded-xl p-4 sm:p-6 mb-6 shadow-sm" :style="{ backgroundColor: colors.light, border: `1px solid ${colors.border}`, boxShadow: shadows.sm }">
                    <h2 id="filters-title" class="sr-only">Фильтры поиска проектов</h2>
                    
                    <div class="flex flex-col sm:flex-row items-center gap-3 mb-4">
                        <div class="flex-1 w-full">
                            <SearchInput v-model="searchQuery" placeholder="Введите название или адрес проекта..." class="w-full" />
                        </div>
                        <div class="flex gap-2 w-full sm:w-auto">
                            <!-- <fwb-button @click="applyFiltersManually" class="flex-1 sm:flex-none h-[46px] transition-colors" :style="{ backgroundColor: colors.brand, color: colors.white }" @mouseenter="$event.target.style.backgroundColor = colors.hover" @mouseleave="$event.target.style.backgroundColor = colors.accent">
                                Применить
                            </fwb-button> -->
                            <fwb-button @click="clearFilters" color="light" class="flex-1 sm:flex-none h-[46px] transition-colors" :style="{ backgroundColor: colors.light, color: colors.brand, border: `1px solid ${colors.border}` }" :class="{ 'opacity-50': !hasActiveFilters }" :disabled="!hasActiveFilters">
                                Сбросить
                            </fwb-button>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
                        <fwb-input v-model="sumFromInput" placeholder="Сумма от (₽)" type="number" class="w-full" />
                        <fwb-input v-model="sumBeforeInput" placeholder="Сумма до (₽)" type="number" class="w-full" />
                        <fwb-input v-model="jobsFromInput" placeholder="Рабочих мест от" type="number" class="w-full" />
                        <fwb-select v-model="selectedTypeBuild" :options="typeBuilds" placeholder="Вид строительства" class="w-full" />
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <fwb-select v-model="selectedCategory" :options="allCategories" placeholder="Категория" class="w-full" />
                        <fwb-select v-model="selectedDateRealise" :options="dateRealiseOptions" placeholder="Срок реализации" class="w-full" />
                        <fwb-select v-model="selectedIndustry" :options="industries" placeholder="Деятельность" class="w-full" />
                        <fwb-select v-model="selectedOwnership" :options="ownerships" placeholder="Собственность" class="w-full" />
                    </div>
                    
                    <div v-if="userRole === 'Admin'" class="mt-4">
                        <fwb-select v-model="selectedStatus" :options="statusOptions" placeholder="Статус проекта" class="w-full max-w-[250px]" />
                    </div>
                    
                    <!-- Активные фильтры (чипсы) -->
                    <div v-if="hasActiveFilters" class="mt-4 flex flex-wrap gap-2" role="list" aria-label="Активные фильтры">
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
                            Деятельность: {{ selectedIndustry }}
                            <button @click="selectedIndustry = ''" class="hover:text-accent transition-colors" aria-label="Удалить фильтр деятельности">×</button>
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
            </section>
            
            <!-- Проекты -->
            <section aria-labelledby="projects-title">
                <h2 id="projects-title" class="sr-only">Список проектов</h2>
                
                <div class="w-full border-2 rounded-xl h-max flex flex-col mt-6 shadow-lg" :style="{ borderColor: colors.border, boxShadow: shadows.lg, backgroundColor: colors.light }">
                    <div v-if="messageText" class="text-center py-6 px-4">
                        <h4 class="text-xl sm:text-2xl font-medium" :style="{ color: colors.accent }">{{ messageText }}</h4>
                        <p v-if="isShowingRecommendations" class="text-sm mt-2" :style="{ color: colors.textMuted }">Показаны проекты, которые могут вас заинтересовать</p>
                    </div>
                    
                    <div v-if="totalDisplayCount > 0" class="flex w-full justify-between items-center p-4 sm:p-6 flex-wrap gap-4">
                        <div class="text-lg sm:text-xl font-semibold" :style="{ color: colors.brand }">
                            {{ totalDisplayCount }} {{ totalDisplayCount === 1 ? 'проект' : totalDisplayCount < 5 ? 'проекта' : 'проектов' }}
                        </div>
                        <div class="flex gap-4 p-3 rounded-xl shadow-md" :style="{ backgroundColor: colors.white, boxShadow: shadows.md }">
                            <button :class="`rounded-xl cursor-pointer transition-opacity ${!switcherViewCard ? 'opacity-100' : 'opacity-40'}`" @click="switcherViewCard = false" :aria-pressed="!switcherViewCard" aria-label="Вид карточками">
                                <img :src="cards" alt="" class="w-8 h-8" aria-hidden="true">
                                <span class="sr-only">Вид карточками</span>
                            </button>
                            <button :class="`rounded-xl cursor-pointer transition-opacity ${switcherViewCard ? 'opacity-100' : 'opacity-40'}`" @click="switcherViewCard = true" :aria-pressed="switcherViewCard" aria-label="Вид списком">
                                <img :src="list" alt="" class="w-8 h-8" aria-hidden="true">
                                <span class="sr-only">Вид списком</span>
                            </button>
                        </div>
                    </div>
                    
                    <div v-if="totalDisplayCount === 0" class="text-center py-16 px-4">
                        <svg class="mx-auto h-24 w-24 opacity-50" :style="{ color: colors.brand }" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-4 text-2xl font-medium" :style="{ color: colors.brand }">Проекты не найдены</h3>
                        <fwb-button v-if="hasActiveFilters" @click="clearFilters" class="mt-4 transition-colors" :style="{ backgroundColor: colors.accent, color: colors.white }" @mouseenter="$event.target.style.backgroundColor = colors.hover" @mouseleave="$event.target.style.backgroundColor = colors.accent">Сбросить фильтры</fwb-button>
                    </div>
                    
                    <div v-else-if="switcherViewCard && paginatedProjects.length > 0" class="px-4 sm:px-8 pb-6">
                        <div class="w-full flex flex-col my-5 gap-4">
                            <CardProjectList v-for="project in paginatedProjects" :key="project.id" :project="project" :isFavorited="isProjectFavorited(project.id)" />
                        </div>
                    </div>
                    
                    <div v-else-if="!switcherViewCard && paginatedProjects.length > 0" class="px-4 sm:px-8 pb-6">
                        <div class="w-full grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 my-5 gap-6">
                            <CardBox v-for="project in paginatedProjects" :key="project.id" :project="project" :isFavorited="isProjectFavorited(project.id)" />
                        </div>
                    </div>
                    
                    <Paginate v-if="totalPages > 1 && paginatedProjects.length > 0" :current-page="currentPage" :total-pages="totalPages" @update:page="goToPage" />
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>