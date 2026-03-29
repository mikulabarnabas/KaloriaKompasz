<script setup>
import { useForm } from "laravel-precognition-vue"
import Input from "@/Components/input.vue"
import { computed } from "vue";
import { wTrans, trans as t } from 'laravel-vue-i18n';

const translatedTerms = computed(() => {
    return wTrans('auth.accept_terms', {
        aszf: `<a href="/documents/ASZF.pdf" target="_blank" class="underline text-primary">${t('auth.terms_link')}</a>`,
        privacy: `<a href="/documents/adatkez.pdf" target="_blank" class="underline text-primary">${t('auth.privacy_link')}</a>`
    });
});

const emit = defineEmits(['success']);

const form = useForm('post', '/register', {
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    acceptTerms: false
})

const onSubmit = () =>
    form.submit().then(() => {
        emit('success', true);
    }).catch(() => {
        emit('success', false);
    })

</script>

<template>
    <div class="animate-fly-in">
        <form class="space-y-5" @submit.prevent="onSubmit">

            <Input v-model="form.name" :label="$t('auth.name')" :error="form.errors.name" placeholder="Zsákos Frodó"
                @change="form.validate('name')" autocomplete="username" />

            <Input v-model="form.email" type="email" :label="$t('auth.email')" :error="form.errors.email"
                placeholder="name@example.com" @change="form.validate('email')" autocomplete="username" />

            <Input v-model="form.password" type="password" :label="$t('auth.password')" placeholder="••••••••"
                :error="form.errors.password" @change="form.validate('password')" autocomplete="new-password" />

            <Input v-model="form.password_confirmation" type="password" :label="$t('auth.password_confirmation')"
                placeholder="••••••••" :error="form.errors.password_confirmation"
                @change="form.validate('password_confirmation')" autocomplete="" />

            <label class="flex gap-2 text-sm items-center"
                :class="form.errors.acceptTerms ? 'text-red-500' : 'text-main-text/70'">

                <input type="checkbox" v-model="form.acceptTerms" @change="form.validate('acceptTerms')"
                    class="accent-primary" />

                <span v-html="translatedTerms.value"></span>
            </label>
            <transition enter-active-class="transition duration-200 ease-out"
                enter-from-class="transform -translate-y-1 opacity-0"
                enter-to-class="transform translate-y-0 opacity-100"
                leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100"
                leave-to-class="opacity-0">
                <p v-if="form.errors.acceptTerms"
                    class="text-[10px] font-bold text-red-400 mt-1.5 ml-1 uppercase tracking-wider">
                    {{ form.errors.acceptTerms }}
                </p>
            </transition>

            <button
                class="w-full py-3 rounded-xl font-bold bg-primary text-black hover:shadow-[0_0_20px_rgba(13,242,89,0.4)] transition disabled:opacity-60"
                :disabled="form.processing">
                {{ $t('auth.create_account') }}
            </button>

        </form>
    </div>
</template>