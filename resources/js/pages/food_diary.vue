<script setup>
import { getBaseFoodMacros, loadDiary } from "../helpers/functions";

import Carousel from 'primevue/carousel';
import { computed, ref, watch } from "vue";
import { usePage } from "@inertiajs/vue3";
import axios from "axios";

import Paginator from "primevue/paginator";
import FloatLabel from "primevue/floatlabel";
import InputText from "primevue/inputtext";
import Button from "primevue/button";
import ConfirmPopup from 'primevue/confirmpopup';
import { useConfirm } from "primevue/useconfirm";
import Select from "primevue/select";
import FileUpload from "primevue/fileupload";
import Image from 'primevue/image'


import { useForm } from "laravel-precognition-vue";

import 'primeicons/primeicons.css'

import AppLayout from "@/Layouts/AppLayout.vue"

defineOptions({
  layout: AppLayout,
})


const page = usePage();

const mealTypeOptions = [
  { label: "Breakfast", value: "breakfast" },
  { label: "Lunch", value: "lunch" },
  { label: "Dinner", value: "dinner" },
  { label: "Snack", value: "snack" },
  { label: "Other", value: "other" },
];

const unitOptions = ref(['g', 'dkg', 'kg', 'l', 'cl', 'dl']);

const selectedDate = ref(
  page.props.selectedDate ?? new Date().toISOString().slice(0, 10)
);

const search = ref("");
const first = 1;
const rows = 5;
const selectedFood = ref(null);
const pageCount = ref(0);
const entries = ref([]);
let searchedFoods = ref([]);

async function searchFood(page) {
  const { data } = await axios.get(`/fdiary/getFoods/${search.value}/${page}`);
  console.log(data)
  searchedFoods.value = data.result;
}

async function getPageCount() {
  const { data } = await axios.get(`/fdiary/getPageCount/${search.value}`);
  pageCount.value = data.pageCount;
}

const selectFood = (food) => {
  selectedFood.value = food;
  addEntryForm.food_id = food?.id ?? null;
};

function onImagesSelect(event) {
  createFoodForm.images = event.files
}

watch(search, () => {
  if (search.value == "") return
  getPageCount();
  searchFood(1);
}, { immediate: true })

watch(selectedDate, async (date) => {
  const { data } = await axios.get(`/fdiary/diary/${date}`);
  entries.value = data.diary ?? [];
}, { immediate: true });

const shiftDate = (days) => {
  const dt = new Date(selectedDate.value);
  dt.setDate(dt.getDate() + days);
  selectedDate.value = dt.toISOString().slice(0, 10);
};

const addEntryForm = useForm("post", "/fdiary/entry", {
  date: selectedDate.value,
  food_id: null,
  meal_type: "other",
  unit: "g",
  amount: 100,
});

const addSelectedFoodToSelectedDate = async () => {
  if (!selectedFood.value?.id) return;

  addEntryForm.food_id = selectedFood.value.id;
  addEntryForm.date = selectedDate.value;

  await addEntryForm.submit();

  const { data } = await axios.get(`/fdiary/diary/${selectedDate.value}`);
  entries.value = data.diary ?? [];
};

const createFoodForm = useForm("post", "/fdiary/create", {
  name: "",
  unit: "g",
  amount: 100,
  fat: 0,
  carb: 0,
  protein: 0,
  calorie: 0,
  note: "",
  images: [],
});

function onCreateFood() {
  createFoodForm.submit({
    forceFormData: true,
    onSuccess: () => {
      createFoodForm.reset()
      createFoodForm.images = []
    }
  })
}

const confirm = useConfirm()

const deleteionConfirmation = (entryId) => {
  confirm.require({
    group: 'headless',
    accept: async () => {
      axios.delete(`/fdiary/entry/${selectedDate.value}/${entryId}`)
      const { data } = await axios.get(`/fdiary/diary/${selectedDate.value}`);
      entries.value = data.diary ?? [];
    }
  })
}

