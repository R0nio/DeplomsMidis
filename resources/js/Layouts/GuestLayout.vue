<script setup>
import { ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link } from "@inertiajs/vue3";
import logo from "../../images/LogoInvestProject.png";

const showingNavigationDropdown = ref(false);

const mainColor = "#436343";
const NavColor = "#2A3F2A";
const accentColor = "#F8D794";
const borderColor = "#886830";
</script>


<template>
    <div>
        <div :style="{ backgroundColor: mainColor}" class="pt-[0px]">
            <nav 
                :style="{ backgroundColor: NavColor, borderBottom: `4px solid ${borderColor}` }" 
                class="fixed top-0 left-0 right-0 z-50 rounded-b-[32px] shadow-lg"
            >
                <div class="w-full px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-20 lg:h-24">
                        
                        <!-- Logo -->
                        <div class="flex-shrink-0">
                            <Link :href="route('dashboard')" class="flex items-center">
                                <img class="h-16 w-auto lg:h-20 object-contain" :src="logo" alt="Логотип">
                            </Link>
                        </div>

                        <!-- Navigation Links - Desktop -->
                        <div class="hidden lg:flex lg:items-center lg:space-x-2">
                            <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                Главная
                            </NavLink>
                            <NavLink :href="route('projects')" :active="route().current('projects')">
                                Проекты
                            </NavLink>
                            <NavLink :href="route('mapProject')" :active="route().current('mapProject')">
                                Карта проектов
                            </NavLink>
                            <NavLink :href="route('contact')" :active="route().current('contact')">
                                Контакты
                            </NavLink>
                            
                            <template v-if="$page.props.auth.user">
                                <div v-if="$page.props.auth.user.role === 'Organisator'">
                                    <NavLink :href="route('create')" :active="route().current('create')">
                                        Создать проект
                                    </NavLink>
                                    <NavLink :href="route('profile.edit')" :active="route().current('profile.edit')">
                                        Мои проекты
                                    </NavLink>
                                </div>
                                <div v-if="$page.props.auth.user.role === 'Investor'">
                                    <NavLink :href="route('profile.edit')" :active="route().current('profile.edit')">
                                        Избранное
                                    </NavLink>
                                </div>
                            </template>
                        </div>

                        <!-- Auth Dropdown - Desktop (отдельный блок) -->
                        <div class="hidden lg:flex lg:items-center lg:space-x-4">
                            <template v-if="$page.props.auth.user">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button class="flex items-center space-x-3 bg-white/10 backdrop-blur-sm rounded-full px-5 py-2.5 hover:bg-white/20 transition-colors">
                                            <div class="text-left">
                                                <div class="text-base font-medium text-white">
                                                    {{ $page.props.auth.user.name }} {{ $page.props.auth.user.lastname }}
                                                </div>
                                                <div class="text-sm text-white/80">
                                                    {{ $page.props.auth.user.role === 'Investor' ? 'Инвестор' : ($page.props.auth.user.role === 'Organisator' ? 'Организатор' : 'Администратор') }}
                                                </div>
                                            </div>
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                <Link :href="route('login')" class="px-5 py-2.5 rounded-xl text-white hover:bg-white/10 transition-colors text-base">
                                    Войти
                                </Link>
                                <Link :href="route('register')" :style="{ backgroundColor: accentColor, color: NavColor }" class="px-5 py-2.5 rounded-xl font-medium hover:opacity-90 transition-opacity text-base">
                                    Регистрация
                                </Link>
                            </template>
                        </div>

                        <!-- Hamburger Menu - Mobile -->
                        <div class="lg:hidden flex items-center">
                            <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="text-white p-2 rounded-lg hover:bg-white/10 transition-colors">
                                <svg class="h-7 w-7" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{ 'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{ 'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mobile Menu -->
                <div :class="showingNavigationDropdown ? 'block' : 'hidden'" class="lg:hidden border-t" :style="{ borderColor: borderColor }">
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
                        <ResponsiveNavLink :href="route('contact')" :active="route().current('contact')" @click="closeMobileMenu">
                            Контакты
                        </ResponsiveNavLink>
                        
                        <template v-if="$page.props.auth.user">
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
                                    <div class="text-white/70 text-sm">{{ $page.props.auth.user.role === 'Investor' ? 'Инвестор' : ($page.props.auth.user.role === 'Organisator' ? 'Организатор' : 'Администратор') }}</div>
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
                            <div class="border-t pt-3 mt-3" :style="{ borderColor: borderColor }">
                                <ResponsiveNavLink :href="route('login')" @click="closeMobileMenu">
                                    Войти
                                </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('register')" @click="closeMobileMenu">
                                    Регистрация
                                </ResponsiveNavLink>
                            </div>
                        </template>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header :style="{ backgroundColor: mainColor}" class="shadow" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>
            
            <div
                :style="{ backgroundColor: mainColor}"
                class="flex h-screen flex-col items-center pt-6 sm:justify-center sm:pt-0"
            >
                <div
                    :style="{ backgroundColor: mainColor}"
                    class="mt-6 w-full overflow-hidden px-6 py-4 sm:max-w-md sm:rounded-lg"
                >
                    <slot />
                </div>
            </div>
        </div>
</div>
</template>
