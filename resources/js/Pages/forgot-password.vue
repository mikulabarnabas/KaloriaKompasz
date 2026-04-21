<script setup>
import { useForm } from "laravel-precognition-vue"
import { trans as t } from 'laravel-vue-i18n';
import InputField from "@/Components/input.vue"
import AppLayout from '@/Layouts/appLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({ status: String });

const showSuccessDialog = ref(false);

const form = useForm('post', '/forgot-password', {
    email: "",
})

const onSubmit = () => {
    form.submit({
        onSuccess: () => {
            showSuccessDialog.value = true;
        }
    });
};

function goToLogin() {
    showSuccessDialog.value = false;
    router.get('/login');
}
</script>

<template>
    <AppLayout>
        <Head :title="t('auth.forgot_password')" />

        <div class="relative min-h-[75vh] bg-background-dark flex items-center justify-center px-6 py-24">
            <div class="absolute inset-0 pointer-events-none opacity-20 dark:opacity-10">
                <svg width="100%" height="100%" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M 80 0 C 80 150, 20 250, 20 400 S 90 550, 80 750" stroke="var(--color-primary)" stroke-width="2" stroke-dasharray="12 12" />
                </svg>
            </div>

            <div class="relative z-10 w-full max-w-md space-y-6 animate-fly-in">
                <div class="bg-neutral-dark p-8 md:p-10 rounded-3xl shadow-2xl border border-neutral-border/10 dark:border-neutral-border/30">
                    <header class="mb-8 text-center">
                        <h2 class="text-4xl font-black text-main-text italic tracking-tighter uppercase">
                            {{ t('auth.forgot_password').split(' ')[0] }} <span class="text-primary">{{ t('auth.forgot_password').split(' ')[1] || '' }}</span>
                        </h2>
                        <p class="text-secondary-text text-xs mt-3 font-medium leading-relaxed max-w-70 mx-auto uppercase tracking-wider">
                            {{ t('auth.forgot_password_subtitle') }}
                        </p>
                    </header>

                    <div v-if="status" class="mb-6 p-4 bg-primary/10 border border-primary/20 rounded-2xl font-bold text-[10px] text-primary text-center uppercase tracking-widest">
                        {{ status }}
                    </div>

                    <form class="space-y-6" @submit.prevent="onSubmit" novalidate>
                        <InputField
                            v-model="form.email"
                            type="email"
                            :label="t('auth.email')"
                            :error="form.errors.email"
                            placeholder="name@example.com"
                            @change="form.validate('email')"
                            autocomplete="username"
                        />

                        <button type="submit" :disabled="form.processing"
                            class="w-full py-5 rounded-full font-black uppercase tracking-[0.15em] text-xs bg-primary text-background-dark shadow-lg shadow-primary/20 hover:shadow-primary/40 active:scale-[0.97] transition-all disabled:opacity-50">
                            <span v-if="form.processing">{{ t('auth.sending') }}...</span>
                            <span v-else>{{ t('auth.send_reset_link') }}</span>
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
                            {{ t('auth.password_reset_sent_title') }}
                        </h3>
                        <p class="text-secondary-text text-[11px] font-bold uppercase tracking-widest leading-relaxed">
                            {{ t('auth.password_reset_sent_message')}}
                        </p>
                    </div>

                    <Button :label="t('auth.sign_in')" icon="login" @click="goToLogin" class="h-14" />
                </div>
            </div>
        </transition>
    </AppLayout>
</template>
