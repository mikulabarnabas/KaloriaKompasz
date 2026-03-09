<script setup>
import { computed, ref, watch } from "vue";
import { useForm } from "laravel-precognition-vue";
//import GlowingButton from "@/Components/glowingButton.vue"
import { trans as t } from 'laravel-vue-i18n';

const props = defineProps({
    show: Boolean,
    food: Object,
    date: String,
    mealTypes: Array
});

const emit = defineEmits(['close', 'saved']);

const unitOptions = ['g', 'dkg', 'kg', 'ml', 'cl', 'dl', 'l'];

const form = useForm("post", "/fdiary/entry", {
    date: props.date,
    food_id: null,
    meal_type: "",
    unit: "g",
    amount: 100,
});

const currentImage = computed(() => props.food?.image || null);

const allowedUnits = computed(() => {
    if (!props.food) return [];
    const weightUnits = ['g', 'dkg', 'kg'];
    const cubicUnits = ['ml', 'l', 'cl', 'dl'];
    if (weightUnits.includes(props.food.unit)) return weightUnits;
    if (cubicUnits.includes(props.food.unit)) return cubicUnits;
    return unitOptions;
});

const calculatedMacros = computed(() => {
    if (!props.food) return { kcal: 0, carbs: 0, fat: 0, protein: 0 };
    let multiplier = form.amount / 100;

    if (['kg', 'l'].includes(form.unit)) multiplier *= 10;
    if (['dkg', 'dl'].includes(form.unit)) multiplier *= 0.1;

    return {
        kcal: Math.round(props.food.calorie * multiplier),
        carb: (props.food.carb * multiplier).toFixed(1),
        fat: (props.food.fat * multiplier).toFixed(1),
        protein: (props.food.protein * multiplier).toFixed(1)
    };
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

watch(() => props.food, (newFood) => {
    if (newFood) {
        form.food_id = newFood.id;
        form.unit = newFood.unit || 'g';
        form.amount = 100;
    }
});

watch(() => props.date, (newDate) => form.date = newDate);
</script>

<template>
    <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0"
        enter-to-class="opacity-100" leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100"
        leave-to-class="opacity-0">
        <div v-if="show" class="fixed inset-0 z-[100] overflow-y-auto flex items-start justify-center px-4 py-6 sm:items-center sm:px-0">
            <div class="fixed inset-0 bg-background-dark/80 backdrop-blur-sm transition-opacity" @click="closeModal"></div>

            <div class="relative w-full max-w-4xl bg-background-dark rounded-[2rem] shadow-2xl flex flex-col md:flex-row border border-neutral-border transform transition-all overflow-hidden my-auto">
                
                <div class="w-full md:w-5/12 bg-neutral-dark/40 p-6 md:p-8 flex flex-col relative border-b md:border-b-0 md:border-r border-neutral-border">
                    <div class="mb-6">
                        <h2 class="text-2xl md:text-3xl font-black text-main-text mb-1 tracking-tight">{{ food?.name }}</h2>
                        <p class="text-[10px] font-black text-primary uppercase tracking-[0.2em]">Product Details</p>
                    </div>

                    <div class="flex-1 flex flex-col items-center justify-center relative min-h-45 md:min-h-50 group">
                        <div class="w-44 h-44 md:w-60 md:h-60 relative z-0">
                            <img v-if="currentImage" :src="currentImage"
                                class="w-full h-full object-contain drop-shadow-2xl transition-transform duration-500 group-hover:scale-105" />
                            <div v-else class="w-full h-full flex items-center justify-center bg-background-dark/60 rounded-full border border-neutral-border">
                                <span class="material-symbols-outlined text-6xl text-primary/20">restaurant</span>
                            </div>
                            <div class="absolute inset-0 bg-primary/5 blur-3xl rounded-full -z-10"></div>
                        </div>
                    </div>

                    <div class="mt-8 grid grid-cols-2 gap-2 text-[10px] font-bold uppercase tracking-wider opacity-60">
                        <div class="flex justify-between border-b border-white/5 pb-1"><span>Protein</span><span>{{ food?.protein }}g</span></div>
                        <div class="flex justify-between border-b border-white/5 pb-1"><span>Carbs</span><span>{{ food?.carb }}g</span></div>
                        <div class="flex justify-between border-b border-white/5 pb-1"><span>Fat</span><span>{{ food?.fat }}g</span></div>
                        <div class="flex justify-between border-b border-white/5 pb-1"><span>Kcal</span><span>{{ food?.calorie }}</span></div>
                    </div>
                </div>

                <div class="w-full md:w-7/12 p-6 md:p-8 flex flex-col bg-background-dark">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h3 class="text-xl font-black text-main-text uppercase tracking-tight">Add to Diary</h3>
                            <p class="text-secondary-text text-sm mt-1">Select meal type and portion size.</p>
                        </div>
                        <button @click="closeModal" class="text-secondary-text hover:text-main-text transition-colors">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <form @submit.prevent="submitForm" class="mt-6 md:mt-8 space-y-6 flex-1 flex flex-col">
                        <div class="space-y-2">
                            <label class="text-primary text-[10px] font-black uppercase tracking-[0.2em] ml-1">Meal Type</label>
                            <div class="relative">
                                <select v-model="form.meal_type" class="w-full appearance-none bg-neutral-dark/40 border border-neutral-border rounded-xl px-4 py-3 text-main-text focus:ring-2 focus:ring-primary outline-none transition-all cursor-pointer">
                                    <option value="" disabled selected>Select meal...</option>
                                    <option v-for="meal in mealTypes" :key="meal.value" :value="meal.value" class="bg-background-dark">{{ meal.label }}</option>
                                </select>
                                <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-primary pointer-events-none text-sm">expand_more</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-5 gap-4">
                            <div class="col-span-3 space-y-2">
                                <label class="text-primary text-[10px] font-black uppercase tracking-[0.2em] ml-1">Amount</label>
                                <input v-model="form.amount" type="number" step="0.1" class="w-full bg-neutral-dark/40 border border-neutral-border rounded-xl px-4 py-3 text-main-text focus:ring-2 focus:ring-primary outline-none" />
                            </div>
                            <div class="col-span-2 space-y-2">
                                <label class="text-primary text-[10px] font-black uppercase tracking-[0.2em] ml-1">Unit</label>
                                <div class="relative">
                                    <select v-model="form.unit" class="w-full appearance-none bg-neutral-dark/40 border border-neutral-border rounded-xl px-4 py-3 text-main-text focus:ring-2 focus:ring-primary outline-none transition-all cursor-pointer">
                                        <option v-for="unit in allowedUnits" :key="unit" :value="unit" class="bg-background-dark">{{ unit }}</option>
                                    </select>
                                    <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-primary pointer-events-none text-sm">expand_more</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-auto pt-6 space-y-6">
                            <div class="bg-primary/5 rounded-2xl p-6 border border-primary/10 relative overflow-hidden">
                                <h2 class="text-[10px] font-black text-secondary-text uppercase tracking-[0.2em] mb-4 text-center">Calculated Totals</h2>
                                <div class="grid grid-cols-4 gap-2">
                                    <div class="text-center">
                                        <div class="text-xl font-black text-primary">{{ calculatedMacros.kcal }}</div>
                                        <div class="text-[8px] font-black text-secondary-text uppercase">Kcal</div>
                                    </div>
                                    <div class="text-center border-l border-white/5">
                                        <div class="text-xl font-black text-main-text">{{ calculatedMacros.carb }}g</div>
                                        <div class="text-[8px] font-black text-secondary-text uppercase">Carbs</div>
                                    </div>
                                    <div class="text-center border-l border-white/5">
                                        <div class="text-xl font-black text-main-text">{{ calculatedMacros.fat }}g</div>
                                        <div class="text-[8px] font-black text-secondary-text uppercase">Fat</div>
                                    </div>
                                    <div class="text-center border-l border-white/5">
                                        <div class="text-xl font-black text-main-text">{{ calculatedMacros.protein }}g</div>
                                        <div class="text-[8px] font-black text-secondary-text uppercase">Protein</div>
                                    </div>
                                </div>
                            </div>

                            <ActionButton 
                                type="submit" 
                                :label="t('foodDiary.add_button')"
                                icon="add_task"
                                :loading="form.processing"
                                :disabled="!form.meal_type"
                            />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Transition>
</template>