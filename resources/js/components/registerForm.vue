<script setup>
import { ref } from "vue"
import { useForm } from "laravel-precognition-vue"
import InputField from "@/Components/input.vue"
import Input from "./input.vue"

const showSuccessDialog = ref(false)

const form = useForm('post', '/register', {
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    acceptTerms: false
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
    <div class="animate-fly-in">
        <form class="space-y-5" @submit.prevent="onSubmit">

            <InputField v-model="form.name" :label="$t('auth.name')" :error="form.errors.name" placeholder="Zsákos Frodó"
                @change="form.validate('name')" />

            <InputField v-model="form.email" type="email" :label="$t('auth.email')" :error="form.errors.email" placeholder="name@example.com"
                @change="form.validate('email')" />

            <InputField v-model="form.password" type="password" :label="$t('auth.password')" placeholder="••••••••"
                :error="form.errors.password" @change="form.validate('password')" />

            <InputField v-model="form.password_confirmation" type="password" :label="$t('auth.password_confirmation')" placeholder="••••••••"
                :error="form.errors.password_confirmation" @change="form.validate('password_confirmation')" />

            <label class="flex gap-2 text-sm text-white/70 items-center">
                <input type="checkbox" v-model="form.acceptTerms" @change="form.validate('acceptTerms')"
                    class="accent-primary">
                    {{ $t('auth.accept_terms') }}
            </label>

            <button
                class="w-full py-3 rounded-xl font-bold bg-primary text-black hover:shadow-[0_0_20px_rgba(13,242,89,0.4)] transition disabled:opacity-60"
                :disabled="form.processing">
                {{ $t('auth.create_account') }}
            </button>

            <div v-if="showSuccessDialog" class="fixed inset-0 bg-black/70 flex items-center justify-center">
                <div class="bg-neutral-dark p-6 rounded-xl max-w-sm w-full text-center border border-white/10">
                    <h3 class="text-white font-bold mb-4">{{ $t('auth.register_dialog_title') }}</h3>
                    <p class="text-white/60 text-sm mt-2 mb-6">
                        {{ $t('auth.register_dialog_message') }}
                    </p>
                    <button class="bg-primary text-black px-6 py-2 rounded-lg font-semibold w-full"
                        @click="closeSuccessDialog">
                        {{ $t('auth.close') }}
                    </button>
                </div>
            </div>

        </form>
    </div>
</template>