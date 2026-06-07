<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import logo from "../../../images/LogoInvestProject.png";

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

        <!-- Контейнер с двумя колонками -->
        <div class="flex flex-col lg:flex-row min-h-[600px] lg:min-h-[700px] rounded-2xl overflow-hidden" :style="{ backgroundColor: '#284139', border: `2px solid #886830` }">
            
            <!-- Левая колонка - форма входа -->
            <div class="w-full lg:w-1/2 p-8 lg:p-12 flex flex-col justify-center" role="main" aria-labelledby="login-title">
                <div v-if="status" class="mb-4 text-sm font-medium text-center" style="color: #4caf50" role="status" aria-live="polite">
                    {{ status }}
                </div>
                
                <form @submit.prevent="submit" class="flex flex-col gap-6" aria-label="Форма авторизации">
                    <h1 
                        id="login-title" 
                        class="text-center text-3xl font-bold mb-2" 
                        style="color: #F8D794"
                        tabindex="0"
                        role="heading"
                        aria-level="1"
                    >
                        Авторизация
                    </h1>
                    
                    <div>
                        <InputLabel for="login" value="Логин" style="color: #F8D794" />
                        <TextInput
                            id="login"
                            type="text"
                            class="block w-full mt-1"
                            v-model="form.login"
                            required
                            autofocus
                            autocomplete="username"
                            style="background-color: #1a2d24; border: 2px solid #886830; color: white; border-radius: 12px;"
                            aria-label="Введите ваш логин"
                            aria-required="true"
                            :aria-invalid="!!form.errors.login"
                        />
                        <InputError class="mt-2" :message="form.errors.login" role="alert" />
                    </div>

                    <div>
                        <div class="flex justify-between items-center">
                            <InputLabel for="password" value="Пароль" style="color: #F8D794" />

                        </div>
                        <TextInput
                            id="password"
                            type="password"
                            class="block w-full mt-1"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                            style="background-color: #1a2d24; border: 2px solid #886830; color: white; border-radius: 12px;"
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
                                style="accent-color: #F8D794"
                                aria-describedby="remember-me-description"
                            />
                            <span class="text-sm" style="color: #F8D794" id="remember-me-description">Запомнить меня</span>
                        </label>
                    </div>

                    <div class="flex flex-col gap-3 mt-2">
                        <PrimaryButton
                            class="w-full flex justify-center py-3"
                            :class="{ 'opacity-50': form.processing }"
                            :disabled="form.processing"
                            style="background-color: #809076; border: 2px solid #886830; color: white; border-radius: 12px;"
                            aria-label="Войти в систему"
                            :aria-disabled="form.processing"
                        >
                            <span v-if="!form.processing">Войти</span>
                            <span v-else class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Вход...
                            </span>
                        </PrimaryButton>
                        
                        <p class="text-center text-sm" style="color: #F8D794">
                            Нет аккаунта?
                            <Link
                                :href="route('register')"
                                class="font-semibold hover:opacity-80 transition-colors underline"
                                style="color: #F8D794"
                                aria-label="Перейти к регистрации"
                            >
                                Зарегистрироваться
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
                    class="text-base lg:text-lg" 
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
</style>