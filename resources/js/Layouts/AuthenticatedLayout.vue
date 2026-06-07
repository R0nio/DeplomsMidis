<script setup>
import { ref, onMounted } from "vue";
import { router } from '@inertiajs/vue3';
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link } from "@inertiajs/vue3";
import logo from "../../images/LogoInvestProject.png";
import AccessibilityHelper from "@/Components/Main/AccessibilityHelper.vue";

// Основные цвета
const mainColor = "#436343";
const NavColor = "#2A3F2A";
const accentColor = "#F8D794";
const borderColor = "#886830";

const showingNavigationDropdown = ref(false);

const closeMobileMenu = () => {
    showingNavigationDropdown.value = false;
};

// Останавливаем речь при навигации Inertia
const stopSpeech = () => {
    if (window.speechSynthesis) {
        try {
            window.speechSynthesis.cancel();
            if (window.speechSynthesis.speaking) {
                window.speechSynthesis.pause();
                window.speechSynthesis.cancel();
            }
        } catch (error) {
            console.error('Error stopping speech:', error);
        }
    }
    
    if (typeof window.__accessibilityCleanup === 'function') {
        window.__accessibilityCleanup();
    }
};

onMounted(() => {
    // Слушаем события Inertia для остановки речи
    router.on('start', () => {
        stopSpeech();
        closeMobileMenu();
        
    });
    
    router.on('finish', () => {
        setTimeout(() => {
            const saved = localStorage.getItem('accessibilitySettings');
            if (saved) {
                try {
                    const settings = JSON.parse(saved);
                    if (!settings.speakMode) {
                        stopSpeech();
                    }
                } catch (e) {}
            }
        }, 100);
    });
});
</script>

