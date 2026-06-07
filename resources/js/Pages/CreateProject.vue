<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref } from 'vue';
import TitlePage from '@/Layouts/TitlePage.vue';
// Массивы
const categories = ref(['']);
const expenses = ref([{ item_name: '', amount: '' }]);
// НОВОЕ: Доходы и расходы по годам
const revenues = ref([{ year: 1, amount: '' }]); // Доходы по годам
const yearlyExpenses = ref([{ year: 1, amount: '' }]); // Расходы по годам
const photoPreviews = ref([]);
const form = useForm({
    name: '',
    shotr_descr: '',
    full_descr: '',
    categories: [],
    fotos: [],
    ownShip: '',
    activity: '',
    type_building: '',
    addres: '',
    latitude: '',
    longitude: '',
    total_investment: '',
    number_date_realise: '',
    count_new_job: '',
    collected_total_investment: '',
    revenues: [], // НОВОЕ
    yearly_expenses: [], // НОВОЕ
    expenses: [],
});
// ========== КАТЕГОРИИ ==========
const addCategory = () => {
    if (categories.value.length < 4) {
        categories.value.push('');
    }
};
const removeCategory = (index) => {
    if (categories.value.length > 1) {
        categories.value.splice(index, 1);
    }
};
// ========== СТРУКТУРА ТРАТ ==========
const addExpense = () => {
    expenses.value.push({ item_name: '', amount: '' });
};
const removeExpense = (index) => {
    if (expenses.value.length > 1) {
        expenses.value.splice(index, 1);
    }
};
// ========== ДОХОДЫ ПО ГОДАМ (НОВОЕ) ==========
const addRevenue = () => {
    const nextYear = revenues.value.length + 1;
    revenues.value.push({ year: nextYear, amount: '' });
};
const removeRevenue = (index) => {
    if (revenues.value.length > 1) {
        revenues.value.splice(index, 1);
        // Пересчитываем года
        revenues.value.forEach((item, idx) => {
            item.year = idx + 1;
        });
    }
};
// ========== РАСХОДЫ ПО ГОДАМ (НОВОЕ) ==========
const addYearlyExpense = () => {
    const nextYear = yearlyExpenses.value.length + 1;
    yearlyExpenses.value.push({ year: nextYear, amount: '' });
};
const removeYearlyExpense = (index) => {
    if (yearlyExpenses.value.length > 1) {
        yearlyExpenses.value.splice(index, 1);
        // Пересчитываем года
        yearlyExpenses.value.forEach((item, idx) => {
            item.year = idx + 1;
        });
    }
};
// ========== ФОТО ==========
const handleFileUpload = (event) => {
    const files = Array.from(event.target.files);
    form.fotos = [...form.fotos, ...files];
    
    files.forEach(file => {
        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreviews.value.push({
                url: e.target.result,
                name: file.name,
                size: (file.size / 1024).toFixed(2)
            });
        };
        reader.readAsDataURL(file);
    });
    
    event.target.value = '';
};
const removePhoto = (index) => {
    form.fotos.splice(index, 1);
    photoPreviews.value.splice(index, 1);
};
// ========== ОТПРАВКА ФОРМЫ ==========
const submit = () => {
    // form.categories = categories.value.filter(cat => cat && cat.trim() !== '');
    
    form.expenses = expenses.value.filter(exp => {
        const hasItemName = exp.item_name && String(exp.item_name).trim() !== '';
        const hasAmount = exp.amount !== '' && exp.amount !== null && exp.amount !== undefined;
        return hasItemName && hasAmount;
    });
    
    // НОВОЕ: Обработка доходов по годам
    form.revenues = revenues.value.filter(rev => {
        return rev.amount !== '' && rev.amount !== null && rev.amount !== undefined;
    });
    
    // НОВОЕ: Обработка расходов по годам
    form.yearly_expenses = yearlyExpenses.value.filter(exp => {
        return exp.amount !== '' && exp.amount !== null && exp.amount !== undefined;
    });
    
    form.post(route('projects.store'), {
        forceFormData: true,
        onSuccess: () => {
            form.reset();
            categories.value = [''];
            expenses.value = [{ item_name: '', amount: '' }];
            revenues.value = [{ year: 1, amount: '' }];
            yearlyExpenses.value = [{ year: 1, amount: '' }];
            photoPreviews.value = [];
        },
        onError: (errors) => {
            console.log('Ошибки валидации:', errors);
        }
    });
};
// Цветовая схема
const colors = {
    bgDark: '#284139',
    bgLight: '#809076',
    accent: '#F8D794',
    border: '#886830',
    white: '#ffffff',
    red: '#DC2626',
    green: '#10B981',
};
</script>
<template>
    <Head title="Создание проекта">
        <meta name="description" content="Форма создания нового инвестиционного проекта">
    </Head>
    <AuthenticatedLayout>
        <template #header>
            <TitlePage id="createProject" value="Создание проекта"></TitlePage>
        </template>
        
        <div class="mx-auto py-6 px-4 sm:px-10 lg:px-16">
            <form @submit.prevent="submit" class=" mx-auto">
                <!-- ============================================
                     ОПИСАНИЕ ПРОЕКТА
                     ============================================ -->
                <div class="rounded-2xl p-6 mb-6 shadow-xl" :style="{ border: `3px solid ${colors.border}`, backgroundColor: colors.bgLight }">
                    <div class="flex items-center gap-3 mb-6 pb-4" :style="{ borderBottom: `2px solid ${colors.border}` }">
                        <h2 class="text-2xl font-bold" :style="{ color: colors.accent }">Основная информация</h2>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="name" value="Название проекта *" :style="{ color: colors.accent }" class="text-lg mb-2" />
                            <TextInput
                                id="name"
                                type="text"
                                class="block w-full rounded-xl"
                                v-model="form.name"
                                required
                                placeholder="Введите название проекта"
                                :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>
                        
                        <div>
                            <InputLabel for="shotr_descr" value="Короткое описание *" :style="{ color: colors.accent }" class="text-lg mb-2" />
                            <TextInput
                                id="shotr_descr"
                                type="text"
                                class="block w-full rounded-xl"
                                v-model="form.shotr_descr"
                                required
                                placeholder="Краткое описание проекта"
                                :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                            />
                            <InputError class="mt-2" :message="form.errors.shotr_descr" />
                        </div>
                        <div class="lg:col-span-2">
                            <InputLabel for="full_descr" value="Подробное описание *" :style="{ color: colors.accent }" class="text-lg mb-2" />
                            <textarea
                                id="full_descr"
                                class="block w-full h-32 rounded-xl focus:outline-none p-4 resize-none"
                                v-model="form.full_descr"
                                required
                                placeholder="Подробное описание проекта..."
                                :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.full_descr" />
                        </div>
                    </div>
                    <!-- Категории -->
                    <div class="mt-6 pt-6" :style="{ borderTop: `2px solid ${colors.border}` }">
                        <InputLabel value="Категории проекта (до 4 категорий)" :style="{ color: colors.accent }" class="text-lg mb-4" />
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div 
                                v-for="(category, index) in categories" 
                                :key="index" 
                                class="flex items-center gap-3"
                            >
                                <TextInput
                                    :id="`category-${index}`"
                                    type="text"
                                    class="block w-full rounded-xl"
                                    v-model="categories[index]"
                                    :placeholder="`Категория ${index + 1}`"
                                    :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                                />
                                
                                <button 
                                    v-if="categories.length > 1"
                                    type="button"
                                    @click="removeCategory(index)"
                                    class="text-white p-3 rounded-xl transition-all hover:scale-110 flex-shrink-0"
                                    :style="{ backgroundColor: colors.red, border: `2px solid ${colors.border}` }"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                                
                                <button 
                                    v-if="index === categories.length - 1 && categories.length < 4"
                                    type="button"
                                    @click="addCategory"
                                    class="text-white p-3 rounded-xl transition-all hover:scale-110 flex-shrink-0"
                                    :style="{ backgroundColor: colors.green, border: `2px solid ${colors.border}` }"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <InputError class="mt-2" :message="form.errors.categories" />
                    </div>
                    <!-- Фото -->
                    <div class="mt-6 pt-6" :style="{ borderTop: `2px solid ${colors.border}` }">
                        <InputLabel for="fotos" value="Изображения проекта" class="text-lg mb-4" :style="{ color: colors.accent }" />
                        
                        <label 
                            for="fotos" 
                            class="cursor-pointer inline-flex items-center gap-2 px-6 py-3 font-semibold rounded-xl transition-all hover:scale-105"
                            :style="{ backgroundColor: colors.bgDark, color: colors.accent, border: `2px solid ${colors.border}` }"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Добавить изображения
                        </label>
                        <input
                            id="fotos"
                            type="file"
                            @change="handleFileUpload"
                            accept="image/*"
                            multiple
                            class="hidden"
                        />
                        
                        <div v-if="photoPreviews.length > 0" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 mt-6">
                            <div 
                                v-for="(preview, index) in photoPreviews" 
                                :key="index"
                                class="relative group rounded-xl overflow-hidden shadow-lg"
                                :style="{ border: `2px solid ${colors.border}` }"
                            >
                                <img :src="preview.url" :alt="preview.name" class="w-full h-48 object-cover">
                                <button
                                    type="button"
                                    @click="removePhoto(index)"
                                    class="absolute top-2 right-2 text-white p-2 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity"
                                    :style="{ backgroundColor: colors.red }"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <InputError class="mt-2" :message="form.errors.fotos" />

                        <div v-if="Object.keys(form.errors).some(k => k.startsWith('fotos.'))" class="mt-2 space-y-1">
                            <p
                                v-for="(msg, key) in form.errors"
                                :key="key"
                                v-show="key.startsWith('fotos.')"
                                class="text-sm text-red-500"
                            >
                                {{ msg }}
                            </p>
                        </div>
                    </div>
                </div>
                <!-- ============================================
                     ХАРАКТЕРИСТИКИ
                     ============================================ -->
                <div class="rounded-2xl p-6 mb-6 shadow-xl" :style="{ border: `3px solid ${colors.border}`, backgroundColor: colors.bgLight }">
                    <div class="flex items-center gap-3 mb-6 pb-4" :style="{ borderBottom: `2px solid ${colors.border}` }">
                        <h2 class="text-2xl font-bold" :style="{ color: colors.accent }">Характеристики проекта</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <InputLabel for="ownShip" value="Собственность" :style="{ color: colors.accent }" class="mb-2" />
                            <TextInput
                                id="ownShip"
                                type="text"
                                class="block w-full rounded-xl"
                                v-model="form.ownShip"
                                placeholder="Тип собственности"
                                :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                            />
                            <InputError class="mt-2" :message="form.errors.ownShip" />
                        </div>
                        <div>
                            <InputLabel for="activity" value="Деятельность" :style="{ color: colors.accent }" class="mb-2" />
                            <TextInput
                                id="activity"
                                type="text"
                                class="block w-full rounded-xl"
                                v-model="form.activity"
                                placeholder="Вид деятельности"
                                :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                            />
                            <InputError class="mt-2" :message="form.errors.activity" />
                        </div>
                        <div>
                            <InputLabel for="type_building" value="Вид строительства" :style="{ color: colors.accent }" class="mb-2" />
                            <TextInput
                                id="type_building"
                                type="text"
                                class="block w-full rounded-xl"
                                v-model="form.type_building"
                                placeholder="Тип строительства"
                                :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                            />
                            <InputError class="mt-2" :message="form.errors.type_building" />
                        </div>
                        <div class="md:col-span-2 lg:col-span-3">
                            <InputLabel for="addres" value="Адрес" :style="{ color: colors.accent }" class="mb-2" />
                            <TextInput
                                id="addres"
                                type="text"
                                class="block w-full rounded-xl"
                                v-model="form.addres"
                                placeholder="Адрес проекта"
                                :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                            />
                            <InputError class="mt-2" :message="form.errors.addres" />
                        </div>
                        <div>
                            <InputLabel for="latitude" value="Широта" :style="{ color: colors.accent }" class="mb-2" />
                            <TextInput
                                id="latitude"
                                type="text"
                                class="block w-full rounded-xl"
                                v-model="form.latitude"
                                placeholder="55.7558"
                                :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                            />
                            <InputError class="mt-2" :message="form.errors.latitude" />
                        </div>
                        <div>
                            <InputLabel for="longitude" value="Долгота" :style="{ color: colors.accent }" class="mb-2" />
                            <TextInput
                                id="longitude"
                                type="text"
                                class="block w-full rounded-xl"
                                v-model="form.longitude"
                                placeholder="37.6173"
                                :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                            />
                            <InputError class="mt-2" :message="form.errors.longitude" />
                        </div>
                    </div>
                </div>
                <!-- ============================================
                     ФИНАНСОВЫЕ ПОКАЗАТЕЛИ
                     ============================================ -->
                <div class="rounded-2xl p-6 mb-6 shadow-xl" :style="{ border: `3px solid ${colors.border}`, backgroundColor: colors.bgLight }">
                    <div class="flex items-center gap-3 mb-6 pb-4" :style="{ borderBottom: `2px solid ${colors.border}` }">

                        <h2 class="text-2xl font-bold" :style="{ color: colors.accent }">Финансовые показатели</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                        <div>
                            <InputLabel for="total_investment" value="Требуемая сумма (₽)" :style="{ color: colors.accent }" class="mb-2" />
                            <TextInput
                                id="total_investment"
                                type="number"
                                class="block w-full rounded-xl"
                                v-model="form.total_investment"
                                placeholder="1000000"
                                :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                            />
                            <InputError class="mt-2" :message="form.errors.total_investment" />
                        </div>
                        <div>
                            <InputLabel for="collected_total_investment" value="Уже собрано (₽)" :style="{ color: colors.accent }" class="mb-2" />
                            <TextInput
                                id="collected_total_investment"
                                type="number"
                                class="block w-full rounded-xl"
                                v-model="form.collected_total_investment"
                                placeholder="500000"
                                :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                            />
                            <InputError class="mt-2" :message="form.errors.collected_total_investment" />
                        </div>
                        <div>
                            <InputLabel for="number_date_realise" value="Срок окупаемости (мес.)" :style="{ color: colors.accent }" class="mb-2" />
                            <TextInput
                                id="number_date_realise"
                                type="number"
                                class="block w-full rounded-xl"
                                v-model="form.number_date_realise"
                                placeholder="24"
                                :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                            />
                            <InputError class="mt-2" :message="form.errors.number_date_realise" />
                        </div>
                        <div>
                            <InputLabel for="count_new_job" value="Рабочих мест" :style="{ color: colors.accent }" class="mb-2" />
                            <TextInput
                                id="count_new_job"
                                type="number"
                                class="block w-full rounded-xl"
                                v-model="form.count_new_job"
                                placeholder="10"
                                :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                            />
                            <InputError class="mt-2" :message="form.errors.count_new_job" />
                        </div>
                    </div>
                    <!-- ДОХОДЫ ПО ГОДАМ -->
                    <div class="mt-8 pt-6" :style="{ borderTop: `2px solid ${colors.border}` }">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="w-6 h-6" :style="{ color: colors.green }" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/>
                            </svg>
                            <InputLabel value="Ожидаемая доходность по годам" :style="{ color: colors.accent }" class="text-lg" />
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            <div 
                                v-for="(revenue, index) in revenues" 
                                :key="`revenue-${index}`"
                                class="rounded-xl p-4 transition-all"
                                :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.green}` }"
                            >
                                <div class="flex items-center justify-between mb-3">
                                    <span class="font-bold text-lg" :style="{ color: colors.accent }">Год {{ revenue.year }}</span>
                                    <div class="flex gap-2">
                                        <button 
                                            v-if="revenues.length > 1"
                                            type="button"
                                            @click="removeRevenue(index)"
                                            class="text-white p-2 rounded-lg transition-all hover:scale-110"
                                            :style="{ backgroundColor: colors.red }"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        <button 
                                            v-if="index === revenues.length - 1"
                                            type="button"
                                            @click="addRevenue"
                                            class="text-white p-2 rounded-lg transition-all hover:scale-110"
                                            :style="{ backgroundColor: colors.green }"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <TextInput
                                    :id="`revenue-${index}`"
                                    type="number"
                                    step="0.01"
                                    class="block w-full rounded-lg"
                                    v-model.number="revenues[index].amount"
                                    placeholder="Доход в ₽"
                                    :style="{ backgroundColor: colors.bgLight, border: `2px solid ${colors.green}`, color: colors.white }"
                                />
                            </div>
                        </div>
                        <InputError class="mt-2" :message="form.errors.revenues" />
                    </div>
                    <!-- РАСХОДЫ ПО ГОДАМ -->
                    <div class="mt-8 pt-6" :style="{ borderTop: `2px solid ${colors.border}` }">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="w-6 h-6" :style="{ color: colors.red }" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12 13a1 1 0 100 2h5a1 1 0 001-1V9a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586 3.707 5.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z" clip-rule="evenodd"/>
                            </svg>
                            <InputLabel value="Ожидаемые расходы по годам" :style="{ color: colors.accent }" class="text-lg" />
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            <div 
                                v-for="(expense, index) in yearlyExpenses" 
                                :key="`yearly-expense-${index}`"
                                class="rounded-xl p-4 transition-all"
                                :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.red}` }"
                            >
                                <div class="flex items-center justify-between mb-3">
                                    <span class="font-bold text-lg" :style="{ color: colors.accent }">Год {{ expense.year }}</span>
                                    <div class="flex gap-2">
                                        <button 
                                            v-if="yearlyExpenses.length > 1"
                                            type="button"
                                            @click="removeYearlyExpense(index)"
                                            class="text-white p-2 rounded-lg transition-all hover:scale-110"
                                            :style="{ backgroundColor: colors.red }"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        <button 
                                            v-if="index === yearlyExpenses.length - 1"
                                            type="button"
                                            @click="addYearlyExpense"
                                            class="text-white p-2 rounded-lg transition-all hover:scale-110"
                                            :style="{ backgroundColor: colors.green }"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <TextInput
                                    :id="`yearly-expense-${index}`"
                                    type="number"
                                    step="0.01"
                                    class="block w-full rounded-lg"
                                    v-model.number="yearlyExpenses[index].amount"
                                    placeholder="Расход в ₽"
                                    :style="{ backgroundColor: colors.bgLight, border: `2px solid ${colors.red}`, color: colors.white }"
                                />
                            </div>
                        </div>
                        <InputError class="mt-2" :message="form.errors.yearly_expenses" />
                    </div>
                    <!-- СТРУКТУРА ТРАТ (старая) -->
                    <div class="mt-8 pt-6" :style="{ borderTop: `2px solid ${colors.border}` }">
                        <InputLabel value="Структура трат" :style="{ color: colors.accent }" class="text-lg mb-4" />
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div 
                                v-for="(expense, index) in expenses" 
                                :key="`expense-${index}`"
                                class="rounded-xl p-4"
                                :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}` }"
                            >
                                <div class="space-y-3">
                                    <div>
                                        <InputLabel :for="`item_name-${index}`" :value="`Статья ${index + 1}`" :style="{ color: colors.accent }" class="mb-2" />
                                        <TextInput
                                            :id="`item_name-${index}`"
                                            type="text"
                                            class="block w-full rounded-lg"
                                            v-model="expenses[index].item_name"
                                            placeholder="Название статьи расходов"
                                            :style="{ backgroundColor: colors.bgLight, border: `2px solid ${colors.border}`, color: colors.white }"
                                        />
                                    </div>
                                    
                                    <div>
                                        <InputLabel :for="`amount-${index}`" value="Сумма (₽)" :style="{ color: colors.accent }" class="mb-2" />
                                        <TextInput
                                            :id="`amount-${index}`"
                                            type="number"
                                            step="0.01"
                                            class="block w-full rounded-lg"
                                            v-model.number="expenses[index].amount"
                                            placeholder="100000"
                                            :style="{ backgroundColor: colors.bgLight, border: `2px solid ${colors.border}`, color: colors.white }"
                                        />
                                    </div>
                                    
                                    <div class="flex gap-2 justify-end">
                                        <button 
                                            v-if="expenses.length > 1"
                                            type="button"
                                            @click="removeExpense(index)"
                                            class="text-white px-4 py-2 rounded-lg transition-all hover:scale-110"
                                            :style="{ backgroundColor: colors.red }"
                                        >
                                            Удалить
                                        </button>
                                        <button 
                                            v-if="index === expenses.length - 1"
                                            type="button"
                                            @click="addExpense"
                                            class="text-white px-4 py-2 rounded-lg transition-all hover:scale-110"
                                            :style="{ backgroundColor: colors.green }"
                                        >
                                            Добавить
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <InputError class="mt-2" :message="form.errors.expenses" />
                    </div>
                </div>
                <!-- КНОПКА ОТПРАВКИ -->
                <div class="flex justify-center">
                    <PrimaryButton
                        class="px-12 py-4 text-xl rounded-2xl font-bold shadow-xl transition-all hover:scale-105"
                        :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                        :disabled="form.processing"
                        :style="{ backgroundColor: colors.bgDark, color: colors.accent, border: `3px solid ${colors.border}` }"
                    >
                        {{ form.processing ? 'Создание...' : 'Создать проект' }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>