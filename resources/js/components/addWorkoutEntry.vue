<script setup>
import { computed, watch } from "vue";
import { useForm } from "laravel-precognition-vue";
import Button from "@/Components/button.vue";
import Input from "@/Components/input.vue";
import { trans as t } from 'laravel-vue-i18n';

const props = defineProps({
    show: Boolean,
    exercise: Object,
    date: String
});

const emit = defineEmits(['close', 'saved']);

const UNIT_TO_BASE = { minutes: 1, hours: 60, m: 1, km: 1000 };

const unitOptions = computed(() => [
    { label: t('workoutDiary.minutes'), value: "minutes" },
    { label: t('workoutDiary.hours'), value: "hours" },
    { label: t('workoutDiary.km'), value: "km" },
    { label: t('workoutDiary.m'), value: "m" }
]);

const form = useForm("post", "/wdiary/entry", {
    date: props.date,
    exercise_id: null,
    unit: "",
    amount: 0,
});

const allowedUnits = computed(() => {
    if (!props.exercise) return [];
    const timeUnits = ['minutes', 'hours'];
    const distanceUnits = ['km', 'm'];

    if (timeUnits.includes(props.exercise.unit)) {
        return unitOptions.value.filter(u => timeUnits.includes(u.value));
    }
    if (distanceUnits.includes(props.exercise.unit)) {
        return unitOptions.value.filter(u => distanceUnits.includes(u.value));
    }
    return unitOptions;
});

const calculatedBurned = computed(() => {
    if (!props.exercise || !form.amount || !form.unit) return 0;
    const amount = Number(form.amount);
    const exerciseUnitFactor = UNIT_TO_BASE[props.exercise.unit] || 1;
    const entryUnitFactor = UNIT_TO_BASE[form.unit] || 1;
    const factor = entryUnitFactor / exerciseUnitFactor;
    const perUnit = Number(props.exercise.calories_per_unit);
    return Math.round(perUnit * factor * amount * 100) / 100;
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

watch(() => props.exercise, (newEx) => {
    if (newEx) {
        form.exercise_id = newEx.id;
        form.unit = newEx.unit || 'minutes';
        form.amount = 0;
    }
});

watch(() => props.date, (newDate) => form.date = newDate);
</script>

<template>
    <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0"
        enter-to-class="opacity-100" leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100"
        leave-to-class="opacity-0">
        <div v-if="show"
            class="fixed inset-0 z-100 overflow-y-auto flex items-start justify-center px-4 py-6 sm:items-center sm:px-0">
            <div class="fixed inset-0 bg-background-dark/80 backdrop-blur-sm transition-opacity" @click="closeModal">
            </div>

            <div
                class="relative w-full max-w-4xl bg-background-dark rounded-[2.5rem] shadow-2xl flex flex-col md:flex-row border border-neutral-border transform transition-all overflow-hidden my-auto">

                <div
                    class="w-full md:w-5/12 bg-neutral-dark/40 p-6 md:p-8 flex flex-col relative border-b md:border-b-0 md:border-r border-neutral-border">
                    <div class="mb-4 md:mb-6">
                        <h2 class="text-2xl md:text-3xl font-black text-main-text mb-2 tracking-tight">
                            {{ exercise?.name }}
                        </h2>
                        <div
                            class="inline-flex items-center px-3 py-1 rounded-full bg-primary/10 border border-primary/20 text-primary text-[10px] font-black uppercase tracking-widest">
                            <span class="material-symbols-outlined text-sm mr-1.5">local_fire_department</span>
                            {{ exercise?.calories_per_unit }} kcal / {{ exercise?.unit }}
                        </div>
                    </div>

                    <div class="flex-1 flex flex-col items-center justify-center relative min-h-48 md:min-h-60 group">
                        <div
                            class="w-32 h-32 md:w-48 md:h-48 flex items-center justify-center bg-background-dark/60 rounded-full shadow-2xl border border-neutral-border relative z-10 transition-transform duration-700 group-hover:rotate-12">
                            <span
                                class="material-symbols-outlined text-6xl md:text-8xl text-primary/30 group-hover:text-primary transition-colors duration-500">fitness_center</span>
                            <div
                                class="absolute inset-0 bg-primary/5 blur-3xl rounded-full group-hover:bg-primary/10 transition-colors">
                            </div>
                        </div>

                        <p v-if="exercise?.note"
                            class="mt-8 text-secondary-text text-sm italic text-center px-4 leading-relaxed opacity-70">
                            "{{ exercise?.note }}"
                        </p>
                    </div>
                </div>

                <div class="w-full md:w-7/12 p-6 md:p-8 flex flex-col bg-background-dark">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-xl font-black text-main-text uppercase tracking-tight">
                                {{ t('workoutDiary.add_button') }}
                            </h3>
                            <p class="text-secondary-text text-sm mt-1">Adjust your units and duration below.</p>
                        </div>
                        <button @click="closeModal"
                            class="w-10 h-10 flex items-center justify-center rounded-full bg-neutral-dark hover:bg-neutral-light/10 text-secondary-text hover:text-main-text transition-all">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <form @submit.prevent="submitForm" class="space-y-6 flex-1 flex flex-col">
                        <div class="grid grid-cols-5 gap-4">
                            <div class="col-span-3">
                                <Input v-model="form.amount" :label="t('workoutDiary.amount_label')"
                                    type="number" step="0.1" placeholder="0.0" :error="form.errors.amount" />
                            </div>

                            <div class="col-span-2">
                                <label
                                    class="text-primary text-[10px] font-black uppercase tracking-[0.2em] mb-2 block ml-1">
                                    {{ t('workoutDiary.unit_label') || 'Unit' }}
                                </label>
                                <div class="relative group">
                                    <select v-model="form.unit"
                                        class="w-full appearance-none bg-neutral-dark/40 border border-neutral-border rounded-xl px-4 py-3 text-main-text focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all cursor-pointer hover:bg-neutral-dark/60">
                                        <option v-for="unit in allowedUnits" :key="unit.value" :value="unit.value"
                                            class="bg-neutral-dark text-main-text">
                                            {{ unit.label }}
                                        </option>
                                    </select>
                                    <span
                                        class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-primary pointer-events-none text-sm group-hover:translate-y-[-40%] transition-transform">expand_more</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-auto pt-6 space-y-6">
                            <div
                                class="bg-primary/5 rounded-4xl p-8 border border-primary/10 text-center relative overflow-hidden group/card">
                                <h2 class="text-[10px] font-black text-secondary-text uppercase tracking-[0.2em] mb-3">
                                    {{ $t('workoutDiary.burned_label') }}
                                </h2>

                                <div class="relative z-10 flex items-baseline justify-center">
                                    <span
                                        class="text-6xl font-black text-primary tracking-tighter transition-transform inline-block group-hover/card:scale-110 duration-500">
                                        {{ calculatedBurned }}
                                    </span>
                                    <span
                                        class="text-lg font-bold text-primary/40 ml-2 uppercase tracking-widest">kcal</span>
                                </div>

                                <div
                                    class="absolute -right-8 -bottom-8 size-32 bg-primary/10 blur-3xl rounded-full transition-opacity group-hover/card:opacity-100 opacity-50">
                                </div>
                                <div class="absolute -left-8 -top-8 size-32 bg-primary/5 blur-3xl rounded-full"></div>
                            </div>

                            <div class="flex gap-3">
                                <Button type="submit" :label="t('workoutDiary.add_button')" icon="cloud_upload"
                                    :loading="form.processing" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Transition>
</template>