<script setup>
import { computed, ref } from "vue";
import { useForm } from "laravel-precognition-vue";
import GlowingButton from "@/Components/glowingButton.vue";
import Input from "@/Components/input.vue";
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    show: Boolean,
    date: String,
});

const emit = defineEmits(['close', 'saved']);

// --- State ---
const currentImageIndex = ref(0);
const filePreviews = ref([]);
const unitOptions = ['g', 'dkg', 'kg', 'ml', 'cl', 'dl', 'l'];

// --- Form: Create New Food ---
const form = useForm("post", "/fdiary/create", {
    name: "",
    amount: 100,
    unit: "g",
    calorie: "",
    fat: "",
    carb: "",
    protein: "",
    notes: "",
    images: [], 
});

const images = computed(() => filePreviews.value);
const currentImage = computed(() => images.value.length > 0 ? images.value[currentImageIndex.value] : null);

const nextImage = () => {
    if (images.value.length <= 1) return;
    currentImageIndex.value = (currentImageIndex.value + 1) % images.value.length;
};

const prevImage = () => {
    if (images.value.length <= 1) return;
    currentImageIndex.value = (currentImageIndex.value - 1 + images.value.length) % images.value.length;
};

const onFileChange = (e) => {
    const files = Array.from(e.target.files);
    form.images = files;
    filePreviews.value = files.map(file => URL.createObjectURL(file));
    currentImageIndex.value = 0;
};

// --- Actions ---
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
    filePreviews.value = [];
    currentImageIndex.value = 0;
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
        <div v-if="show" class="fixed inset-0 z-100 overflow-y-auto flex items-start justify-center px-4 py-6 sm:items-center sm:px-0">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="closeModal"></div>

            <div class="relative w-full max-w-4xl bg-background-dark rounded-3xl shadow-2xl flex flex-col md:flex-row border border-primary/20 transform transition-all overflow-hidden my-auto">
                
                <div class="w-full md:w-5/12 bg-black/20 p-6 md:p-8 flex flex-col relative border-b md:border-b-0 md:border-r border-white/5">

                    <div class="flex-1 flex flex-col items-center justify-center relative min-h-45 md:min-h-50 group">
                        <button v-if="images.length > 1" @click.prevent="prevImage" class="absolute left-0 p-2 text-primary/50 hover:text-primary z-10">
                            <span class="material-symbols-outlined text-3xl">chevron_left</span>
                        </button>

                        <div class="w-40 h-40 md:w-56 md:h-56 relative z-0">
                            <img v-if="currentImage" :src="currentImage" class="w-full h-full object-cover rounded-2xl drop-shadow-2xl" />
                            <div v-else class="w-full h-full flex items-center justify-center bg-white/5 rounded-3xl border-2 border-dashed border-white/10 text-white/20">
                                <span class="material-symbols-outlined text-6xl">add_a_photo</span>
                            </div>
                        </div>

                        <button v-if="images.length > 1" @click.prevent="nextImage" class="absolute right-0 p-2 text-primary/50 hover:text-primary z-10">
                            <span class="material-symbols-outlined text-3xl">chevron_right</span>
                        </button>
                    </div>
                </div>

                <div class="w-full md:w-7/12 p-6 md:p-8 flex flex-col bg-background-dark">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-xl font-bold text-white uppercase tracking-tight">{{ t('foodDiary.create_food_title') }}</h3>
                        </div>
                        <button @click="closeModal" class="text-slate-400 hover:text-white transition-colors">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <form @submit.prevent="submitForm" class="space-y-4 flex-1 flex flex-col">
                        <Input v-model="form.name" label="FOOD NAME" placeholder="e.g. Protein Bar" :error="form.errors.name" />

                        <div class="grid grid-cols-5 gap-4">
                            <div class="col-span-3">
                                <Input v-model="form.amount" label="AMOUNT" type="number" :error="form.errors.amount" />
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs text-white/60 mb-2 uppercase">UNIT</label>
                                <div class="relative">
                                    <select v-model="form.unit" class="w-full appearance-none bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-1 focus:ring-primary focus:border-primary outline-none cursor-pointer transition-all">
                                        <option v-for="u in unitOptions" :key="u" :value="u" class="bg-slate-900">{{ u }}</option>
                                    </select>
                                    <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-primary pointer-events-none text-sm">expand_more</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <Input v-model="form.calorie" label="CALORIES (KCAL)" type="number" :error="form.errors.calorie" />
                            <Input v-model="form.protein" label="PROTEIN (G)" type="number" step="0.1" :error="form.errors.protein" />
                            <Input v-model="form.carb" label="CARBS (G)" type="number" step="0.1" :error="form.errors.carb" />
                            <Input v-model="form.fat" label="FAT (G)" type="number" step="0.1" :error="form.errors.fat" />
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs text-white/60 mb-2 uppercase">Food Photos</label>
                            <div class="relative group">
                                <input type="file" multiple accept="image/*" @change="onFileChange" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
                                <div class="border border-white/10 bg-white/5 rounded-xl p-4 text-center group-hover:border-primary/50 transition-all flex items-center justify-center gap-3">
                                    <span class="material-symbols-outlined text-primary">upload_file</span>
                                    <span class="text-sm text-slate-400 font-medium">Add photos</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-auto pt-6">
                            <GlowingButton type="submit" :disabled="form.processing" class="w-full transition-all active:scale-[0.98] flex items-center justify-center gap-2 h-14">
                                <span>{{ t('foodDiary.save_food') }}</span>
                                <span v-if="form.processing" class="material-symbols-outlined animate-spin text-xl">progress_activity</span>
                                <span v-else class="material-symbols-outlined text-xl">save</span>
                            </GlowingButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Transition>
</template>