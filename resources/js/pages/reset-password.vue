<script setup>
import { ref } from "vue"; // Ref hozzáadva a popup kezeléséhez
import { useForm } from "laravel-precognition-vue"
import { trans as t } from 'laravel-vue-i18n';
import InputField from "@/Components/input.vue"
import AppLayout from '@/Layouts/appLayout.vue';
import Button from "@/Components/button.vue"; // Button komponens importálása a popup-hoz
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    token: String,
    email: String,
});

const showSuccessDialog = ref(false); // Popup láthatósága

const form = useForm('post', '/reset-password', {
    token: props.token,
    email: props.email || '',
    password: '',
    password_confirmation: '',
});

const onSubmit = () => {
    form.submit({
        onSuccess: () => {
            showSuccessDialog.value = true;
        },
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

function goToLogin() {
    showSuccessDialog.value = false;
    router.get('/login');
}
</script>

<template>
    <AppLayout>
        <Head :title="t('auth.reset_password_button')" />

        <div class="relative min-h-[75vh] bg-background-dark flex items-center justify-center px-6 py-24 overflow-hidden">
            <div class="relative z-10 w-full max-w-md space-y-6 animate-fly-in">
                <div class="bg-neutral-dark p-8 md:p-10 rounded-3xl shadow-2xl border border-neutral-border/10 dark:border-neutral-border/30">
                    <header class="mb-8 text-center">
                        <h2 class="text-4xl font-black text-main-text italic tracking-tighter uppercase">
                            {{ t('auth.password') }} <span class="text-primary">reset</span>
                        </h2>
                    </header>

                    <form class="space-y-5" @submit.prevent="onSubmit" novalidate>
                        <InputField v-model="form.email" type="email" :label="t('auth.email')" :error="form.errors.email" readonly class="opacity-60 pointer-events-none" />
                        <InputField v-model="form.password" type="password" :label="t('auth.new_password')" :error="form.errors.password" autocomplete="new-password" placeholder="••••••••" />
                        <InputField v-model="form.password_confirmation" type="password" :label="t('auth.password_confirmation')" :error="form.errors.password_confirmation" autocomplete="new-password" placeholder="••••••••" />

                        <button type="submit" :disabled="form.processing"
                            class="w-full py-5 rounded-full font-black uppercase tracking-[0.15em] text-xs bg-primary text-background-dark shadow-lg shadow-primary/20 hover:shadow-primary/40 active:scale-[0.97] transition-all disabled:opacity-50 mt-4">
                            <span v-if="form.processing">{{ t('auth.updating') }}...</span>
                            <span v-else>{{ t('auth.reset_password_button') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div v-if="showSuccessDialog" class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-background-dark/90 backdrop-blur-md">
                <div class="w-full max-w-sm bg-neutral-dark border border-primary/20 rounded-[3rem] p-10 text-center space-y-8 shadow-2xl">
                    <div class="size-24 bg-primary/10 rounded-full flex items-center justify-center border border-primary/20 mx-auto">
                        <span class="material-symbols-outlined text-5xl text-primary">lock_reset</span>
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-2xl font-black uppercase tracking-tighter text-main-text">
                            {{ t('auth.password_reset_success_title') ?? 'Sikeres frissítés!' }}
                        </h3>
                        <p class="text-secondary-text text-[11px] font-bold uppercase tracking-widest leading-relaxed">
                            {{ t('auth.password_reset_success_message') ?? 'A jelszavad sikeresen megváltozott. Most már bejelentkezhetsz az új jelszavaddal.' }}
                        </p>
                    </div>

                    <Button :label="t('auth.go_to_login') ?? 'Bejelentkezés'" icon="login" @click="goToLogin" class="h-14" />
                </div>
            </div>
        </transition>
    </AppLayout>
</template>
