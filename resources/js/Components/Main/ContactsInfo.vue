<script setup>
defineProps({
    phonesdata:{
        type:Array
    },
    emails:{
        type:Array
    },
    socials:{
        type:Array
    },
})
</script>
<template>
    <div class="mt-6 w-max ml-2">
        <!-- Phone -->
        <div class="flex flex-col mb-8 w-auto" >
            <div class="border-b-[1px] border-white text-white">
                <h3 class="text-3xl pl-3 pb-4">Телефон</h3>
            </div>
            <div class="flex flex-col ml-3" v-for="phone in phonesdata">
                <div class="flex justify-start items-center gap-4 mt-6 bg-[#EEEEEE] w-max px-5 py-1 rounded-3xl">
                    <img class="object-cover h-14" src="../../../images/Phone.png" alt="">
                    <p class="text-2xl max-sm:text-xl">{{ phone.number }}</p>
                </div>
            </div>
        </div>

        <!-- Email -->
        <div class="flex flex-col mb-8 w-max" >
            <div class="border-b-[1px] border-white text-white">
                <h3 class="text-3xl pl-3 pb-4">Почта</h3>
            </div>
            <div class="flex flex-col ml-3" v-for="email in emails">
                <div class="flex justify-start items-center gap-4 mt-6 bg-[#EEEEEE] w-max px-5 py-1 rounded-3xl">
                    <img class="object-cover h-14" src="../../../images/Email.png" alt="">
                    <p class="text-2xl max-sm:text-xl">{{ email.pochta }}</p>
                </div>
            </div>
        </div>

        <!-- Social 
        <div class="flex flex-col mb-8 w-max" >
            <div class="border-b-[1px] border-white text-white">
                <h3 class="text-3xl pl-3 pb-4">Социальные сети</h3>
            </div>
            <div class="flex flex-col ml-3" v-for="social in socials">
                <div class="flex justify-start items-center gap-4 mt-6 bg-[#EEEEEE] w-max px-5 py-1 rounded-3xl">
                    <img class="object-cover h-14" :src="social.img" alt="">
                    <p class="text-3xl">{{ social.name }}</p>
                </div>
            </div>
        </div>-->
    </div>
</template>