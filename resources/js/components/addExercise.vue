<script setup>
import { useForm } from "laravel-precognition-vue";
import GlowingButton from "@/Components/glowingButton.vue";
import Input from "@/Components/input.vue"; // Imported your Input component
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    show: Boolean
});

const emit = defineEmits(['close', 'saved']);

const unitOptions = [
  { label: t('workoutDiary.minute'), value: "minutes" },
  { label: t('workoutDiary.hour'), value: "hours" },
  { label: t('workoutDiary.km'), value: "km" },
  { label: t('workoutDiary.m'), value: "m" }
];

const form = useForm("post", "/wdiary/create", {
    name: "",
    unit: "minutes",
    calories_per_unit: 0,
    note: ""
});

const submitForm = () => {
    form.submit({
        preserveScroll: true,
        onSuccess: () => {
            emit('saved');
            closeModal();
        }
    });
};

const closeModal = () => {
    emit('close');
    form.reset();
};
</script>

<template>
    <Transition 
        enter-active-class="transition duration-200 ease-out" 
        enter-from-class="opacity-0"
        enter-to-class="opacity-100" 
        leave-active-class="transition duration-150 ease-in" 
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="show" class="fixed inset-0 z-[100] overflow-y-auto flex items-center justify-center px-4 py-6">
            <div class="fixed inset-0 bg-background-dark/80 backdrop-blur-sm transition-opacity" @click="closeModal"></div>

            <div class="relative w-full max-w-lg bg-background-dark rounded-[2.5rem] shadow-2xl border border-neutral-border p-6 md:p-10 transform transition-all">
                
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <h3 class="text-2xl font-black text-main-text uppercase tracking-tight">
                            {{ t('workoutDiary.create_exercise_title') }}
                        </h3>
                        <p class="text-secondary-text text-sm mt-1">Define a new custom activity.</p>
                    </div>
                    <button @click="closeModal" class="w-10 h-10 flex items-center justify-center rounded-full bg-neutral-dark hover:bg-neutral-light/10 text-secondary-text hover:text-main-text transition-all">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="space-y-6">
                    
                    <Input 
                        v-model="form.name" 
                        type="text"
                        :label="t('workoutDiary.exercise_name_label')"
                        placeholder="e.g. Mountain Biking"
                        :error="form.errors.name"
                    />

                    <div class="grid grid-cols-2 gap-4">
                        <Input 
                            v-model.number="form.calories_per_unit" 
                            type="number" 
                            step="0.1"
                            :label="t('workoutDiary.calorie_label')"
                            placeholder="0.0"
                            :error="form.errors.calories_per_unit"
                        />

                        <div class="space-y-2">
                            <label class="text-primary text-[10px] font-black uppercase tracking-[0.2em] mb-2 block ml-1">
                                {{ t('workoutDiary.unit_label') }}
                            </label>
                            <div class="relative group">
                                <select v-model="form.unit"
                                    class="w-full appearance-none bg-neutral-dark/40 border border-neutral-border rounded-xl px-4 py-3 text-main-text focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all cursor-pointer hover:bg-neutral-dark/60">
                                    <option v-for="unit in unitOptions" :key="unit.value" :value="unit.value" class="bg-neutral-dark text-main-text">
                                        {{ unit.label }}
                                    </option>
                                </select>
                                <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-primary pointer-events-none text-sm group-hover:translate-y-[-40%] transition-transform">
                                    expand_more
                                </span>
                            </div>
                        </div>
                    </div>

                    <Input 
                        v-model="form.note" 
                        type="text"
                        :label="t('foodDiary.note_label')"
                        placeholder="Short description..."
                        :error="form.errors.note"
                    />

                    <div class="pt-4">
                        <GlowingButton 
                            type="submit" 
                            :disabled="form.processing"
                            class="w-full h-14 transition-all active:scale-[0.98] flex items-center justify-center gap-2 rounded-xl"
                        >
                            <span class="font-black uppercase tracking-widest text-sm">
                                {{ t('workoutDiary.save_exercise') }}
                            </span>
                            <span v-if="form.processing" class="material-symbols-outlined animate-spin text-xl">
                                progress_activity
                            </span>
                            <span v-else class="material-symbols-outlined text-xl">save</span>
                        </GlowingButton>
                    </div>
                </form>
            </div>
        </div>
    </Transition>
</template>