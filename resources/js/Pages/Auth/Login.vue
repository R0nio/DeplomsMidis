<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
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

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    login: '',  
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Вход в систему">
            <meta name="description" content="Страница авторизации на инвестиционной платформе InvestProject">
        </Head>

        <div class="flex flex-col lg:flex-row min-h-[600px] lg:min-h-[700px] rounded-2xl overflow-hidden shadow-xl" :style="{ backgroundColor: colors.surface, border: `1px solid ${colors.hover}`, boxShadow: shadows.xl }">
            
            <!-- Левая колонка - форма входа -->
            <div class="w-full lg:w-1/2 p-8 lg:p-12 flex flex-col justify-center" :style="{ backgroundColor: colors.light }" role="main" aria-labelledby="login-title">
                <div v-if="status" class="mb-4 p-3 rounded-lg text-sm font-medium text-center" :style="{ backgroundColor: colors.success + '20', color: colors.success }" role="status" aria-live="polite">
                    {{ status }}
                </div>
                
                <form @submit.prevent="submit" class="flex flex-col gap-6" aria-label="Форма авторизации">
                    <h1 
                        id="login-title" 
                        class="text-center text-3xl lg:text-4xl font-heading font-bold mb-2"
                        :style="{ color: colors.brand, fontFamily: fonts.heading }"
                        tabindex="0"
                        role="heading"
                        aria-level="1"
                    >
                        Авторизация
                    </h1>
                    
                    <div>
                        <InputLabel for="login" value="Логин" />
                        <TextInput
                            id="login"
                            type="text"
                            class="block w-full mt-1"
                            v-model="form.login"
                            required
                            autofocus
                            autocomplete="username"
                            :class="{ 'border-red-500': form.errors.login }"
                            aria-label="Введите ваш логин"
                            aria-required="true"
                            :aria-invalid="!!form.errors.login"
                        />
                        <InputError class="mt-2" :message="form.errors.login" role="alert" />
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <InputLabel for="password" value="Пароль" />
                            <Link 
                                v-if="canResetPassword" 
                                :href="route('password.request')"
                                class="text-sm transition-colors"
                                :style="{ color: colors.hover }"
                                @mouseenter="$event.target.style.color = colors.accent"
                                @mouseleave="$event.target.style.color = colors.hover"
                                aria-label="Восстановить пароль"
                            >
                                Забыли пароль?
                            </Link>
                        </div>
                        <TextInput
                            id="password"
                            type="password"
                            class="block w-full mt-1"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                            :class="{ 'border-red-500': form.errors.password }"
                            aria-label="Введите ваш пароль"
                            aria-required="true"
                            :aria-invalid="!!form.errors.password"
                        />
                        <InputError class="mt-2" :message="form.errors.password" role="alert" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer" aria-label="Запомнить меня">
                            <Checkbox
                                v-model:checked="form.remember"
                                name="remember"
                            />
                            <span class="text-sm" :style="{ color: colors.textMuted }">Запомнить меня</span>
                        </label>
                    </div>

                    <PrimaryButton
                        class="w-full flex justify-center py-3 px-4 font-semibold rounded-lg transition-all duration-200 disabled:opacity-50"
                        :class="{ 'opacity-50': form.processing }"
                        :disabled="form.processing"
                        aria-label="Войти в систему"
                        :aria-disabled="form.processing"
                    >
                        <span v-if="!form.processing">Войти</span>
                        <span v-else class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-5 w-5" :style="{ color: colors.white }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Вход...
                        </span>
                    </PrimaryButton>
                    
                    <p class="text-center text-sm" :style="{ color: colors.textMuted }">
                        Нет аккаунта?
                        <Link
                            :href="route('register')"
                            class="font-semibold transition-colors"
                            :style="{ color: colors.accent }"
                            @mouseenter="$event.target.style.color = colors.hover"
                            @mouseleave="$event.target.style.color = colors.accent"
                            aria-label="Перейти к регистрации"
                        >
                            Зарегистрироваться
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