<script setup>
import { ref } from "vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link } from "@inertiajs/vue3";
import logo from "../../images/LogoInvestProject.png";
import AccessibilityHelper from "@/Components/Main/AccessibilityHelper.vue";

const showingNavigationDropdown = ref(false);

const mainColor = "#436343";
const NavColor = "#2A3F2A";
const accentColor = "#F8D794";
const borderColor = "#886830";

const closeMobileMenu = () => {
    showingNavigationDropdown.value = false;
};
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
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 ">
                    <slot name="header" />
                </div>
            </header>
            
            <!-- Page Content -->
            <main 
                id="main-content" 
                :style="{ backgroundColor: mainColor }" 
                class="flex-1 flex flex-col items-center justify-center min-h-[calc(100vh-200px)] py-8" 
                tabindex="-1"
            >
                <div class="w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>