<script setup>
import { ref } from "vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link } from "@inertiajs/vue3";
import logo from "../../images/LogoInvestProject.png";

const mainColor = "#8EB6FF";
const NavColor = "#267FBE";
const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div :style="{ backgroundColor: mainColor}" :class="`min-h-screen flex flex-col pt-[100px] xl:pt-[100px] mx-auto`">
            <nav :style="{ backgroundColor: NavColor, boxShadow: 'inset 0 -2px 0 0 #4E4E4E, 0 6px 9px rgba(0, 0, 0, 0.25)' }" class="fixed top-0 left-0 z-50 border-[#4E4E4E] mb-4 border-b-2 pb-4 xl:h-[100px] xl:flex w-full items-center rounded-b-[56px]">
                <!-- Обычное меню -->
                <div class="w-full px-12 md:px-8 lg:px-16">
                    <div class="flex h-16 justify-between">
                        <div class="flex max-xl:justify-between">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center ">
                                <Link :href="route('dashboard')">
                                    <img class="xl:w-24 xl:h-18 w-22 h-16  object-cover" :src="logo" alt="Логотип">
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="flex justify-between items-center w-auto">
                                <div class="hidden xl:space-x-8 xl:-my-px xl:ms-5 xl:flex">
                                    <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                        Главная
                                    </NavLink>
                                </div>
                                <!-- projects -->
                                <div class="hidden xl:space-x-8 xl:-my-px xl:ms-5 xl:flex">
                                    <NavLink :href="route('projects')" :active="route().current('projects')">
                                        Проекты
                                    </NavLink>
                                </div>
                                <!-- mapProject -->
                                <div class="hidden xl:space-x-8 xl:-my-px xl:ms-5 xl:flex">
                                    <NavLink :href="route('mapProject')"
                                        :active="route().current('mapProject')">
                                        Карта проектов
                                    </NavLink>
                                </div>
                                <div v-if="$page.props.auth.user" class="flex">
                                    <!-- Создать проект -->
                                    <div v-if="$page.props.auth.user.role === 'Organisator'" class="hidden xl:space-x-8 xl:-my-px xl:ms-5 xl:flex">
                                        <NavLink :href="route('create')" :active="route().current('create')">
                                            Создать проект
                                        </NavLink>
                                    </div>
                                </div>
                                <!-- создать избранные -->
                                <div v-if="$page.props.auth.user" class="flex">
                                    <div v-if="$page.props.auth.user.role === 'Organisator'" class="hidden xl:space-x-8 xl:-my-px xl:ms-5 xl:flex">
                                        <NavLink :href="route('profile.edit')" :active="route().current('profile.edit')">
                                            Мои проекты
                                        </NavLink>
                                    </div>

                                    <!-- Избранные проекты -->
                                    <div v-if="$page.props.auth.user.role === 'Investor'" class="hidden xl:space-x-8 xl:-my-px xl:ms-5 xl:flex">
                                        <NavLink :href="route('profile.edit')" :active="route().current('profile.edit')">
                                            Избранные проекты
                                        </NavLink>
                                    </div>
                                </div>
                                <div v-else>
                                </div>

                                <!-- contact -->
                                <div class="hidden xl:space-x-8 xl:-my-px xl:ms-5 xl:flex">
                                    <NavLink :href="route('contact')" :active="route().current('contact')">
                                        Контакты
                                    </NavLink>
                                </div>
                            </div>

                        </div>

                        <!-- Отображение кнопки выпадающего меню при авторизации иначе войти и регистр -->
                        <div class="hidden xl:ms-6 xl:flex xl:items-center">
                            <!-- Settings Dropdown -->
                            <div class="relative ms-3 flex w-max" v-if="$page.props.auth.user">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-xl">
                                            <button type="button"
                                                class="inline-flex items-center rounded-xl border border-transparent bg-white 2xl:p-3 p-2 2xl:text-lg font-medium leading-5 text-black transition duration-150 ease-in-out hover:text-gray-400 focus:outline-none">
                                                <div class="flex gap-3 items-center max-2xl:flex-col">
                                                    <div class="border-b-2 px-2 border-black">
                                                        {{$page.props.auth.user.middlename}}
                                                        {{ $page.props.auth.user.name }}
                                                        {{ $page.props.auth.user.lastname }}
                                                    </div>
                                                
                                                    <div>
                                                        <div v-if="$page.props.auth.user.role === 'Investor'">Инвестор</div>
                                                        <div v-if="$page.props.auth.user.role === 'Organisator'">Организатор</div>
                                                    </div>
                                                
                                                </div>

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
                                        <div class="flex flex-col">                                            
                                            <DropdownLink :href="route('profile.edit')" class="border-t-2">
                                                Личный кабинет
                                            </DropdownLink>
                                            <DropdownLink :href="route('logout')" method="post" as="button">
                                                Выйти
                                            </DropdownLink>
                                        </div>

                                    </template>
                                </Dropdown>
                            </div>

                            <template class="flex" v-else>
                                <Link :href="route('login')"
                                    class="text-[16px] font-normal bg-[#EEEEEE] p-2.5 rounded-xl text-black hover:text-gray-500 focus:border-blue-200 mr-10">
                                    Войти
                                </Link>

                                <Link :href="route('register')"
                                    class="text-[16px] font-normal bg-[#EEEEEE] p-2.5 rounded-xl text-black hover:text-gray-500 focus:border-blue-200">
                                    Зарегистрироваться
                                </Link>
                            </template>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center xl:hidden">
                            <div v-if="$page.props.auth.user" class="flex gap-3 items-center text-white mr-5 xl:text-xl text-sm md::flex-row flex-col">
                                <div class="border-b-2 px-2 border-white">
                                    {{$page.props.auth.user.middlename}}
                                    {{ $page.props.auth.user.name }}
                                    {{ $page.props.auth.user.lastname }}
                                </div>
                            
                                <div>
                                    <div v-if="$page.props.auth.user.role === 'Investor'">Инвестор</div>
                                    <div v-if="$page.props.auth.user.role === 'Organisator'">организатор</div>
                                </div>
                            </div>

                            <button @click="
                                showingNavigationDropdown =
                                !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-white transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none">
                                <svg class="h-8 w-8" stroke="currentColor" fill="none" viewBox="0 0 24 24">
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

                <!-- Выпадающее меню -->
                <div :class="{
                    block: showingNavigationDropdown,
                    hidden: !showingNavigationDropdown,
                }" class="xl:hidden p-4 h-max">
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Главная
                        </ResponsiveNavLink>
                    </div>
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink :href="route('projects')" :active="route().current('projects')">
                            Проекты
                        </ResponsiveNavLink>
                    </div>
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink :href="route('mapProject')"
                            :active="route().current('mapProject')">
                            Карта проектов
                        </ResponsiveNavLink>
                    </div>
                    <div v-if="$page.props.auth.user" class="flex flex-col">
                        <!-- Создать проект -->
                        <div v-if="$page.props.auth.user.role === 'Organisator'" class="space-y-1 pb-3 pt-2">
                            <ResponsiveNavLink :href="route('contact')" :active="route().current('contact')">
                                Создать проект
                            </ResponsiveNavLink>
                        </div>

                        <!-- Мои проекты -->
                        <div v-if="$page.props.auth.user.role === 'Organisator'" class="space-y-1 pb-3 pt-2">
                            <ResponsiveNavLink :href="route('profile.edit')" :active="route().current('profile.edit')">
                                Мои проекты
                            </ResponsiveNavLink>
                        </div>

                        <!-- Избранные проекты -->
                        <div v-if="$page.props.auth.user.role === 'Investor'" class="space-y-1 pb-3 pt-2">
                            <ResponsiveNavLink :href="route('profile.edit')" :active="route().current('profile.edit')">
                                Избранные проекты
                            </ResponsiveNavLink>
                        </div>

                    </div>
                    <div v-else>
                    </div>
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink :href="route('contact')" :active="route().current('contact')">
                            Контакты
                        </ResponsiveNavLink>
                    </div>
                    <!-- Responsive Settings Options -->
                    <div class="border-t border-gray-200 pb-1 pt-4" v-if="$page.props.auth.user">
                            <div v-if="$page.props.auth.user" class="flex gap-3 ml-4 items-center text-white mr-5">
                                <div class="border-b-2 px-2 border-white text-2xl">
                                    {{$page.props.auth.user.middlename}}
                                    {{ $page.props.auth.user.name }}
                                    {{ $page.props.auth.user.lastname }}
                                </div>
                            
                                <div class="text-2xl">
                                    <div v-if="$page.props.auth.user.role === 'Investor'">Инвестор</div>
                                    <div v-if="$page.props.auth.user.role === 'Organisator'">организатор</div>
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
            <header :style="{ backgroundColor: mainColor }" class="flex-shrink-0" v-if="$slots.header">
                <div class="mx-auto pt-6 px-10 text-lg max-sm:px-4 xl:px-16 lg:px-16">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main :style="{ backgroundColor: mainColor }" class="flex-1">
                <slot />
            </main>

            <footer :style="{ backgroundColor: NavColor, boxShadow: 'inset 0 2px 0 0 #4E4E4E, 0 -4px 9px rgba(0, 0, 0, 0.25)' }" class="bg-[${NavColor}] max-h-[300px] max-xl:max-h-[450px] max-sm:max-h-max pb-8 py-8 border-t-2 border-[#4E4E4E] px-16 flex justify-between items-center relative z-10 flex-shrink-0 rounded-t-[56px] max-xl:flex-col">
                <!-- firstblock -->
                <div>   
                    <!-- Logo -->
                    <div class="flex shrink-0 items-center ">
                        <Link :href="route('dashboard')">
                            <img class="w-38 h-28 object-cover mb-8" :src="logo" alt="Логотип">
                        </Link>
                    </div>
                </div>
                <!-- SecondBlock -->
                <div class="flex gap-10 items-center my-6 max-[480px]:border-y-2">
                    <div class="grid grid-rows-2 grid-cols-3 gap-10 max-sm:grid-cols-2 max-sm:grid-rows-3 max-[480px]:grid-cols-1 max-[480px]:gap-5 max-[480px]:my-6">
                        <div class="space-x-8 xl:-my-px xl:ms-10 xl:flex">
                            <NavLink class="" :href="route('dashboard')" :active="route().current('dashboard')">
                                <p class="text-2xl  max-lg:text-[16px]">Главная</p>
                            </NavLink>
                        </div>
                        <!-- projects -->
                        <div class="space-x-8 xl:-my-px xl:ms-10 xl:flex">
                            <NavLink :href="route('projects')" :active="route().current('projects')">
                                <p class="text-2xl  max-lg:text-[16px]">Проекты</p>
                            </NavLink>
                        </div>
                        <!-- mapProject -->
                        <div class="space-x-8 xl:-my-px xl:ms-10 xl:flex">
                            <NavLink :href="route('mapProject')" :active="route().current('mapProject')">
                                <p class="text-2xl  max-lg:text-[16px]">Карта проектов</p>
                            </NavLink>
                        </div>
                        <!-- contact -->
                        <div class="space-x-8 xl:-my-px xl:ms-10 xl:flex ">
                            <NavLink :href="route('contact')" :active="route().current('contact')">
                                <p class="text-2xl  max-lg:text-[16px]">Контакты</p>
                            </NavLink>
                        </div>

                        <!-- Избранные проекты -->
                        <div v-if="$page.props.auth.user">
                            <!-- Избранные проекты -->
                            <div v-if="$page.props.auth.user.role === 'Investor'" class="space-x-8 xl:-my-px xl:ms-10 xl:flex">
                                <NavLink :href="route('profile.edit')" :active="route().current('profile.edit')">
                                    <p class="text-2xl  max-lg:text-[16px]">Избранные проекты</p>
                                </NavLink>  
                            </div>
                            <!-- Создать проект -->
                            <div v-if="$page.props.auth.user.role === 'Organisator'" class="space-x-8 xl:-my-px xl:ms-10 xl:flex">
                                <NavLink :href="route('contact')" :active="route().current('contact')">
                                    <p class="text-2xl  max-lg:text-[16px]">Создать проект</p>
                                </NavLink>  
                            </div>
                        </div>
                        <div v-if="$page.props.auth.user">
                          <!-- Создать проект -->
                            <div v-if="$page.props.auth.user.role === 'Organisator'" class="space-x-8 xl:-my-px xl:ms-10 xl:flex">
                                <NavLink :href="route('profile.edit')" :active="route().current('profile.edit')">
                                    <p class="text-2xl  max-lg:text-[16px]">Мои проекты</p>
                                </NavLink>  
                            </div>
                        </div>
                        <div class="flex" v-else>

                        </div>
                    </div>
                </div>
                <!-- thertiBlock -->
                <div class="xl:ms-6 xl:flex xl:items-center max-sm:flex max-sm:flex-col">
                    <!-- Settings Dropdown -->
                    <div class="flex max-sm:flex-col gap-4" v-if="$page.props.auth.user">
                        <Link :href="route('profile.edit')"
                        class="max-sm:w-full w-max text-center text-[16px] font-normal bg-[#EEEEEE] p-2.5 rounded-xl text-black hover:text-gray-500 focus:border-blue-200 max-sm:mb-4">
                            Личный кабинет
                        </Link>
                        <Link :href="route('logout')" method="post" as="button"
                        class="max-sm:w-full w-max text-center text-[16px] font-normal bg-[#EEEEEE] p-2.5 rounded-xl text-black hover:text-gray-500 focus:border-blue-200 max-sm:mb-4">
                            Выйти
                        </Link>
                    </div>

                    <template class="flex" v-else>
                        <Link :href="route('login')"
                            class="w-full text-center text-[16px] font-normal bg-[#EEEEEE] p-2.5 rounded-xl text-black hover:text-gray-500 focus:border-blue-200 mr-10 max-sm:mb-4">
                            Войти
                        </Link>

                        <Link :href="route('register')"
                            class="text-[16px] font-normal bg-[#EEEEEE] p-2.5 rounded-xl text-black hover:text-gray-500 focus:border-blue-200">
                            Зарегистрироваться
                        </Link>
                    </template>
                </div>
            </footer>
        </div>
    </div>
</template>
