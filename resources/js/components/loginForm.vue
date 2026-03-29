<script setup>
import { useForm } from "laravel-precognition-vue"
import { trans as t } from 'laravel-vue-i18n';
import InputField from "@/Components/input.vue"
import { SocialLogin } from '@capgo/capacitor-social-login';
import { Device } from '@capacitor/device';
import axios from "axios";
const emit = defineEmits(['success']);

const form = useForm('post', '/login', {
    email: "",
    password: "",
    remember: false,
})

const onSubmit = () =>
    form.submit().then(() => {
        emit('success', true);
        form.reset();
    }).catch(() => {
        emit('success', false);
    })

const loginWithGoogle = async () => {
    const info = await Device.getInfo();

    if (info.platform === 'web') {
        window.location.href = '/auth/google/redirect';
        return;
    }

    try {
        await SocialLogin.initialize({
            google: {
                webClientId: '10740457262-47dacbavcs5blgon888e89us8tcp5504.apps.googleusercontent.com',
                mode: 'online'
            },
        });

        const result = await SocialLogin.login({
            provider: 'google',
            options: {
                scopes: ['email', 'profile'],
            },
        });

        if (result.result?.idToken) {
            const response = await axios.post('/auth/google/callback', {
                token: result.result.idToken
            });

            if (response.data.success) {
                console.log("Laravel beléptetés sikeres!");
                window.location.href = '/';
                emit('success', true);
            }
        }
    } catch (error) {
        console.error("Google hiba:", error);
        emit('success', false);
    }
}
</script>

<template>
    <div class="space-y-6 animate-fly-in">
        <header>
            <h2 class="text-2xl font-bold text-main-text tracking-tight">
                {{ t('auth.welcome_back') }}
            </h2>
            <p class="text-secondary-text text-sm mt-1">
                {{ t('auth.login_subtitle') }}
            </p>
        </header>

        <form class="space-y-5" @submit.prevent="onSubmit" novalidate>
            <InputField v-model="form.email" type="email" :label="t('auth.email')" :error="form.errors.email"
                placeholder="name@example.com" @change="form.validate('email')" autocomplete="username" />

            <div class="relative">
                <InputField v-model="form.password" type="password" :label="t('auth.password')"
                    :error="form.errors.password" autocomplete="current-password" placeholder="••••••••"
                    @change="form.validate('password')" />
                <a href="/forgot-password"
                    class="absolute top-0 right-1 text-[10px] uppercase tracking-wider font-bold text-primary hover:brightness-110 transition-all">
                    {{ t('auth.forgot_password') }}
                </a>
            </div>

            <label class="flex items-center gap-3 px-1 cursor-pointer group w-fit">
                <input type="checkbox" v-model="form.remember"
                    class="accent-primary h-4 w-4 rounded border-black/10 dark:border-white/10">
                <span
                    class="text-xs font-medium text-main-text transition-colors">
                    {{ t('auth.remember_me') }}
                </span>
            </label>

            <button type="submit" :disabled="form.processing"
                class="w-full py-4 rounded-2xl font-black uppercase tracking-widest text-sm bg-primary text-invert-text shadow-lg shadow-primary/20 hover:shadow-primary/40 active:scale-[0.98] transition-all disabled:opacity-50">
                <span v-if="form.processing">{{ t('auth.signing_in') }}...</span>
                <span v-else>{{ t('auth.sign_in') }}</span>
            </button>
        </form>

        <div class="relative py-2">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-black/5 dark:border-white/10"></div>
            </div>
            <div class="relative flex justify-center text-[10px] uppercase tracking-[0.2em] font-bold">
                <span class="bg-neutral-dark px-4 text-main-text/50">
                    {{ t('auth.continue_with') }}
                </span>
            </div>
        </div>
            <button @click="loginWithGoogle" type="button"
                class="flex items-center justify-center py-3 rounded-xl bg-background-light/5 border border-background-light/20 text-sm font-bold text-main-text hover:bg-black/6 dark:hover:bg-white/10 transition-all w-full">
                Google
            </button>
        <div class="grid gap-4">

        </div>
    </div>
</template>
