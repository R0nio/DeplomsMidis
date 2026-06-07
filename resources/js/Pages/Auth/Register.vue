<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Checkbox from '@/Components/Checkbox.vue';
import Select from '@/Components/Select.vue';
import { MaskInput } from 'vue-mask-next';
import { ref, watch } from 'vue';
import logo from "../../../images/LogoInvestProject.png";

// ===== ЦВЕТА И СТИЛИ КОМПОНЕНТА =====
const colors = {
    brand: 'var(--color-brand)',
    brandDark: 'var(--color-brand-dark)',
    accent: 'var(--color-accent)',
    hover: 'var(--color-hover)',
    page: 'var(--color-page)',
    surface: 'var(--color-surface)',
    light: 'var(--color-light)',
    white: 'var(--color-white)',
    white80: 'rgba(255, 255, 255, 0.8)',
    white10: 'rgba(255, 255, 255, 0.1)',
    error: 'var(--color-error)',
    success: 'var(--color-success)',
    text: 'var(--color-text)',
    textMuted: 'var(--color-text-muted)',
    textSoft: 'var(--color-text-soft)',
};

const fonts = {
    heading: 'var(--font-heading)',
    body: 'var(--font-body)',
};

const shadows = {
    xl: 'var(--shadow-xl)',
};

const transitions = {
    normal: 'var(--transition-normal)',
};

const form = useForm({
    name: '',
    middlename: '',
    lastname: '',
    email: '',
    login: '',
    number: '',
    role: '',
    password: '',
    organization_name: '',
    password_confirmation: '',
    acceptPersonalDate: false,
});

const isPhoneValid = ref(true);
const phoneError = ref('');

const validatePhone = (phone) => {
    if (!phone) {
        isPhoneValid.value = false;
        phoneError.value = 'Телефон обязателен для заполнения';
        return false;
    }
    
    const phoneRegex = /^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/;
    if (!phoneRegex.test(phone)) {
        isPhoneValid.value = false;
        phoneError.value = 'Введите телефон в формате +7 (XXX) XXX-XX-XX';
        return false;
    }
    
    if (phone.includes('_')) {
        isPhoneValid.value = false;
        phoneError.value = 'Пожалуйста, заполните все цифры номера телефона';
        return false;
    }
    
    isPhoneValid.value = true;
    phoneError.value = '';
    return true;
};

watch(() => form.number, (newValue) => {
    validatePhone(newValue);
});

