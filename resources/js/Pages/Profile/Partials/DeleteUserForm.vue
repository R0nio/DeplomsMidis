<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-6" aria-labelledby="delete-account-heading">
        <header>
            <h2 id="delete-account-heading" class="text-lg font-medium text-gray-900">
                Удалить аккаунт
            </h2>

            <p class="mt-1 text-sm text-gray-800">
                После удаления аккаунта все связанные с ним данные будут безвозвратно удалены.
                Перед удалением сохраните все важные данные.
            </p>
        </header>

        <DangerButton type="button" @click="confirmUserDeletion">Удалить аккаунт</DangerButton>

        <Modal :show="confirmingUserDeletion" @close="closeModal" aria-labelledby="delete-confirm-heading">
            <div class="p-6">
                <h2
                    id="delete-confirm-heading"
                    class="text-lg font-medium text-gray-900"
                >
                    Вы уверены, что хотите удалить аккаунт?
                </h2>

                <p class="mt-1 text-sm text-gray-800">
                    После удаления аккаунта все его данные будут безвозвратно
                    удалены. Введите пароль для подтверждения удаления.
                </p>

                <div class="mt-6">
                    <InputLabel
                        for="delete-password"
                        value="Пароль"
                        class="sr-only"
                    />

                    <TextInput
                        id="delete-password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        autocomplete="current-password"
                        class="mt-1 block w-3/4"
                        placeholder="Пароль"
                        :invalid="!!form.errors.password"
                        described-by="delete-password-error"
                        @keyup.enter="deleteUser"
                    />

                    <InputError id="delete-password-error" :message="form.errors.password" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton type="button" @click="closeModal">
                        Отмена
                    </SecondaryButton>

                    <DangerButton
                        type="button"
                        class="ms-3"
                        :class="{ 'opacity-50': form.processing }"
                        :disabled="form.processing"
                        :aria-busy="form.processing ? 'true' : 'false'"
                        @click="deleteUser"
                    >
                        Удалить аккаунт
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
