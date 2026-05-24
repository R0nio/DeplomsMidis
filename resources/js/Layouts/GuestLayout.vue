<script setup>
import { ref } from "vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link } from "@inertiajs/vue3";
import logo from "../../images/LogoInvestProject.png";

const showingNavigationDropdown = ref(false);

const mainColor = "#8EB6FF";
const NavColor = "#267FBE";
</script>


<template>
    <div>
        <a
            href="#main-content"
            class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-[100] focus:bg-white focus:text-black focus:px-4 focus:py-2 focus:rounded-md focus:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-700"
        >
            Перейти к основному содержимому
        </a>
        <div :style="{ backgroundColor: mainColor}" class="pt-[0px]">
            <nav
                aria-label="Основная навигация"
                :style="{ backgroundColor: NavColor, boxShadow: 'inset 0 -2px 0 0 #4E4E4E, 0 6px 9px rgba(0, 0, 0, 0.25)' }"
                class="fixed top-0 left-0 z-50 border-[#4E4E4E] mb-4 border-b-2 pb-4 xl:h-[100px] xl:flex w-full items-center rounded-b-[56px]"
            >
                <!-- Primary Navigation Menu -->
                <div class="w-full px-12 md:px-8 lg:px-16">
                    <div class="flex h-16 justify-between">
                        <div class="flex max-xl:justify-between max-xl:w-full">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center ">
                                <Link :href="route('dashboard')" aria-label="InvestProject — на главную">
                                    <img class="w-24 h-18 object-cover" :src="logo" alt="" aria-hidden="true">
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <ul class="flex justify-between items-center list-none m-0 p-0">
                                <li class="hidden space-x-8 xl:-my-px xl:ms-10 xl:flex max-xl:ms-3">
                                    <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                        Главная страница
                                    </NavLink>
                                </li>
                                <!-- projects -->
                                <li class="hidden space-x-8 xl:-my-px xl:ms-10 xl:flex max-xl:ms-3">
                                    <NavLink :href="route('projects')" :active="route().current('projects')">
                                        Инвестиционные проекты
                                    </NavLink>
                                </li>
                                <!-- mapProject -->
                                <li class="hidden space-x-8 xl:-my-px xl:ms-10 xl:flex max-xl:ms-3">
                                    <NavLink :href="route('mapProject')" :active="route().current('mapProject')">
                                        Карта проектов
                                    </NavLink>
                                </li>
                                <!-- contact -->
                                <li class="hidden space-x-8 xl:-my-px xl:ms-10 xl:flex max-xl:ms-3">
                                    <NavLink :href="route('contact')" :active="route().current('contact')">
                                        Контакты
                                    </NavLink>
                                </li>
                            </ul>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center xl:hidden">
                            <button
                                type="button"
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                :aria-expanded="showingNavigationDropdown ? 'true' : 'false'"
                                aria-controls="guest-mobile-menu"
                                :aria-label="showingNavigationDropdown ? 'Закрыть меню' : 'Открыть меню'"
                                class="inline-flex items-center justify-center rounded-md p-2 text-white transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-700 focus:bg-gray-100 focus:text-gray-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-white"
                            >
                                <svg class="h-8 w-8" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                    <path :class="{
                                        hidden: showingNavigationDropdown,
                                        'inline-flex': !showingNavigationDropdown,
                                    }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{
                                        hidden: !showingNavigationDropdown,
                                        'inline-flex': showingNavigationDropdown,
                                    }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    id="guest-mobile-menu"
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="xl:hidden p-4"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Главная страница
                        </ResponsiveNavLink>
                    </div>
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink :href="route('projects')" :active="route().current('projects')">
                            Инвестиционные проекты
                        </ResponsiveNavLink>
                    </div>
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink :href="route('mapProject')" :active="route().current('mapProject')">
                            Карта проектов
                        </ResponsiveNavLink>
                    </div>
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink :href="route('contact')" :active="route().current('contact')">
                            Контакты
                        </ResponsiveNavLink>
                    </div>
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink :href="route('login')" :active="route().current('login')">
                            Войти
                        </ResponsiveNavLink>
                    </div>
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink :href="route('register')" :active="route().current('register')">
                            Регистрация
                        </ResponsiveNavLink>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header :style="{ backgroundColor: mainColor}" class="shadow" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <main
                id="main-content"
                tabindex="-1"
                :style="{ backgroundColor: mainColor}"
                class="flex h-screen flex-col items-center pt-6 sm:justify-center sm:pt-0 focus:outline-none"
            >
                <div
                    :style="{ backgroundColor: mainColor}"
                    class="mt-6 w-full overflow-hidden px-6 py-4 sm:max-w-md sm:rounded-lg"
                >
                    <slot />
                </div>
            </main>
        </div>
</div>
</template>
