<script setup>
import { computed, ref } from "vue";
import { useForm } from "laravel-precognition-vue";
import GlowingButton from "@/Components/glowingButton.vue";
import Input from "@/Components/input.vue";
import { trans as t } from 'laravel-vue-i18n';

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
    <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0"
        enter-to-class="opacity-100" leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100"
        leave-to-class="opacity-0">
        <div v-if="show"
            class="fixed inset-0 z-[100] overflow-y-auto flex items-start justify-center px-4 py-6 sm:items-center sm:px-0">
            <div class="fixed inset-0 bg-background-dark/80 backdrop-blur-sm transition-opacity" @click="closeModal">
            </div>

            <div
                class="relative w-full max-w-4xl bg-background-dark rounded-[2rem] shadow-2xl flex flex-col md:flex-row border border-neutral-border transform transition-all overflow-hidden my-auto">

                <div
                    class="w-full md:w-5/12 bg-neutral-dark/40 p-6 md:p-8 flex flex-col relative border-b md:border-b-0 md:border-r border-neutral-border">
                    <div class="mb-6">
                        <h2 class="text-xs font-black text-primary uppercase tracking-[0.2em] mb-1">Visuals</h2>
                        <p class="text-secondary-text text-sm">Preview of your food entry</p>
                    </div>

                    <div class="flex-1 flex flex-col items-center justify-center relative min-h-45 md:min-h-50 group">
                        <button v-if="images.length > 1" @click.prevent="prevImage"
                            class="absolute left-0 p-2 text-primary/40 hover:text-primary z-10 transition-colors">
                            <span class="material-symbols-outlined text-4xl">chevron_left</span>
                        </button>

                        <div class="w-44 h-44 md:w-60 md:h-60 relative z-0">
                            <img v-if="currentImage" :src="currentImage"
                                class="w-full h-full object-cover rounded-3xl border border-neutral-border shadow-2xl" />
                            <div v-else
                                class="w-full h-full flex flex-col items-center justify-center bg-background-dark/60 rounded-3xl border-2 border-dashed border-neutral-border text-secondary-text/30 group-hover:border-primary/30 transition-colors">
                                <span class="material-symbols-outlined text-6xl mb-2">add_a_photo</span>
                                <span class="text-[10px] font-black uppercase tracking-widest">No Image</span>
                            </div>
                            <div class="absolute inset-0 bg-primary/5 blur-3xl rounded-full -z-10"></div>
                        </div>

                        <button v-if="images.length > 1" @click.prevent="nextImage"
                            class="absolute right-0 p-2 text-primary/40 hover:text-primary z-10 transition-colors">
                            <span class="material-symbols-outlined text-4xl">chevron_right</span>
                        </button>
                    </div>

                    <div v-if="images.length > 1" class="mt-4 flex justify-center gap-1.5">
                        <div v-for="(_, i) in images" :key="i" class="size-1.5 rounded-full transition-all duration-300"
                            :class="i === currentImageIndex ? 'w-4 bg-primary' : 'bg-neutral-border'">
                        </div>
                    </div>
                </div>

                <div class="w-full md:w-7/12 p-6 md:p-8 flex flex-col bg-background-dark">
                    <div class="flex justify-between items-start mb-6">
                        <h3 class="text-xl font-black text-main-text uppercase tracking-tight">{{
                            t('foodDiary.create_food_title') }}</h3>
                        <button @click="closeModal" class="text-secondary-text hover:text-main-text transition-colors">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <form @submit.prevent="submitForm" class="space-y-5 flex-1 flex flex-col">
                        <Input v-model="form.name" label="FOOD NAME" placeholder="e.g. Greek Yogurt"
                            :error="form.errors.name" />

                        <div class="grid grid-cols-5 gap-4">
                            <div class="col-span-3">
                                <Input v-model="form.amount" label="BASE AMOUNT" type="number"
                                    :error="form.errors.amount" />
                            </div>
                            <div class="col-span-2">
                                <label
                                    class="block text-[10px] font-black text-primary uppercase tracking-widest mb-2 ml-1">UNIT</label>
                                <div class="relative">
                                    <select v-model="form.unit"
                                        class="w-full appearance-none bg-neutral-dark/40 border border-neutral-border rounded-xl px-4 py-3 text-main-text focus:ring-2 focus:ring-primary focus:border-transparent outline-none cursor-pointer transition-all">
                                        <option v-for="u in unitOptions" :key="u" :value="u" class="bg-background-dark">
                                            {{ u }}</option>
                                    </select>
                                    <span
                                        class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-primary pointer-events-none text-sm">expand_more</span>
                                </div>
                            </div>
                        </div>

                        <div
                            class="grid grid-cols-2 gap-4 bg-neutral-dark/20 p-4 rounded-2xl border border-neutral-border/50">
                            <Input v-model="form.calorie" label="KCAL" type="number" :error="form.errors.calorie" />
                            <Input v-model="form.protein" label="PROTEIN (G)" type="number" step="0.1"
                                :error="form.errors.protein" />
                            <Input v-model="form.carb" label="CARBS (G)" type="number" step="0.1"
                                :error="form.errors.carb" />
                            <Input v-model="form.fat" label="FAT (G)" type="number" step="0.1"
                                :error="form.errors.fat" />
                        </div>

                        <div class="space-y-2">
                            <label
                                class="block text-[10px] font-black text-secondary-text uppercase tracking-widest mb-2 ml-1">Food
                                Photos</label>
                            <div class="relative group">
                                <input type="file" multiple accept="image/*" @change="onFileChange"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
                                <div
                                    class="border border-neutral-border bg-neutral-dark/40 rounded-xl p-4 text-center group-hover:border-primary/50 transition-all flex items-center justify-center gap-3">
                                    <span
                                        class="material-symbols-outlined text-primary group-hover:scale-110 transition-transform">add_photo_alternate</span>
                                    <span class="text-sm text-secondary-text font-bold">Add or Replace Photos</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-auto pt-4">
                            <GlowingButton type="submit" :disabled="form.processing"
                                class="w-full transition-all active:scale-[0.98] h-14 rounded-xl">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="font-black uppercase tracking-widest text-sm">{{
                                        t('foodDiary.save_food') }}</span>
                                    <span v-if="form.processing"
                                        class="material-symbols-outlined animate-spin text-xl">progress_activity</span>
                                    <span v-else class="material-symbols-outlined text-xl">cloud_upload</span>
                                </div>
                            </GlowingButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Transition>
</template>