const images = computed(() => {
  if (!selectedFood.value?.image_paths) return []

  const parts = selectedFood.value.image_paths.split(':').filter(Boolean)
  const folder = parts[0].trim()
  const filenames = parts.slice(1)

  return filenames.map(file => ({
    itemImageSrc: `/storage/foods/${folder}/${file.trim()}`,
    thumbnailImageSrc: `/storage/foods/${folder}/${file.trim()}`,
    alt: selectedFood.value.name
  }))
})
</script>

<template>
  <main class="mx-auto w-full max-w-7xl px-4 py-6">
    <!-- DATE NAV -->
    <section class="mb-6 rounded-2xl border p-5">
      <h2 class="text-lg font-semibold">{{ $t('foodDiary.date') }}</h2>

      <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div class="flex items-center gap-2">
          <Button label="←" severity="secondary" type="button" @click="shiftDate(-1)" />
          <div class="space-y-1">
            <input v-model="selectedDate" type="date" class="w-full rounded-lg border px-3 py-2 text-sm" />
          </div>
          <Button label="→" severity="secondary" type="button" @click="shiftDate(1)" />
        </div>
      </div>
    </section>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 mb-6">
      <!-- SEARCH -->
      <section class="rounded-2xl border p-5">
        <h2 class="text-lg font-semibold">{{ $t('foodDiary.search_title') }}</h2>
        <InputText class="mt-4 flex gap-2 w-full" v-model="search" type="text"
          :placeholder="$t('foodDiary.search_placeholder')" />

        <div class="mt-4">
          <ul class="space-y-2">
            <li v-for="food in searchedFoods" :key="food.id ?? food.name" class="cursor-pointer rounded-xl border p-3"
              @click="selectFood(food)">
              <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                  <div class="truncate font-semibold">
                    {{ food.name }}
                  </div>
                  <div class="mt-1 text-xs">
                    {{ food.calorie }} kcal · {{ $t('foodDiary.fat_label') }} {{ food.fat }} g · {{
                      $t('foodDiary.carb_label') }}
                    {{ food.carb }} g · {{ $t('foodDiary.protein_label') }} {{ food.protein }} g
                  </div>
                </div>

                <div class="shrink-0 rounded-full border px-2 py-1 text-xs">
                  #{{ food.id }}
                </div>
              </div>
            </li>
          </ul>

          <Paginator v-if="searchedFoods.length" class="mt-8" :rows="rows" :totalRecords="pageCount"
            @page="(e) => searchFood(e.page + 1)">
            <template #container="{ first, last, page, pageCount, prevPageCallback, nextPageCallback, totalRecords }">
              <div
                class="flex items-center border border-primary bg-transparent rounded-full w-full py-1 px-2 justify-between">
                <Button icon="pi pi-chevron-left" rounded variant="text" @click="prevPageCallback"
                  :disabled="page === 0" />
                <div class="text-color font-medium">
                  <span class="hidden sm:block">{{ $t('foodDiary.paginator_visible_range', {
                    first: first, last: last, total:
                      totalRecords
                  }) }}</span>
                  <span class="block sm:hidden">{{ $t('foodDiary.paginator_page_of', {
                    page: page + 1, pageCount: pageCount
                  }) }}</span>
                </div>
                <Button icon="pi pi-chevron-right" rounded variant="text" @click="nextPageCallback"
                  :disabled="page === pageCount - 1" />
              </div>
            </template>
          </Paginator>
        </div>
      </section>

      <!-- SELECTED FOOD -->
      <section class="rounded-2xl border p-5">
        <h2 class="text-lg font-semibold">{{ $t('foodDiary.selected_food_title') }}</h2>
        <p class="mt-1 text-sm">{{ $t('foodDiary.selected_food_subtitle') }}</p>

        <div v-if="selectedFood" class="mt-4 rounded-xl border p-4">
          <div class="text-xl font-semibold">{{ selectedFood.name }}</div>

          <Carousel v-if="images.length" :value="images" :numVisible="3" :numScroll="1" circular>
            <template #item="{ data }">
              <Image :src="data.itemImageSrc" preview imageClass="h-40 w-full object-cover rounded-xl" />
            </template>
          </Carousel>

          <div v-else
            class="mt-4 h-40 flex items-center justify-center rounded-xl border border-dashed text-sm text-gray-500">
            No images available
          </div>

          <div class="mt-4 space-y-3">
            <div class="space-y-1">
              <label class="text-xs font-medium">{{ $t('foodDiary.meal_type_label') }}</label>

              <Select v-model="addEntryForm.meal_type" :options="mealTypeOptions" optionLabel="label"
                optionValue="value" class="w-full" :placeholder="$t('foodDiary.meal_type_placeholder')" />
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <FloatLabel variant="on">
                  <InputText id="food_amount" v-model="addEntryForm.amount" type="number" class="w-full" />
                  <label for="food_amount">{{ $t('foodDiary.amount_label') }}</label>
                </FloatLabel>
                <small v-if="addEntryForm.validate('amount')" class="block text-xs">
                  {{ addEntryForm.errors.amount }}
                </small>
              </div>

              <div>
                <FloatLabel variant="on">
                  <Select inputId="food_unit" v-model="addEntryForm.unit" :options="unitOptions" class="w-full" />
                  <label for="food_unit">{{ $t('foodDiary.unit_label') }}</label>
                </FloatLabel>
                <small v-if="addEntryForm.invalid('unit')" class="block text-xs">
                  {{ addEntryForm.errors.unit }}
                </small>
              </div>
            </div>

            <button type="button" class="w-full rounded-lg border px-3 py-2 text-sm font-medium"
              :disabled="addEntryForm.processing" @click="addSelectedFoodToSelectedDate">
              {{ $t('foodDiary.add_button') }} {{ selectedDate }}
            </button>
          </div>
        </div>

        <div v-else class="mt-4 rounded-xl border border-dashed p-6 text-sm">
          {{ $t('foodDiary.no_selected_food') }}
        </div>
      </section>

      <!-- CREATE FOOD -->
      <section class="rounded-2xl border p-5">
        <h2 class="text-lg font-semibold">{{ $t('foodDiary.create_food_title') }}</h2>

        <form class="mt-5 space-y-4" novalidate @submit.prevent="onCreateFood">
          <div class="space-y-3">
            <div>
              <FloatLabel variant="on">
                <InputText id="food_name" v-model="createFoodForm.name" class="w-full"
                  @change="createFoodForm.validate('name')" />
                <label for="food_name">{{ $t('foodDiary.food_name_label') }}</label>
              </FloatLabel>
              <small v-if="createFoodForm.invalid('name')" class="block text-xs">
                {{ createFoodForm.errors.name }}
              </small>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <!-- amount -->
              <div>
                <FloatLabel variant="on">
                  <InputText id="food_amount" v-model="createFoodForm.amount" type="number" class="w-full"
                    @change="createFoodForm.validate('amount')" />
                  <label for="food_amount">{{ $t('foodDiary.amount_label') }}</label>
                </FloatLabel>
                <small v-if="createFoodForm.invalid('amount')" class="block text-xs">
                  {{ createFoodForm.errors.amount }}
                </small>
              </div>

              <div>
                <FloatLabel variant="on">
                  <Select inputId="food_unit" v-model="createFoodForm.unit" :options="unitOptions" class="w-full"
                    @change="createFoodForm.validate('unit')" />
                  <label for="food_unit">{{ $t('foodDiary.unit_label') }}</label>
                </FloatLabel>
                <small v-if="createFoodForm.invalid('unit')" class="block text-xs">
                  {{ createFoodForm.errors.unit }}
                </small>
              </div>
            </div>

            <div>
              <FloatLabel variant="on">
                <InputText id="food_calorie" v-model="createFoodForm.calorie" class="w-full"
                  @change="createFoodForm.validate('calorie')" />
                <label for="food_calorie">{{ $t('foodDiary.calorie_label') }}</label>
              </FloatLabel>
              <small v-if="createFoodForm.invalid('calorie')" class="block text-xs">
                {{ createFoodForm.errors.calorie }}
              </small>
            </div>

            <div>
              <FloatLabel variant="on">
                <InputText id="food_fat" v-model="createFoodForm.fat" class="w-full"
                  @change="createFoodForm.validate('fat')" />
                <label for="food_fat">{{ $t('foodDiary.fat_label') }}</label>
              </FloatLabel>
              <small v-if="createFoodForm.invalid('fat')" class="block text-xs">
                {{ createFoodForm.errors.fat }}
              </small>
            </div>

            <div>
              <FloatLabel variant="on">
                <InputText id="food_carb" v-model="createFoodForm.carb" class="w-full"
                  @change="createFoodForm.validate('carb')" />
                <label for="food_carb">{{ $t('foodDiary.carb_label') }}</label>
              </FloatLabel>
              <small v-if="createFoodForm.invalid('carb')" class="block text-xs">
                {{ createFoodForm.errors.carb }}
              </small>
            </div>

            <div>
              <FloatLabel variant="on">
                <InputText id="food_protein" v-model="createFoodForm.protein" class="w-full"
                  @change="createFoodForm.validate('protein')" />
                <label for="food_protein">{{ $t('foodDiary.protein_label') }}</label>
              </FloatLabel>
              <small v-if="createFoodForm.invalid('protein')" class="block text-xs">
                {{ createFoodForm.errors.protein }}
              </small>
            </div>

            <div>
              <FloatLabel variant="on">
                <InputText id="food_note" v-model="createFoodForm.note" class="w-full"
                  @change="createFoodForm.validate('note')" />
                <label for="food_note">{{ $t('foodDiary.note_label') }}</label>
              </FloatLabel>
              <small v-if="createFoodForm.invalid('note')" class="block text-xs">
                {{ createFoodForm.errors.note }}
              </small>
            </div>
          </div>

          <div class="space-y-2">
            <div class="text-sm font-medium">{{ $t('foodDiary.image_optional') }}</div>

            <FileUpload mode="basic" name="images[]" multiple :fileLimit="5" accept="image/*" :maxFileSize="4_000_000"
              customUpload @select="onImagesSelect" class="w-full" />

            <small v-if="createFoodForm.invalid('image')" class="block text-xs">
              {{ createFoodForm.errors.image }}
            </small>
          </div>

          <Button type="submit" :label="$t('foodDiary.save_food')" class="w-full"
            :disabled="createFoodForm.processing" />
        </form>
      </section>
    </div>

    <!-- DIARY -->
    <section class="rounded-2xl border p-5">
      <h2 class="text-lg font-semibold">{{ $t('foodDiary.diary_title') }}: {{ selectedDate }}</h2>

      <div v-if="entries.length" class="mt-4 space-y-3">

        <ul class="space-y-2">
          <li v-for="food in entries" :key="food.id" class="rounded-xl border p-3">
            <div class="flex items-start justify-between gap-3">
              <div class="min-w-0">
                <div class="truncate font-semibold">{{ food.name }}</div>
                <div class="mt-1 text-xs">
                  {{ food.calorie }} kcal · {{ food.amount }} {{ food.unit }}
                  · {{ $t('foodDiary.' + food.pivot.meal_type) }}
                </div>
              </div>

              <div class="flex items-center gap-2">
                <Button :label="$t('foodDiary.delete')" severity="danger" size="small" type="button"
                  @click="deleteionConfirmation(food.pivot.id)" />
              </div>
            </div>
          </li>
        </ul>
      </div>

      <div v-else class="mt-4 rounded-xl border border-dashed p-6 text-sm">
        {{ $t('foodDiary.no_entries') }}
      </div>
    </section>

    <ConfirmPopup group="headless">
      <template #container="{ acceptCallback, rejectCallback }">
        <div class="rounded p-4">
          <span>{{ $t('foodDiary.delete_confirmation') }}</span>
          <div class="flex items-center gap-2 mt-4">
            <Button :label="$t('foodDiary.delete')" size="small" severity="danger" @click="acceptCallback"></Button>
            <Button :label="$t('foodDiary.dialog_close')" variant="outlined" @click="rejectCallback"
              severity="secondary" size="small" text></Button>
          </div>
        </div>
      </template>
    </ConfirmPopup>

  </main>
</template>