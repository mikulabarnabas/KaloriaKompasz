<script setup>
import { useForm } from "laravel-precognition-vue"
import { trans as t } from 'laravel-vue-i18n';
import InputField from "@/Components/input.vue"
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    token: String,
    email: String,
});

const form = useForm('post', '/reset-password', {
    token: props.token,
    email: props.email || '',
    password: '',
    password_confirmation: '',
});

const onSubmit = () => form.submit({
    onFinish: () => form.reset('password', 'password_confirmation'),
});
</script>

<template>
    <AppLayout>
        <Head :title="t('auth.reset_password_button')" />
        
        <div class="relative min-h-[75vh] bg-background-dark flex items-center justify-center px-6 py-24 overflow-hidden">
            <div class="absolute inset-0 pointer-events-none opacity-20 dark:opacity-10">
                <svg width="100%" height="100%" fill="none" xmlns="http://www.w3.org/2000/vue">
                    <path d="M 20 0 C 20 150, 80 250, 80 400 S 10 550, 20 750" stroke="var(--color-primary)" stroke-width="2" stroke-dasharray="12 12" />
                </svg>
            </div>

            <div class="relative z-10 w-full max-w-md space-y-6 animate-fly-in">
                <div class="bg-neutral-dark p-8 md:p-10 rounded-3xl shadow-2xl border border-neutral-border/10 dark:border-neutral-border/30">
                    <header class="mb-8 text-center">
                        <h2 class="text-4xl font-black text-main-text italic tracking-tighter uppercase">
                            {{ t('auth.password') }} <span class="text-primary">reset</span>
                        </h2>
                    </header>

                    <form class="space-y-5" @submit.prevent="onSubmit" novalidate>
                        <InputField 
                            v-model="form.email" 
                            type="email" 
                            :label="t('auth.email')" 
                            :error="form.errors.email"
                            readonly
                            class="opacity-60 pointer-events-none"
                        />

                        <InputField 
                            v-model="form.password" 
                            type="password" 
                            :label="t('auth.new_password')"
                            :error="form.errors.password" 
                            autocomplete="new-password" 
                            placeholder="••••••••"
                            @change="form.validate('password')" 
                        />

                        <InputField 
                            v-model="form.password_confirmation" 
                            type="password" 
                            :label="t('auth.password_confirmation')"
                            :error="form.errors.password_confirmation" 
                            autocomplete="new-password" 
                            placeholder="••••••••"
                            @change="form.validate('password_confirmation')" 
                        />

                        <button type="submit" :disabled="form.processing"
                            class="w-full py-5 rounded-full font-black uppercase tracking-[0.15em] text-xs bg-primary text-background-dark shadow-lg shadow-primary/20 hover:shadow-primary/40 active:scale-[0.97] transition-all disabled:opacity-50 mt-4">
                            <span v-if="form.processing">{{ t('auth.updating') }}...</span>
                            <span v-else>{{ t('auth.reset_password_button') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>