const submit = () => {
    if (!validatePhone(form.number)) {
        return;
    }
    
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const roleOptions = [
    { value: 'Investor', label: 'Инвестор' },
    { value: 'Organisator', label: 'Инициатор' },
];
</script>

<template>
    <GuestLayout>
        <Head title="Регистрация">
            <meta name="description" content="Регистрация на инвестиционной платформе InvestProject">
        </Head>

        <div class="flex flex-col lg:flex-row min-h-[700px] lg:min-h-[800px] rounded-2xl overflow-hidden shadow-xl" :style="{ backgroundColor: colors.surface, border: `1px solid ${colors.hover}`, boxShadow: shadows.xl }">
            
            <!-- Левая колонка - форма регистрации -->
            <div class="w-full lg:w-1/2 p-6 lg:p-8 overflow-y-auto" :style="{ backgroundColor: colors.light }" role="main" aria-labelledby="register-title">
                <form @submit.prevent="submit" class="flex flex-col gap-4" aria-label="Форма регистрации">
                    <h1 
                        id="register-title" 
                        class="text-center text-3xl lg:text-4xl font-heading font-bold mb-2"
                        :style="{ color: colors.brand, fontFamily: fonts.heading }"
                        tabindex="0"
                        role="heading"
                        aria-level="1"
                    >
                        Регистрация
                    </h1>
                    
                    <!-- ФИО в одну строку -->
                    <div>
                        <InputLabel value="ФИО" />
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-1">
                            <div>
                                <TextInput
                                    type="text"
                                    placeholder="Фамилия"
                                    class="block w-full"
                                    v-model="form.middlename"
                                    required
                                    :class="{ 'border-red-500': form.errors.middlename }"
                                    aria-label="Введите вашу фамилию"
                                    aria-required="true"
                                    :aria-invalid="!!form.errors.middlename"
                                />
                                <InputError class="mt-1" :message="form.errors.middlename" role="alert" />
                            </div>
                            <div>
                                <TextInput
                                    type="text"
                                    placeholder="Имя"
                                    class="block w-full"
                                    v-model="form.name"
                                    required
                                    :class="{ 'border-red-500': form.errors.name }"
                                    aria-label="Введите ваше имя"
                                    aria-required="true"
                                    :aria-invalid="!!form.errors.name"
                                />
                                <InputError class="mt-1" :message="form.errors.name" role="alert" />
                            </div>
                            <div>
                                <TextInput
                                    type="text"
                                    placeholder="Отчество"
                                    class="block w-full"
                                    v-model="form.lastname"
                                    :class="{ 'border-red-500': form.errors.lastname }"
                                    aria-label="Введите ваше отчество"
                                    :aria-invalid="!!form.errors.lastname"
                                />
                                <InputError class="mt-1" :message="form.errors.lastname" role="alert" />
                            </div>
                        </div>
                    </div>

                    <!-- Email и телефон в одну строку -->
                    <div>
                        <InputLabel value="Контактные данные" />
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-1">
                            <div>
                                <TextInput
                                    type="email"
                                    placeholder="Электронная почта"
                                    class="block w-full"
                                    v-model="form.email"
                                    required
                                    :class="{ 'border-red-500': form.errors.email }"
                                    aria-label="Введите ваш email"
                                    aria-required="true"
                                    :aria-invalid="!!form.errors.email"
                                />
                                <InputError class="mt-1" :message="form.errors.email" role="alert" />
                            </div>
                            <div>
                                <MaskInput
                                    v-model="form.number"
                                    mask="+7 (###) ###-##-##"
                                    placeholder="+7 (___) ___-__-__"
                                    required
                                    class="block w-full h-12 rounded-xl px-4 shadow-sm transition-all duration-200 outline-none"
                                    :style="{ 
                                        border: `1px solid ${colors.light}`, 
                                        backgroundColor: colors.page, 
                                        color: colors.brand,
                                        transition: transitions.normal
                                    }"
                                    :class="{
                                        'ring-2 ring-red-400': (!isPhoneValid && form.number) || form.errors.number,
                                    }"
                                    aria-label="Введите номер телефона в формате +7 (XXX) XXX-XX-XX"
                                    aria-required="true"
                                    :aria-invalid="!isPhoneValid"
                                />
                                <p v-if="phoneError && form.number" class="text-xs mt-1" :style="{ color: colors.error }" role="alert">
                                    {{ phoneError }}
                                </p>
                                <InputError class="mt-1" :message="form.errors.number" role="alert" />
                            </div>
                        </div>
                    </div>

                    <!-- Логин -->
                    <div>
                        <InputLabel for="login" value="Логин" />
                        <TextInput
                            id="login"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.login"
                            required
                            :class="{ 'border-red-500': form.errors.login }"
                            aria-label="Придумайте логин"
                            aria-required="true"
                            :aria-invalid="!!form.errors.login"
                        />
                        <InputError class="mt-2" :message="form.errors.login" role="alert" />
                    </div>

                    <!-- Роль -->
                    <div>
                        <InputLabel for="role" value="Ваша роль" />
                        <Select
                            id="role"
                            class="mt-1 block w-full rounded-lg"
                            v-model="form.role"
                            :options="roleOptions"
                            required
                            aria-label="Выберите вашу роль на платформе 1 Инвестор 2 Инициатор"
                            aria-required="true"
                            :aria-invalid="!!form.errors.role"
                        />
                        <InputError class="mt-2" :message="form.errors.role" role="alert" />
                    </div>

                    <!-- Название организации (только для организатора) -->
                    <div v-if="form.role === 'Organisator'">
                        <InputLabel for="organization_name" value="Название организации" />
                        <TextInput
                            id="organization_name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.organization_name"
                            :class="{ 'border-red-500': form.errors.organization_name }"
                            aria-label="Введите название вашей организации"
                            :aria-invalid="!!form.errors.organization_name"
                        />
                        <InputError class="mt-2" :message="form.errors.organization_name" role="alert" />
                    </div>

                    <!-- Пароль и подтверждение в одну строку -->
                    <div>
                        <InputLabel value="Пароль" />
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-1">
                            <div>
                                <TextInput
                                    type="password"
                                    placeholder="Пароль"
                                    class="block w-full"
                                    v-model="form.password"
                                    required
                                    :class="{ 'border-red-500': form.errors.password }"
                                    aria-label="Придумайте пароль"
                                    aria-required="true"
                                    :aria-invalid="!!form.errors.password"
                                />
                                <InputError class="mt-1" :message="form.errors.password" role="alert" />
                            </div>
                            <div>
                                <TextInput
                                    type="password"
                                    placeholder="Подтвердите пароль"
                                    class="block w-full"
                                    v-model="form.password_confirmation"
                                    required
                                    :class="{ 'border-red-500': form.errors.password_confirmation }"
                                    aria-label="Повторите пароль"
                                    aria-required="true"
                                    :aria-invalid="!!form.errors.password_confirmation"
                                />
                                <InputError class="mt-1" :message="form.errors.password_confirmation" role="alert" />
                            </div>
                        </div>
                    </div>

                    <!-- Согласие -->
                    <div class="mt-2">
                        <label class="flex items-center cursor-pointer gap-2" aria-label="Согласие на обработку персональных данных">
                            <Checkbox 
                                name="acceptPersonalDate" 
                                v-model:checked="form.acceptPersonalDate" 
                                required 
                                aria-required="true"
                            />
                            <span class="text-sm" :style="{ color: colors.textMuted }">Согласие на обработку персональных данных</span>
                        </label>
                        <InputError class="mt-2" :message="form.errors.acceptPersonalDate" role="alert" />
                    </div>

                    <PrimaryButton
                        class="w-full flex justify-center py-3 px-4 font-semibold rounded-lg transition-all duration-200 disabled:opacity-50 mt-4"
                        :class="{ 'opacity-50': form.processing }"
                        :disabled="form.processing || !isPhoneValid"
                        aria-label="Зарегистрироваться"
                        :aria-disabled="form.processing || !isPhoneValid"
                    >
                        <span v-if="!form.processing">Зарегистрироваться</span>
                        <span v-else class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-5 w-5" :style="{ color: colors.white }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Регистрация...
                        </span>
                    </PrimaryButton>
                    
                    <p class="text-center text-sm" :style="{ color: colors.textMuted }">
                        Уже зарегистрированы?
                        <Link
                            :href="route('login')"
                            class="font-semibold transition-colors"
                            :style="{ color: colors.accent }"
                            @mouseenter="$event.target.style.color = colors.hover"
                            @mouseleave="$event.target.style.color = colors.accent"
                            aria-label="Перейти к авторизации"
                        >
                            Войти
                        </Link>
                    </p>
                </form>
            </div>

            <!-- Правая колонка - брендинг -->
            <div class="w-full lg:w-1/2 p-8 lg:p-12 flex flex-col items-center justify-center text-center" :style="{ backgroundColor: colors.brand }" role="complementary" aria-labelledby="brand-title">
                <div class="backdrop-blur-sm rounded-2xl p-8" :style="{ backgroundColor: colors.white10 }">
                    <img 
                        :src="logo" 
                        alt="Логотип InvestProject - инвестиционная платформа" 
                        class="w-48 lg:w-64 mb-6 mx-auto"
                        aria-describedby="logo-description"
                    >
                    <h2 id="brand-title" class="text-3xl lg:text-4xl font-heading font-bold mb-3" :style="{ color: colors.white, fontFamily: fonts.heading }" tabindex="0" role="heading" aria-level="2">
                        InvestProject
                    </h2>
                    <p class="text-base lg:text-lg" :style="{ color: colors.white80 }">
                        Инвестиционная платформа для поиска проектов
                    </p>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>