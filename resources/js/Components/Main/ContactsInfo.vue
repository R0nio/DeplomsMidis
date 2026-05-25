<script setup>
defineProps({
    phonesdata: {
        type: Array,
        default: () => []
    },
    emails: {
        type: Array,
        default: () => []
    },
    socials: {
        type: Array,
        default: () => []
    },
})
</script>

<template>
    <div class="mt-6 w-full">
        <!-- Phone -->
        <div class="flex flex-col mb-8 w-full">
            <div class="border-b-2 border-[#886830] mb-4">
                <h3 class="text-2xl sm:text-3xl pb-3 pl-2 font-semibold" style="color: #F8D794">Телефон</h3>
            </div>
            <div class="flex flex-col gap-4" v-for="phone in phonesdata" :key="phone.number">
                <div class="flex items-center gap-4 p-3 rounded-xl w-full sm:w-auto" style="background-color: #284139; border: 2px solid #886830">
                    <img class="w-10 h-10 sm:w-12 sm:h-12 object-contain" src="../../../images/Phone.png" alt="Phone">
                    <p class="text-base sm:text-xl font-medium" style="color: #F8D794">{{ phone.number }}</p>
                </div>
            </div>
        </div>

        <!-- Email -->
        <div class="flex flex-col mb-8 w-full">
            <div class="border-b-2 border-[#886830] mb-4">
                <h3 class="text-2xl sm:text-3xl pb-3 pl-2 font-semibold" style="color: #F8D794">Почта</h3>
            </div>
            <div class="flex flex-col gap-4" v-for="email in emails" :key="email.pochta">
                <div class="flex items-center gap-4 p-3 rounded-xl w-full sm:w-auto" style="background-color: #284139; border: 2px solid #886830">
                    <img class="w-10 h-10 sm:w-12 sm:h-12 object-contain" src="../../../images/Email.png" alt="Email">
                    <p class="text-base sm:text-xl font-medium" style="color: #F8D794">{{ email.pochta }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
* {
    box-shadow: none !important;
}
</style>