<template>
    <div>
        <div :style="{ backgroundColor: mainColor }" class="min-h-screen flex flex-col">
            <nav 
                :style="{ backgroundColor: NavColor, borderBottom: `4px solid ${borderColor}` }"
                class="fixed top-0 left-0 right-0 z-50 rounded-b-[32px] shadow-lg"
                role="navigation"
                aria-label="Главная навигация"
                >
                <div class="w-full px-4 sm:px-6 lg:px-8">
                    <div class="relative flex items-center justify-between h-20 lg:h-24">
                    <!-- Лого -->
                    <div class="flex-shrink-0 w-40 sm:w-48">
                        <Link :href="route('dashboard')" class="flex items-center" aria-label="На главную">
                        <img class="h-16 lg:h-20 w-auto object-contain" :src="logo" alt="Логотип InvestProject - инвестиционная платформа" />
                        </Link>
                    </div>

                    <!-- Центр меню: всегда центр экрана (desktop) -->
                    <div class="hidden xl:flex absolute left-1/2 -translate-x-1/2 items-center gap-2 z-10 max-w-[52vw] overflow-x-auto whitespace-nowrap">
                        <NavLink :href="route('dashboard')" :active="route().current('dashboard')">Главная</NavLink>
                        <NavLink :href="route('projects')" :active="route().current('projects')">Проекты</NavLink>
                        <NavLink :href="route('mapProject')" :active="route().current('mapProject')">Карта проектов</NavLink>

                        <template v-if="$page.props.auth && $page.props.auth.user">
                        <template v-if="$page.props.auth.user.role === 'Organisator'">
                            <NavLink :href="route('create')" :active="route().current('create')">Создать проект</NavLink>
                            <NavLink :href="route('profile.edit')" :active="route().current('profile.edit')">Мои проекты</NavLink>
                        </template>
                        <template v-if="$page.props.auth.user.role === 'Investor'">
                            <NavLink :href="route('profile.edit')" :active="route().current('profile.edit')">Избранное</NavLink>
                        </template>
                        </template>
                    </div>

                    <!-- Правая зона: helper + auth/burger -->
                    <div class="flex items-center gap-2 sm:gap-3 shrink-0 relative z-[60] min-w-max">
                        <AccessibilityHelper class="shrink-0" />

                        <!-- Desktop auth -->
                        <div class="hidden xl:block">
                        <template v-if="$page.props.auth && $page.props.auth.user">
                            <Dropdown align="right" width="48">
                            <template #trigger>
                                <button
                                class="flex items-center gap-3 bg-white/10 backdrop-blur-sm rounded-full px-4 py-2 hover:bg-white/20 transition-colors min-w-[180px]"
                                aria-label="Меню пользователя"
                                >
                                <div class="text-left flex-1 min-w-0">
                                    <div class="text-sm font-medium text-white truncate">
                                    {{ $page.props.auth.user.name }} {{ $page.props.auth.user.lastname }}
                                    </div>
                                    <div class="text-xs text-white/80">
                                    {{ $page.props.auth.user.role === 'Investor' ? 'Инвестор' : ($page.props.auth.user.role === 'Organisator' ? 'Инициатор' : 'Администратор') }}
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                                </button>
                            </template>
                            <template #content>
                                <div class="py-1">
                                <DropdownLink :href="route('profile.edit')" class="flex items-center gap-2 px-4 py-2 hover:bg-gray-100">
                                    Личный кабинет
                                </DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button" class="flex items-center gap-2 px-4 py-2 hover:bg-gray-100 w-full text-left">
                                    Выйти
                                </DropdownLink>
                                </div>
                            </template>
                            </Dropdown>
                        </template>

                        <template v-else>
                            <div class="flex gap-2">
                            <Link :href="route('login')" class="px-4 py-2 rounded-xl text-white hover:bg-white/10 border-2 border-[#886830] text-sm whitespace-nowrap">
                                Войти
                            </Link>
                            <Link :href="route('register')" :style="{ backgroundColor: accentColor, color: NavColor }" class="px-4 py-2 rounded-xl font-medium hover:opacity-90 transition-opacity text-sm whitespace-nowrap">
                                Регистрация
                            </Link>
                            </div>
                        </template>
                        </div>

                        <!-- Mobile burger -->
                        <button
                        @click="showingNavigationDropdown = !showingNavigationDropdown"
                        class="xl:hidden text-white p-2 rounded-lg hover:bg-white/10 transition-colors"
                        aria-label="Меню"
                        :aria-expanded="showingNavigationDropdown"
                        >
                        <svg class="h-7 w-7" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <path :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span class="sr-only">{{ showingNavigationDropdown ? 'Закрыть меню' : 'Открыть меню' }}</span>
                        </button>
                    </div>
                    </div>
                </div>
                
                <!-- Мобильное выпадающее меню -->
                <div 
                    :class="showingNavigationDropdown ? 'block' : 'hidden'" 
                    class="xl:hidden border-t" 
                    :style="{ borderColor: borderColor }"
                    role="menu"
                    aria-label="Мобильное меню"
                >
                    <div class="px-4 py-3 space-y-1">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')" @click="closeMobileMenu">
                            Главная
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('projects')" :active="route().current('projects')" @click="closeMobileMenu">
                            Проекты
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('mapProject')" :active="route().current('mapProject')" @click="closeMobileMenu">
                            Карта проектов
                        </ResponsiveNavLink>
                        
                        <template v-if="$page.props.auth && $page.props.auth.user">
                            <div v-if="$page.props.auth.user.role === 'Organisator'">
                                <ResponsiveNavLink :href="route('create')" :active="route().current('create')" @click="closeMobileMenu">
                                    Создать проект
                                </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('profile.edit')" :active="route().current('profile.edit')" @click="closeMobileMenu">
                                    Мои проекты
                                </ResponsiveNavLink>
                            </div>
                            <div v-if="$page.props.auth.user.role === 'Investor'">
                                <ResponsiveNavLink :href="route('profile.edit')" :active="route().current('profile.edit')" @click="closeMobileMenu">
                                    Избранное
                                </ResponsiveNavLink>
                            </div>
                            
                            <div class="border-t pt-3 mt-3" :style="{ borderColor: borderColor }">
                                <div class="px-4 py-2 mb-2">
                                    <div class="text-white font-medium">{{ $page.props.auth.user.name }} {{ $page.props.auth.user.lastname }}</div>
                                    <div class="text-white/70 text-sm">{{ $page.props.auth.user.role === 'Investor' ? 'Инвестор' : ($page.props.auth.user.role === 'Organisator' ? 'Инициатор' : 'Администратор') }}</div>
                                </div>
                                <ResponsiveNavLink class="mb-3" :href="route('profile.edit')" @click="closeMobileMenu">
                                    Личный кабинет
                                </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('logout')" method="post" as="button" @click="closeMobileMenu">
                                    Выйти
                                </ResponsiveNavLink>
                            </div>
                        </template>
                        <template v-else>
                            <div class="flex gap-2  ">
                                <Link :href="route('login')" class="block w-full text-center px-4 py-2.5 rounded-xl text-white border hover:bg-white/10 transition-colors" :style="{ borderColor: borderColor }" @click="closeMobileMenu">
                                    Войти
                                </Link>
                                <Link :href="route('register')" class="block w-full text-center px-4 py-2.5 rounded-xl font-medium" :style="{ backgroundColor: accentColor, color: NavColor }" @click="closeMobileMenu">
                                    Регистрация
                                </Link>
                            </div>
                        </template>
                    </div>
                </div>
            </nav>


            <!-- Отступ для фиксированного хедера -->
            <div class="pt-20 lg:pt-24"></div>

            <!-- Page Heading -->
            <header :style="{ backgroundColor: mainColor }" class="flex-shrink-0" v-if="$slots.header">
                <div class="mx-auto py-6 pb-0 px-4 sm:px-10 lg:px-16">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main id="main-content" :style="{ backgroundColor: mainColor }" class="flex-1" tabindex="-1">
                <slot />
            </main>

            <!-- Footer -->
            <footer 
                :style="{ backgroundColor: NavColor, borderTop: `4px solid ${borderColor}` }" 
                class="rounded-t-[32px] mt-auto"
                role="contentinfo"
                aria-label="Подвал сайта"
            >
                <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-14">
                    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-10">
                        
                        <div class="text-center lg:text-left">
                            <Link :href="route('dashboard')" class="inline-block mb-5" aria-label="На главную">
                                <img class="h-16 lg:h-20 w-auto object-contain" :src="logo" alt="Логотип InvestProject">
                            </Link>
                            <p class="text-white/70 text-base max-w-xs mx-auto lg:mx-0">
                                Инвестиционная платформа для поиска проектов
                            </p>
                        </div>

                        <div class="flex-1">
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-8 text-center lg:text-left">
                                <!-- Навигация -->
                                <div tabindex="0" role="region" aria-label="Навигация по сайту" class="focus:outline-none rounded-lg p-1">
                                    <h3 class="text-white font-semibold mb-4 text-lg" :style="{ color: accentColor }">Навигация</h3>
                                    <ul class="space-y-3" aria-label="Разделы сайта">
                                        <li>
                                            <Link 
                                                :href="route('dashboard')" 
                                                class="text-white/70 hover:text-white transition-colors text-base focus:outline-none rounded-md px-1"
                                                tabindex="0"
                                            >
                                                Главная
                                            </Link>
                                        </li>
                                        <li>
                                            <Link 
                                                :href="route('projects')" 
                                                class="text-white/70 hover:text-white transition-colors text-base focus:outline-none rounded-md px-1"
                                                tabindex="0"
                                            >
                                                Проекты
                                            </Link>
                                        </li>
                                        <li>
                                            <Link 
                                                :href="route('mapProject')" 
                                                class="text-white/70 hover:text-white transition-colors text-base focus:outline-none rounded-md px-1"
                                                tabindex="0"
                                            >
                                                Карта проектов
                                            </Link>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Для пользователей -->
                                <div tabindex="0" role="region" aria-label="Пользовательское меню" class="focus:outline-none rounded-lg p-1">
                                    <h3 class="text-white font-semibold mb-4 text-lg" :style="{ color: accentColor }">Для пользователей</h3>
                                    <ul class="space-y-3" aria-label="Пользовательское меню">
                                        <template v-if="$page.props.auth && $page.props.auth.user">
                                            <li>
                                                <Link 
                                                    :href="route('profile.edit')" 
                                                    class="text-white/70 hover:text-white transition-colors text-base focus:outline-none rounded-md px-1"
                                                    tabindex="0"
                                                >
                                                    Личный кабинет
                                                </Link>
                                            </li>
                                            <li v-if="$page.props.auth.user.role === 'Organisator'">
                                                <Link 
                                                    :href="route('create')" 
                                                    class="text-white/70 hover:text-white transition-colors text-base focus:outline-none rounded-md px-1"
                                                    tabindex="0"
                                                >
                                                    Создать проект
                                                </Link>
                                            </li>
                                            <li>
                                                <Link 
                                                    :href="route('logout')" 
                                                    method="post" 
                                                    as="button" 
                                                    class="text-white/70 hover:text-white transition-colors text-base focus:outline-none rounded-md px-1"
                                                    tabindex="0"
                                                >
                                                    Выйти
                                                </Link>
                                            </li>
                                        </template>
                                        <template v-else>
                                            <li>
                                                <Link 
                                                    :href="route('login')" 
                                                    class="text-white/70 hover:text-white transition-colors text-base focus:outline-none rounded-md px-1"
                                                    tabindex="0"
                                                >
                                                    Войти
                                                </Link>
                                            </li>
                                            <li>
                                                <Link 
                                                    :href="route('register')" 
                                                    class="text-white/70 hover:text-white transition-colors text-base focus:outline-none rounded-md px-1"
                                                    tabindex="0"
                                                >
                                                    Регистрация
                                                </Link>
                                            </li>
                                        </template>
                                    </ul>
                                </div>

                                <!-- Контакты -->
                                <div tabindex="0" role="region" aria-label="Контактная информация" class="focus:outline-none rounded-lg p-1">
                                    <h3 class="text-white font-semibold mb-4 text-lg" :style="{ color: accentColor }">Контакты</h3>
                                    <ul class="space-y-3" aria-label="Контактная информация">
                                        <li 
                                            class="text-white/70 text-base focus:outline-none rounded-md px-1"
                                            tabindex="0"
                                        >
                                            Email: info@investproject.ru
                                        </li>
                                        <li 
                                            class="text-white/70 text-base focus:outline-none rounded-md px-1"
                                            tabindex="0"
                                        >
                                            Телефон: +7 (932) 207-30-74
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</template>