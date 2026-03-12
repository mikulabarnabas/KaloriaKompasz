<script setup>
import { useForm, Head } from '@inertiajs/vue3';

const props = defineProps({
    token: String,
    email: String, // A Laravel néha átadja az e-mailt a query stringben biztonság kedvéért
});

const form = useForm({
    token: props.token,
    email: props.email || '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/reset-password', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>

    <div class="min-h-screen flex flex-col justify-center items-center bg-gray-100">
        <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-4">Új jelszó beállítása</h2>

            <form @submit.prevent="submit">
                <div>
                    <label class="block text-sm font-medium text-gray-700">E-mail cím</label>
                    <input 
                        type="email" 
                        v-model="form.email" 
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" 
                        required
                    >
                    <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Új jelszó</label>
                    <input 
                        type="password" 
                        v-model="form.password" 
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" 
                        required
                    >
                    <div v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Jelszó megerősítése</label>
                    <input 
                        type="password" 
                        v-model="form.password_confirmation" 
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" 
                        required
                    >
                </div>

                <div class="mt-4">
                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700"
                    >
                        Jelszó mentése
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>