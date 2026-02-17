<script setup>
import { computed, ref, watch } from "vue";
import { useForm } from "laravel-precognition-vue";
import GlowingButton from "@/Components/glowingButton.vue"
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    show: Boolean,
    food: Object,
    date: String,
    mealTypes: Array
});

const emit = defineEmits(['close', 'saved']);

const currentImageIndex = ref(0);
const unitOptions = ['g', 'dkg', 'kg', 'ml', 'cl', 'dl', 'l'];

const form = useForm("post", "/fdiary/entry", {
    date: props.date,
    food_id: null,
    meal_type: "",
    unit: "g",
    amount: 100,
});

const images = computed(() => {
    if (!props.food?.image_paths) return [];
    const parts = props.food.image_paths.split(':').filter(Boolean);
    return parts.slice(1).map(file => `/storage/foods/${parts[0].trim()}/${file.trim()}`);
});

const currentImage = computed(() => {
    if (images.value.length) return images.value[currentImageIndex.value];
    return props.food?.image || null;
});

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
    currentImageIndex.value = 0;
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
        <div v-if="show"
            class="fixed inset-0 z-100 overflow-y-auto flex items-start justify-center px-4 py-6 sm:items-center sm:px-0">

            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="closeModal"></div>

            <div
                class="relative w-full max-w-4xl bg-background-dark rounded-3xl shadow-2xl flex flex-col md:flex-row border border-primary/20 transform transition-all overflow-hidden my-auto">
                <div
                    class="w-full md:w-5/12 bg-black/20 p-6 md:p-8 flex flex-col relative border-b md:border-b-0 md:border-r border-white/5">

                    <div class="mb-4 md:mb-6">
                        <h2 class="text-2xl md:text-3xl font-bold text-white mb-1">{{ food?.name }}</h2>
                    </div>

                    <div class="flex-1 flex flex-col items-center justify-center relative min-h-45 md:min-h-50 group">
                        <button @click.prevent="prevImage"
                            class="absolute left-0 p-2 text-primary/50 hover:text-primary transition-colors z-10">
                            <span class="material-symbols-outlined text-3xl">chevron_left</span>
                        </button>

                        <div class="w-40 h-40 md:w-56 md:h-56 relative z-0">
                            <img v-if="currentImage" :src="currentImage"
                                class="w-full h-full object-contain drop-shadow-2xl" />
                            <div v-else class="w-full h-full flex items-center justify-center bg-white/5 rounded-full">
                                <span class="material-symbols-outlined text-6xl text-white/20">restaurant</span>
                            </div>
                        </div>

                        <button @click.prevent="nextImage"
                            class="absolute right-0 p-2 text-primary/50 hover:text-primary transition-colors z-10">
                            <span class="material-symbols-outlined text-3xl">chevron_right</span>
                        </button>

                        <div class="flex gap-2 mt-4 md:mt-6">
                            <button v-for="(_, idx) in images" :key="idx" @click="currentImageIndex = idx"
                                class="w-8 h-1 rounded-full transition-all"
                                :class="idx === currentImageIndex ? 'bg-primary' : 'bg-white/20 hover:bg-white/40'"></button>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-wrap gap-1 justify-center text-center">
                        <div
                            class="px-3 py-1 rounded-full bg-orange-500/10 border border-orange-500/20 text-orange-500 md:text-sm font-medium min-w-20">
                            {{ food?.calorie }} kcal
                        </div>
                        <div
                            class="px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-500 md:text-sm font-medium min-w-20">
                            {{ food?.carb }}g Carbs
                        </div>
                        <div
                            class="px-3 py-1 rounded-full bg-green-500/10 border border-green-500/20 text-green-500 md:text-sm font-medium min-w-20">
                            {{ food?.fat }}g Fat
                        </div>
                        <div
                            class="px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 md:text-sm font-medium min-w-20">
                            {{ food?.protein }}g Protein
                        </div>
                    </div>
                </div>

                <div class="w-full md:w-7/12 p-6 md:p-8 flex flex-col bg-background-dark">

                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h3 class="text-xl font-bold text-white">Add to Diary
                            </h3>
                            <p class="text-slate-400 text-sm mt-1">Select meal type and portion size.</p>
                        </div>
                        <button @click="closeModal" class="text-slate-400 hover:text-white transition-colors">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <form @submit.prevent="submitForm" class="mt-6 md:mt-8 space-y-5 md:space-y-6 flex-1 flex flex-col">

                        <div class="space-y-2">
                            <label class="text-primary text-xs font-medium ml-1">Meal Type</label>
                            <div class="relative">
                                <select v-model="form.meal_type"
                                    class="w-full appearance-none bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all cursor-pointer hover:bg-white/10">
                                    <option value="" disabled selected class="text-gray-500">Select meal...</option>
                                    <option v-for="meal in mealTypes" :key="meal.value" :value="meal.value"
                                        class="bg-gray-800 text-white">
                                        {{ meal.label }}
                                    </option>
                                </select>
                                <span
                                    class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-primary pointer-events-none text-sm">expand_more</span>
                            </div>
                            <small v-if="form.invalid('meal_type')" class="text-red-400 text-xs ml-1">
                                {{ form.errors.meal_type }}
                            </small>
                        </div>

                        <div class="grid grid-cols-5 gap-4">
                            <div class="col-span-3 space-y-2">
                                <label class="text-primary text-xs font-medium ml-1">Amount</label>
                                <input v-model="form.amount" type="number" step="0.1" placeholder="0"
                                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all placeholder-slate-600" />
                            </div>
                            <div class="col-span-2 space-y-2">
                                <label class="text-primary text-xs font-medium ml-1">Unit</label>
                                <div class="relative">
                                    <select v-model="form.unit"
                                        class="w-full appearance-none bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all cursor-pointer hover:bg-white/10">
                                        <option v-for="unit in allowedUnits" :key="unit" :value="unit"
                                            class="bg-gray-800">{{ unit }}</option>
                                    </select>
                                    <span
                                        class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-primary pointer-events-none text-sm">expand_more</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-auto pt-6 space-y-6">
                            <div class="bg-black/20 rounded-xl p-4 border border-white/5">
                                <div class="flex justify-center items-center text-sm mb-1">
                                    <h2 class="text-lg font-bold text-white">Total macros</h2>
                                </div>
                                <div class="flex justify-between text-slate-500">
                                    <div
                                        class="px-3 py-1 rounded-full bg-orange-500/10 border border-orange-500/20 text-orange-500 md:text-sm font-medium">
                                        {{ calculatedMacros.kcal }}kcal
                                    </div>
                                    <div
                                        class="px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-500 md:text-sm font-medium">
                                        {{ calculatedMacros.carb }}g Carbs
                                    </div>
                                    <div
                                        class="px-3 py-1 rounded-full bg-green-500/10 border border-green-500/20 text-green-500 md:text-sm font-medium">
                                        {{ calculatedMacros.fat }}g Fat
                                    </div>
                                    <div
                                        class="px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 md:text-sm font-medium">
                                        {{ calculatedMacros.protein }}g Protein
                                    </div>
                                </div>
                            </div>

                            <div class="flex gap-3">
                                <GlowingButton type="submit" :disabled="form.processing"
                                    class="flex-1 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                                    <span>{{ t('foodDiary.add_button') }}</span>
                                    <span v-if="form.processing"
                                        class="material-symbols-outlined animate-spin text-xl">progress_activity</span>
                                    <span v-else class="material-symbols-outlined text-xl">add</span>
                                </GlowingButton>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Transition>
</template>