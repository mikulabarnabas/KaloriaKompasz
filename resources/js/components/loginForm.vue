<script setup>
import { ref } from "vue"
import { useForm } from "laravel-precognition-vue"
import { useI18n } from 'vue-i18n'
import InputField from "@/Components/input.vue"

const { t } = useI18n()
const showSuccessDialog = ref(false)

const form = useForm('post', '/login', {
    email: "",
    password: "",
    remember: false,
})

const onSubmit = () =>
    form.submit().then(() => {
        form.reset()
        showSuccessDialog.value = true
    })

function closeSuccessDialog() {
    showSuccessDialog.value = false
    window.location.href = "/"
}
</script>

<template>
    <div class="space-y-6 animate-fly-in">
        <header>
            <h2 class="text-2xl font-bold text-black dark:text-white tracking-tight">
                {{ t('auth.welcome_back') }}
            </h2>
            <p class="text-black/50 dark:text-white/50 text-sm mt-1">
                {{ t('auth.login_subtitle') }}
            </p>
        </header>

        <form class="space-y-5" @submit.prevent="onSubmit" novalidate>
            <InputField v-model="form.email" type="email" :label="t('auth.email')" :error="form.errors.email"
                placeholder="name@example.com" @change="form.validate('email')" />

            <div class="relative">
                <InputField v-model="form.password" type="password" :label="t('auth.password')"
                    :error="form.errors.password" placeholder="••••••••" @change="form.validate('password')" />
                <a href="#"
                    class="absolute top-0 right-1 text-[10px] uppercase tracking-wider font-bold text-primary hover:brightness-110 transition-all">
                    {{ t('auth.forgot_password') }}
                </a>
            </div>

            <label class="flex items-center gap-3 px-1 cursor-pointer group w-fit">
                <input type="checkbox" v-model="form.remember"
                    class="accent-primary h-4 w-4 rounded border-black/10 dark:border-white/10">
                <span
                    class="text-xs font-medium text-black/60 dark:text-white/60 group-hover:text-black dark:group-hover:text-white transition-colors">
                    {{ t('auth.remember_me') }}
                </span>
            </label>

            <button type="submit" :disabled="form.processing"
                class="w-full py-4 rounded-2xl font-black uppercase tracking-widest text-sm bg-primary text-black shadow-lg shadow-primary/20 hover:shadow-primary/40 active:scale-[0.98] transition-all disabled:opacity-50">
                <span v-if="form.processing">{{ t('auth.signing_in') }}...</span>
                <span v-else>{{ t('auth.sign_in') }}</span>
            </button>
        </form>

        <div class="relative py-2">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-black/5 dark:border-white/10"></div>
            </div>
            <div class="relative flex justify-center text-[10px] uppercase tracking-[0.2em] font-bold">
                <span class="bg-white dark:bg-neutral-dark px-4 text-black/30 dark:text-white/30">
                    {{ t('auth.continue_with') }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <a href="/auth/google/redirect"
                class="flex items-center justify-center py-3 rounded-xl bg-black/[0.03] dark:bg-white/5 border border-black/5 dark:border-white/10 text-sm font-bold text-black/70 dark:text-white/80 hover:bg-black/[0.06] dark:hover:bg-white/10 transition-all">
                Google
            </a>
            <button
                class="flex items-center justify-center py-3 rounded-xl bg-black/[0.03] dark:bg-white/5 border border-black/5 dark:border-white/10 text-sm font-bold text-black/70 dark:text-white/80 hover:bg-black/[0.06] dark:hover:bg-white/10 transition-all">
                Apple
            </button>
        </div>

        <div v-if="showSuccessDialog"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div
                class="bg-white dark:bg-neutral-dark border border-black/5 dark:border-white/10 p-8 rounded-[2.5rem] max-w-sm w-full text-center shadow-2xl animate-fly-in">
                <div
                    class="w-16 h-16 bg-primary/20 text-primary rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-xl text-black dark:text-white font-bold mb-6">{{ t('auth.login_dialog_title') }}</h3>
                <button @click="closeSuccessDialog"
                    class="w-full bg-primary text-black py-4 rounded-2xl font-black uppercase tracking-widest text-sm hover:brightness-110 transition-all">
                    {{ t('auth.close') }}
                </button>
            </div>
        </div>
    </div>
</template>