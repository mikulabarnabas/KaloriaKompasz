<script setup>
import { useForm, Head } from '@inertiajs/vue3';

defineProps({ status: String });

const form = useForm({
    email: '',
});

const submit = () => {
    form.post('/forgot-password');
};
</script>

<template>

    <div class="min-h-screen flex flex-col justify-center items-center bg-gray-100">
        <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-4">Jelszó visszaállítása</h2>
            
            <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                {{ status }}
            </div>

            <form @submit.prevent="submit">
                <div>
                    <label class="block text-sm font-medium text-gray-700">E-mail cím</label>
                    <input 
                        type="email" 
                        v-model="form.email" 
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" 
                        required 
                        autofocus
                    >
                    <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
                </div>

                <div class="mt-4">
                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 disabled:opacity-50"
                    >
                        Visszaállító link küldése
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>