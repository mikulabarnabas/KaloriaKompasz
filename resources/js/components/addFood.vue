<script setup>
import { computed, ref } from "vue";
import { useForm } from "laravel-precognition-vue";
import Input from "@/Components/input.vue";
import { trans as t } from 'laravel-vue-i18n';
import Button from "@/Components/button.vue"

const props = defineProps({
    show: Boolean,
    date: String,
});

const emit = defineEmits(['close', 'saved']);

const unitOptions = ['g', 'dkg', 'kg', 'ml', 'cl', 'dl', 'l'];
const imagePreview = ref(null);

const form = useForm("post", "/fdiary/create", {
    name: "",
    amount: 100,
    unit: "g",
    calorie: "",
    fat: "",
    carb: "",
    protein: "",
    notes: "",
    image: null,
});

const onFileChange = (e) => {
    const file = e.target.files[0];
    form.image = file;
    imagePreview.value = URL.createObjectURL(file);
};

const submitForm = () => {
    console.log(form.image)
    form.submit({
        preserveScroll: true,
        onSuccess: () => {
            emit('saved');
            closeModal();
        }
    });
};

const closeModal = () => {
    if (imagePreview.value) {
        URL.revokeObjectURL(imagePreview.value);
        imagePreview.value = null;
    }
    emit('close');
};

const currentImage = computed(() => imagePreview.value);
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
                class="relative w-full max-w-4xl bg-background-dark rounded-4xl shadow-2xl flex flex-col md:flex-row border border-neutral-border transform transition-all overflow-hidden my-auto">

                <div
                    class="w-full md:w-5/12 bg-neutral-dark/40 p-6 md:p-8 flex flex-col relative border-b md:border-b-0 md:border-r border-neutral-border">
                    <div class="mb-6">
                        <h2 class="text-xs font-black text-primary uppercase tracking-[0.2em] mb-1">
                            {{ $t('foodDiary.visuals_title') }}
                        </h2>
                        <p class="text-secondary-text text-sm">{{ $t('foodDiary.visuals_subtitle') }}</p>
                    </div>

                    <div class="flex-1 flex flex-col items-center justify-center relative min-h-45 md:min-h-50 group">
                        <div class="w-44 h-44 md:w-60 md:h-60 relative z-0">
                            <img v-if="currentImage" :src="currentImage"
                                class="w-full h-full object-cover rounded-3xl border border-neutral-border shadow-2xl" />
                            <div v-else
                                class="w-full h-full flex flex-col items-center justify-center bg-background-dark/60 rounded-3xl border-2 border-dashed border-neutral-border text-secondary-text/30 group-hover:border-primary/30 transition-colors">
                                <span class="material-symbols-outlined text-6xl mb-2">add_a_photo</span>
                                <span class="text-[10px] font-black uppercase tracking-widest">
                                    {{ $t('foodDiary.no_image_label') }}
                                </span>
                            </div>
                            <div class="absolute inset-0 bg-primary/5 blur-3xl rounded-full -z-10"></div>
                        </div>
                    </div>
                </div>

                <div class="w-full md:w-7/12 p-6 md:p-8 flex flex-col bg-background-dark">
                    <div class="flex justify-between items-start mb-6">
                        <h3 class="text-xl font-black text-main-text uppercase tracking-tight">
                            {{ t('foodDiary.add_new_food') }}
                        </h3>
                        <button @click="closeModal" class="text-secondary-text hover:text-main-text transition-colors">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <form @submit.prevent="submitForm" class="space-y-5 flex-1 flex flex-col">
                        <Input v-model="form.name" :label="t('foodDiary.food_name_label')"
                            :placeholder="t('foodDiary.food_name_placeholder')" :error="form.errors.name" />

                        <div class="grid grid-cols-5 gap-4">
                            <div class="col-span-3">
                                <Input v-model="form.amount" :label="t('foodDiary.amount_label')" type="number"
                                    :error="form.errors.amount" />
                            </div>
                            <div class="col-span-2">
                                <label
                                    class="block text-[10px] font-black text-primary uppercase tracking-widest mb-2 ml-1">
                                    {{ $t('foodDiary.unit_label') }}
                                </label>
                                <div class="relative">
                                    <select v-model="form.unit"
                                        class="w-full appearance-none bg-neutral-dark/40 border border-neutral-border rounded-xl px-4 py-3 text-main-text focus:ring-2 focus:ring-primary focus:border-transparent outline-none cursor-pointer transition-all">
                                        <option v-for="u in unitOptions" :key="u" :value="u" class="bg-background-dark">
                                            {{ u }}
                                        </option>
                                    </select>
                                    <span
                                        class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-primary pointer-events-none text-sm">expand_more</span>
                                </div>
                            </div>
                        </div>

                        <div
                            class="grid grid-cols-2 gap-4 bg-neutral-dark/20 p-4 rounded-2xl border border-neutral-border/50">
                            <Input v-model="form.calorie" :label="t('foodDiary.calorie_label')" type="number"
                                :error="form.errors.calorie" />
                            <Input v-model="form.protein" :label="t('foodDiary.protein_label_with_unit')" type="number"
                                step="0.1" :error="form.errors.protein" />
                            <Input v-model="form.carb" :label="t('foodDiary.carb_label_with_unit')" type="number"
                                step="0.1" :error="form.errors.carb" />
                            <Input v-model="form.fat" :label="t('foodDiary.fat_label_with_unit')" type="number"
                                step="0.1" :error="form.errors.fat" />
                        </div>

                        <div class="space-y-2">
                            <label
                                class="block text-[10px] font-black text-secondary-text uppercase tracking-widest mb-2 ml-1">
                                {{ $t('foodDiary.image_label') }}
                            </label>
                            <div class="relative group">
                                <input type="file" accept="image/*" @change="onFileChange"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
                                <div
                                    class="border border-neutral-border bg-neutral-dark/40 rounded-xl p-4 text-center group-hover:border-primary/50 transition-all flex items-center justify-center gap-3">
                                    <span
                                        class="material-symbols-outlined text-primary group-hover:scale-110 transition-transform">add_photo_alternate</span>
                                    <span class="text-sm text-secondary-text font-bold">
                                        {{ $t('foodDiary.add_replace_photo') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-auto pt-4">
                            <Button type="submit" :label="t('foodDiary.save_food')" icon="cloud_upload"
                                :loading="form.processing" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Transition>
</template>
