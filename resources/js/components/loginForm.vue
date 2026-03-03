<script setup>
import { ref } from "vue"
import { useForm } from "laravel-precognition-vue"
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const showSuccessDialog = ref(false)

const form = useForm('post', '/login', {
    email: "",
    password: "",
    remember_token: false,
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
    <div class="space-y-5 animate-fly-in">
        <h2 class="text-2xl font-bold text-white">
            {{ $t('auth.welcome_back') }}
        </h2>
        <p class="text-white/50 text-sm mt-1 mb-6">
            {{ $t('auth.login_subtitle') }}
        </p>

        <form class="space-y-5" @submit.prevent="onSubmit" novalidate>
            <div>
                <label class="block text-xs text-white/60 mb-2">
                    {{ $t('auth.email') }}
                </label>
                <input v-model="form.email" type="email" autocomplete="email" @change="form.validate('email')"
                    class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-white placeholder-white/30 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/40"
                    placeholder="name@example.com">
                <p v-if="form.invalid('email')" class="text-xs text-red-400 mt-1">
                    {{ form.errors.email }}
                </p>
            </div>

            <div>
                <div class="flex justify-between mb-2">
                    <label class="text-xs text-white/60">
                        {{ $t('auth.password') }}
                    </label>
                    <a href="#" class="text-xs text-primary hover:underline">
                        {{ $t('auth.forgot_password') }}
                    </a>
                </div>

                <input v-model="form.password" type="password" autocomplete="current-password"
                    @change="form.validate('password')"
                    class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-white placeholder-white/30 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/40"
                    placeholder="••••••••">

                <p v-if="form.invalid('password')" class="text-xs text-red-400 mt-1">
                    {{ form.errors.password }}
                </p>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" v-model="form.remember_token" class="accent-primary">
                <label class="text-sm text-white/70">
                    {{ $t('auth.remember_me') }}
                </label>
            </div>

            <button type="submit" :disabled="form.processing"
                class="w-full py-3 rounded-xl font-bold bg-primary text-black hover:shadow-[0_0_20px_rgba(13,242,89,0.4)] transition disabled:opacity-60">
                {{ $t('auth.sign_in') }}
            </button>
        </form>

        <div class="flex items-center gap-4 my-8 text-xs text-white/40">
            <div class="h-px flex-1 bg-white/10"></div>
            {{ t('auth.continue_with') }}
            <div class="h-px flex-1 bg-white/10"></div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <button class="py-3 rounded-xl bg-white/5 border border-white/10 text-white/80 hover:bg-white/10">
                Google
            </button>
            <button class="py-3 rounded-xl bg-white/5 border border-white/10 text-white/80 hover:bg-white/10">
                Apple
            </button>
        </div>

        <div v-if="showSuccessDialog" class="fixed inset-0 bg-black/70 flex items-center justify-center">
            <div class="bg-neutral-dark p-6 rounded-xl max-w-sm w-full text-center">
                <h3 class="text-white font-bold mb-4">
                    {{ $t('auth.login_dialog_title') }}
                </h3>
                <button @click="closeSuccessDialog" class="bg-primary text-black px-4 py-2 rounded-lg font-semibold">
                    {{ $t('auth.close') }}
                </button>
            </div>
        </div>
    </div>
</template>