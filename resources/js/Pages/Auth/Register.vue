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

// Состояние валидации телефона
const isPhoneValid = ref(true);
const phoneError = ref('');

// Валидация телефона
const validatePhone = (phone) => {
    if (!phone) {
        isPhoneValid.value = false;
        phoneError.value = 'Телефон обязателен для заполнения';
        return false;
    }
    
    // Проверка формата +7 (XXX) XXX-XX-XX
    const phoneRegex = /^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/;
    if (!phoneRegex.test(phone)) {
        isPhoneValid.value = false;
        phoneError.value = 'Введите телефон в формате +7 (XXX) XXX-XX-XX';
        return false;
    }
    
    // Проверка что все цифры введены (нет символов _)
    if (phone.includes('_')) {
        isPhoneValid.value = false;
        phoneError.value = 'Пожалуйста, заполните все цифры номера телефона';
        return false;
    }
    
    isPhoneValid.value = true;
    phoneError.value = '';
    return true;
};

// Следим за изменением номера телефона
watch(() => form.number, (newValue) => {
    validatePhone(newValue);
});

const submit = () => {
    // Валидация телефона перед отправкой
    if (!validatePhone(form.number)) {
        return;
    }
    
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const roleOptions = [
    { value: '', label: 'Выберите роль' },
    { value: 'Investor', label: 'Инвестор' },
    { value: 'Organisator', label: 'Организатор (Инициатор проекта)' },
];
</script>

<template>
    <GuestLayout>
        <Head title="Регистрация">
            <meta name="description" content="Регистрация на инвестиционной платформе InvestProject">
        </Head>

        <!-- Контейнер с двумя колонками -->
        <div class="flex flex-col lg:flex-row min-h-[700px] lg:min-h-[800px] rounded-2xl overflow-hidden" :style="{ backgroundColor: '#284139', border: `2px solid #886830` }">
            
            <!-- Левая колонка - форма регистрации -->
            <div class="w-full lg:w-1/2 p-6 lg:p-8 overflow-y-auto" role="main" aria-labelledby="register-title">
                <form @submit.prevent="submit" class="flex flex-col gap-4" aria-label="Форма регистрации">
                    <h1 
                        id="register-title" 
                        class="text-center text-3xl font-bold mb-4" 
                        style="color: #F8D794"
                        tabindex="0"
                        role="heading"
                        aria-level="1"
                    >
                        Регистрация
                    </h1>
                    
                    <!-- Строка ФИО (Фамилия, Имя, Отчество в одну строку) -->
                    <div>
                        <InputLabel value="ФИО" style="color: #F8D794" />
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-1">
                            <div>
                                <TextInput
                                    id="middlename"
                                    type="text"
                                    placeholder="Фамилия"
                                    class="block w-full"
                                    v-model="form.middlename"
                                    required
                                    autocomplete="family-name"
                                    style="background-color: #1a2d24; border: 2px solid #886830; color: white; border-radius: 12px;"
                                    aria-label="Введите вашу фамилию"
                                    aria-required="true"
                                    :aria-invalid="!!form.errors.middlename"
                                />
                                <InputError class="mt-1" :message="form.errors.middlename" role="alert" />
                            </div>
                            <div>
                                <TextInput
                                    id="name"
                                    type="text"
                                    placeholder="Имя"
                                    class="block w-full"
                                    v-model="form.name"
                                    required
                                    autocomplete="given-name"
                                    style="background-color: #1a2d24; border: 2px solid #886830; color: white; border-radius: 12px;"
                                    aria-label="Введите ваше имя"
                                    aria-required="true"
                                    :aria-invalid="!!form.errors.name"
                                />
                                <InputError class="mt-1" :message="form.errors.name" role="alert" />
                            </div>
                            <div>
                                <TextInput
                                    id="lastname"
                                    type="text"
                                    placeholder="Отчество"
                                    class="block w-full"
                                    v-model="form.lastname"
                                    autocomplete="additional-name"
                                    style="background-color: #1a2d24; border: 2px solid #886830; color: white; border-radius: 12px;"
                                    aria-label="Введите ваше отчество"
                                    :aria-invalid="!!form.errors.lastname"
                                />
                                <InputError class="mt-1" :message="form.errors.lastname" role="alert" />
                            </div>
                        </div>
                    </div>

                    <!-- Строка Email и Телефон в одну строку -->
                    <div>
                        <InputLabel value="Контактные данные" style="color: #F8D794" />
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-1">
                            <div>
                                <TextInput
                                    id="email"
                                    type="email"
                                    placeholder="Электронная почта"
                                    class="block w-full"
                                    v-model="form.email"
                                    required
                                    autocomplete="email"
                                    style="background-color: #1a2d24; border: 2px solid #886830; color: white; border-radius: 12px;"
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
                                    class="mt-1 block w-full h-12 rounded-xl"
                                    style="background-color: #1a2d24; border: 2px solid #886830; color: white;"
                                    :class="{ 'border-red-500': !isPhoneValid && form.number }"
                                    aria-label="Введите номер телефона в формате +7 (XXX) XXX-XX-XX"
                                    aria-required="true"
                                    :aria-invalid="!isPhoneValid"
                                />
                                <p v-if="phoneError && form.number" class="text-red-500 text-xs mt-1" role="alert">
                                    {{ phoneError }}
                                </p>
                                <InputError class="mt-1" :message="form.errors.number" role="alert" />
                            </div>
                        </div>
                    </div>

                    <!-- Логин -->
                    <div>
                        <InputLabel for="login" value="Логин" style="color: #F8D794" />
                        <TextInput
                            id="login"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.login"
                            required
                            autocomplete="username"
                            style="background-color: #1a2d24; border: 2px solid #886830; color: white; border-radius: 12px;"
                            aria-label="Придумайте логин"
                            aria-required="true"
                            :aria-invalid="!!form.errors.login"
                        />
                        <InputError class="mt-2" :message="form.errors.login" role="alert" />
                    </div>

                    <!-- Роль -->
                    <div>
                        <InputLabel for="role" value="Ваша роль" style="color: #F8D794" />
                        <Select
                            id="role"
                            class="mt-1 block w-full rounded-xl"
                            v-model="form.role"
                            :options="roleOptions"
                            required
                            style="background-color: #1a2d24; border: 2px solid #886830; color: white;"
                            aria-label="Выберите вашу роль на платформе"
                            aria-required="true"
                            :aria-invalid="!!form.errors.role"
                        />
                        <InputError class="mt-2" :message="form.errors.role" role="alert" />
                    </div>

                    <!-- Название организации (только для организатора) -->
                    <div v-if="form.role === 'Organisator'">
                        <InputLabel for="organization_name" value="Название организации" style="color: #F8D794" />
                        <TextInput
                            id="organization_name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.organization_name"
                            autocomplete="organization"
                            style="background-color: #1a2d24; border: 2px solid #886830; color: white; border-radius: 12px;"
                            aria-label="Введите название вашей организации"
                            :aria-invalid="!!form.errors.organization_name"
                        />
                        <InputError class="mt-2" :message="form.errors.organization_name" role="alert" />
                    </div>

                    <!-- Пароль и подтверждение в одну строку -->
                    <div>
                        <InputLabel value="Пароль" style="color: #F8D794" />
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-1">
                            <div>
                                <TextInput
                                    id="password"
                                    type="password"
                                    placeholder="Пароль"
                                    class="block w-full"
                                    v-model="form.password"
                                    required
                                    autocomplete="new-password"
                                    style="background-color: #1a2d24; border: 2px solid #886830; color: white; border-radius: 12px;"
                                    aria-label="Придумайте пароль"
                                    aria-required="true"
                                    :aria-invalid="!!form.errors.password"
                                />
                                <InputError class="mt-1" :message="form.errors.password" role="alert" />
                            </div>
                            <div>
                                <TextInput
                                    id="password_confirmation"
                                    type="password"
                                    placeholder="Подтвердите пароль"
                                    class="block w-full"
                                    v-model="form.password_confirmation"
                                    required
                                    autocomplete="new-password"
                                    style="background-color: #1a2d24; border: 2px solid #886830; color: white; border-radius: 12px;"
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
                        <label class="flex items-center cursor-pointer" aria-label="Согласие на обработку персональных данных">
                            <Checkbox 
                                name="acceptPersonalDate" 
                                v-model:checked="form.acceptPersonalDate" 
                                required 
                                style="border-color: #886830"
                                aria-required="true"
                            />
                            <span class="ms-2 text-sm" style="color: #F8D794">Согласие на обработку персональных данных</span>
                        </label>
                        <InputError class="mt-2" :message="form.errors.acceptPersonalDate" role="alert" />
                    </div>

                    <div class="mt-4 flex flex-col">
                        <PrimaryButton
                            class="w-full flex justify-center py-3"
                            :class="{ 'opacity-50': form.processing }"
                            :disabled="form.processing || !isPhoneValid"
                            style="background-color: #809076; border: 2px solid #886830; color: white; border-radius: 12px;"
                            aria-label="Зарегистрироваться"
                            :aria-disabled="form.processing || !isPhoneValid"
                        >
                            <span v-if="!form.processing">Зарегистрироваться</span>
                            <span v-else class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Регистрация...
                            </span>
                        </PrimaryButton>
                        
                        <p class="text-center text-sm mt-3" style="color: #F8D794">
                            Уже зарегистрированы?
                            <Link
                                :href="route('login')"
                                class="font-semibold hover:opacity-80 transition-colors underline"
                                style="color: #F8D794"
                                aria-label="Перейти к авторизации"
                            >
                                Войти
                            </Link>
                        </p>
                    </div>
                </form>
            </div>

            <!-- Правая колонка - логотип и информация -->
            <div 
                class="w-full lg:w-1/2 p-8 lg:p-12 flex flex-col items-center justify-center text-center"
                :style="{ backgroundColor: '#1a2d24' }"
                role="complementary"
                aria-labelledby="brand-title"
            >
                <img 
                    :src="logo" 
                    alt="Логотип InvestProject - инвестиционная платформа" 
                    class="w-48 lg:w-64 mb-6"
                    aria-describedby="logo-description"
                >
                <h2 
                    id="brand-title" 
                    class="text-2xl lg:text-3xl font-bold mb-3" 
                    style="color: #F8D794"
                    tabindex="0"
                    role="heading"
                    aria-level="2"
                >
                    InvestProject
                </h2>
                <p 
                    class="text-base lg:text-lg mb-4" 
                    style="color: #e8f0ee"
                    tabindex="0"
                >
                    Инвестиционная платформа для поиска проектов
                </p>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

/* Стили для скролла в левой колонке */
.overflow-y-auto::-webkit-scrollbar {
    width: 8px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #1a2d24;
    border-radius: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #886830;
    border-radius: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #F8D794;
}

/* Стили для плейсхолдера */
input::placeholder, .mask-input::placeholder {
    color: #809076;
    opacity: 0.7;
}
</style>