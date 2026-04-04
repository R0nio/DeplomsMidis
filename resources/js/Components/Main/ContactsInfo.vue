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
    <div class="mt-16">
        <!-- Phone -->
        <div class="flex flex-col mb-8 w-[674px]" >
            <div class="border-b-[1px] border-black">
                <h3 class="text-3xl pl-3 h-[50px]">Телефон</h3>
            </div>
            <div class="flex flex-col" v-for="phone in phonesdata">
                <div class="flex justify-start items-center gap-8 mt-6">
                    <img class="object-cover h-14" src="../../../images/Phone.png" alt="">
                    <p class="text-3xl">{{ phone.number }}</p>
                </div>
            </div>
        </div>

        <!-- Email -->
        <div class="flex flex-col mb-8 w-[674px]" >
            <div class="border-b-[1px] border-black">
                <h3 class="text-3xl pl-3 h-[50px]">Почта</h3>
            </div>
            <div class="flex flex-col" v-for="email in emails">
                <div class="flex justify-start items-center gap-8 mt-6">
                    <img class="object-cover h-14" src="../../../images/Email.png" alt="">
                    <p class="text-3xl">{{ email.pochta }}</p>
                </div>
            </div>
        </div>

        <!-- Social -->
        <div class="flex flex-col mb-8 w-[674px]" >
            <div class="border-b-[1px] border-black">
                <h3 class="text-3xl pl-3 h-[50px]">Социальные сети</h3>
            </div>
            <div class="flex flex-col" v-for="social in socials">
                <div class="flex justify-start items-center gap-8 mt-6">
                    <img class="object-cover h-14" :src="social.img" alt="">
                    <p class="text-3xl">{{ social.name }}</p>
                </div>
            </div>
        </div>
    </div>
</template>