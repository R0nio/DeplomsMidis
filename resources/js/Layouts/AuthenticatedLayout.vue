<script setup>
import { ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link } from "@inertiajs/vue3";

const mainColor = "#C9D8EF";
const footerColor = "#A9BCDB";
const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100 flex flex-col">
            <nav class="border-black border-b-2 bg-[#A9BCDB] sm:h-[90px] sm:flex w-full items-center">
                <!-- Primary Navigation Menu -->
                <div class="w-full px-4 sm:px-10 lg:px-16 ">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center ">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo class="block h-9 w-auto fill-current text-black" />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="flex justify-between">
                                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                    <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                        Главная
                                    </NavLink>
                                </div>
                                <!-- projects -->
                                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                    <NavLink :href="route('projects')" :active="route().current('projects')">
                                        Ивестиционные проекты
                                    </NavLink>
                                </div>
                                <!-- mapProject -->
                                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                    <NavLink :href="route('mapProject')"
                                        :active="route().current('mapProject')">
                                        Карта проектов
                                    </NavLink>
                                </div>
                                <!-- contact -->
                                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                    <NavLink :href="route('contact')" :active="route().current('contact')">
                                        Контакты
                                    </NavLink>
                                </div>
                            </div>

                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Settings Dropdown -->
                            <div class="relative ms-3 flex w-max" v-if="$page.props.auth.user">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none">
                                                {{
                                                    $page.props.auth.user
                                                        .middlename
                                                }}
                                                {{ $page.props.auth.user.name }}

                                                <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')">
                                            Личный кабинет
                                        </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button">
                                            Выйти
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>

                            <template class="flex" v-else>
                                <Link :href="route('login')"
                                    class="text-[16px] font-normal text-black hover:text-gray-500 focus:border-blue-200 p-2 mr-2">
                                    Авторизация
                                </Link>

                                <Link :href="route('register')"
                                    class="text-[16px] font-normal text-black hover:text-gray-500 focus:border-blue-200 p-2">
                                    Регистрация
                                </Link>
                            </template>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button @click="
                                showingNavigationDropdown =
                                !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{
                                        hidden: showingNavigationDropdown,
                                        'inline-flex':
                                            !showingNavigationDropdown,
                                    }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{
                                        hidden: !showingNavigationDropdown,
                                        'inline-flex':
                                            showingNavigationDropdown,
                                    }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{
                    block: showingNavigationDropdown,
                    hidden: !showingNavigationDropdown,
                }" class="sm:hidden">
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Главная
                        </ResponsiveNavLink>
                    </div>
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink :href="route('projects')" :active="route().current('projects')">
                            Инвестиционные проекты
                        </ResponsiveNavLink>
                    </div>
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink :href="route('mapProject')"
                            :active="route().current('mapProject')">
                            Карта проектов
                        </ResponsiveNavLink>
                    </div>
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink :href="route('contact')" :active="route().current('contact')">
                            Контакты
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="border-t border-gray-200 pb-1 pt-4" v-if="$page.props.auth.user">
                        <div class="px-4">
                            <div class="text-base font-medium text-gray-800">
                                {{ $page.props.auth.user.middlename }}
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Личный кабинет
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                Выйти
                            </ResponsiveNavLink>
                        </div>
                    </div>

                    <template v-else>
                        <div class="flex flex-col">
                            <Link :href="route('login')"
                                class="text-[16px] font-normal text-black hover:text-gray-500 focus:border-blue-200 p-2 mr-2">
                                Авторизация
                            </Link>

                            <Link :href="route('register')"
                                class="text-[16px] font-normal text-black hover:text-gray-500 focus:border-blue-200 p-2">
                                Регистрация
                            </Link>
                        </div>

                    </template>
                </div>
            </nav>

            <!-- Page Heading -->
            <header :class="`bg-[${mainColor}] flex-shrink-0 relative z-10`" v-if="$slots.header">
                <div class="mx-auto py-6 px-4 sm:px-10 lg:px-16 bg-[#BDCCE4]">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main :class="`bg-[${mainColor}] flex-1`">
                <slot />
            </main>

            <footer
                :class="`bg-[${footerColor}] h-[567px] border-t-2 border-black px-16 flex justify-between items-center relative z-10 flex-shrink-0`">
                <!-- firstblock -->
                <div>
                    <div class="flex shrink-0 items-center ">
                        <Link :href="route('dashboard')">
                            <ApplicationLogo class="block h-9 w-auto fill-current text-black" />
                        </Link>
                    </div>
                    <h1 class="text-2xl">InvestProject</h1>
                </div>
                <!-- SecondBlock -->
                <div class="flex gap-20">
                    <div class="flex  justify-between">
                        <div class=" hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <NavLink class="" :href="route('dashboard')" :active="route().current('dashboard')">
                                <p class="text-2xl">Главная</p>
                            </NavLink>
                        </div>
                        <!-- projects -->
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <NavLink :href="route('projects')" :active="route().current('projects')">
                                <p class="text-2xl">Инвестиционные проекты</p>
                            </NavLink>
                        </div>
                        <!-- mapProject -->
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <NavLink :href="route('mapProject')" :active="route().current('mapProject')">
                                <p class="text-2xl">Карта проектов</p>
                            </NavLink>
                        </div>
                        <!-- contact -->
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <NavLink :href="route('contact')" :active="route().current('contact')">
                                <p class="text-2xl">Контакты</p>
                            </NavLink>
                        </div>
                    </div>
                </div>
                <!-- thertiBlock -->
                 <div>
                                            <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Settings Dropdown -->
                            <div class="relative ms-3 flex w-max" v-if="$page.props.auth.user">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none">
                                                {{
                                                    $page.props.auth.user
                                                        .middlename
                                                }}
                                                {{ $page.props.auth.user.name }}

                                                <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')">
                                            Личный кабинет
                                        </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button">
                                            Выйти
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>

                            <template class="flex" v-else>
                                <Link :href="route('login')"
                                    class="text-[16px] font-normal text-black hover:text-gray-500 focus:border-blue-200 p-2 mr-2">
                                    Авторизация
                                </Link>

                                <Link :href="route('register')"
                                    class="text-[16px] font-normal text-black hover:text-gray-500 focus:border-blue-200 p-2">
                                    Регистрация
                                </Link>
                            </template>
                        </div>
                 </div>
            </footer>
        </div>
    </div>
</template>
