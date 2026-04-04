<script setup>
import { ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link } from "@inertiajs/vue3";


const showingNavigationDropdown = ref(false);


</script>


<template>
            <nav class="border-black border-b-2 bg-[#A9BCDB] sm:h-[90px] sm:flex w-full items-center">
                <!-- Primary Navigation Menu -->
                <div class="w-full px-4 sm:px-10 lg:px-16 ">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center ">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-black"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                             <div class="flex justify-between">
<div
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                >
                                    Главная страница
                                </NavLink>
                            </div>
                            <!-- projects -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    :href="route('projects')"
                                    :active="route().current('projects')"
                                >
                                    Инвестиционные проекты
                                </NavLink>
                            </div>
                            <!-- mapProject -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    :href="route('mapProject')"
                                    :active="route().current('mapProject')"
                                >
                                    Карта проектов
                                </NavLink>
                            </div>
                            <!-- contact -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    :href="route('contact')"
                                    :active="route().current('contact')"
                                >
                                    Контакты
                                </NavLink>
                            </div>
                             </div>
                            
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            Главная страница
                        </ResponsiveNavLink>
                    </div>
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            :href="route('projects')"
                            :active="route().current('projects')"
                        >
                           Инвестиционные проекты
                        </ResponsiveNavLink>
                    </div>
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            :href="route('mapProject')"
                            :active="route().current('mapProject')"
                        >
                            Карта проектов
                        </ResponsiveNavLink>
                    </div>
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            :href="route('contact')"
                            :active="route().current('contact')"
                        >
                            Контакты
                        </ResponsiveNavLink>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>
    <div
        class="flex h-[90vh] flex-col items-center bg-white pt-6 sm:justify-center sm:pt-0"
    >
        <div
            class="mt-6 w-full overflow-hidden bg-white px-6 py-4 sm:max-w-md sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
