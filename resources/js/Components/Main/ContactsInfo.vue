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

const sanitizePhone = (phone) => (phone || '').replace(/[^+\d]/g, '');
</script>
<template>
    <address class="not-italic mt-6 w-max ml-2">
        <!-- Phone -->
        <section aria-labelledby="contacts-phone-heading" class="flex flex-col mb-8 w-auto">
            <div class="border-b-[1px] border-white text-white">
                <h3 id="contacts-phone-heading" class="text-3xl pl-3 pb-4">Телефон</h3>
            </div>
            <ul class="flex flex-col ml-3 list-none m-0 p-0">
                <li v-for="phone in phonesdata" :key="phone.number">
                    <a
                        :href="`tel:${sanitizePhone(phone.number)}`"
                        class="flex justify-start items-center gap-4 mt-6 bg-[#EEEEEE] w-max px-5 py-1 rounded-3xl text-black focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-700"
                        :aria-label="`Позвонить по номеру ${phone.number}`"
                    >
                        <img class="object-cover h-14" src="../../../images/Phone.png" alt="" aria-hidden="true">
                        <span class="text-2xl max-sm:text-xl">{{ phone.number }}</span>
                    </a>
                </li>
            </ul>
        </section>

        <!-- Email -->
        <section aria-labelledby="contacts-email-heading" class="flex flex-col mb-8 w-max">
            <div class="border-b-[1px] border-white text-white">
                <h3 id="contacts-email-heading" class="text-3xl pl-3 pb-4">Почта</h3>
            </div>
            <ul class="flex flex-col ml-3 list-none m-0 p-0">
                <li v-for="email in emails" :key="email.pochta">
                    <a
                        :href="`mailto:${email.pochta}`"
                        class="flex justify-start items-center gap-4 mt-6 bg-[#EEEEEE] w-max px-5 py-1 rounded-3xl text-black focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-700"
                        :aria-label="`Написать на ${email.pochta}`"
                    >
                        <img class="object-cover h-14" src="../../../images/Email.png" alt="" aria-hidden="true">
                        <span class="text-2xl max-sm:text-xl">{{ email.pochta }}</span>
                    </a>
                </li>
            </ul>
        </section>

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
    </address>
</template>
