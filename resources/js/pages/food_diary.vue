<script setup>
import { getBaseFoodMacros, loadDiary } from "../helpers/functions";

import { ref, computed, watch } from "vue";
import { usePage, router } from "@inertiajs/vue3";

import Navbar from "../components/navbar.vue";
import Footer from "../components/footer.vue";

import Paginator from "primevue/paginator";
import FloatLabel from "primevue/floatlabel";
import InputText from "primevue/inputtext";
import Button from "primevue/button";
import Dialog from "primevue/dialog";
import Select from "primevue/select";
import FileUpload from "primevue/fileupload";

import { useForm } from "laravel-precognition-vue";

const page = usePage();


const mealTypeOptions = [
  { label: "Breakfast", value: "breakfast" },
  { label: "Lunch", value: "lunch" },
  { label: "Dinner", value: "dinner" },
  { label: "Snack", value: "snack" },
  { label: "Other", value: "other" },
];

const unitOptions = ref(['g', 'dkg', 'kg', 'l', 'cl', 'dl']);
const foods = ref(page.props.foods ?? []);
const selectedDate = ref(
  page.props.selectedDate ?? new Date().toISOString().slice(0, 10)
);
const selectedDiary = ref(page.props.selectedDiary ?? null);

const search = ref("");
const first = ref(0);
const rows = ref(5);
const selectedFood = ref(null);

const showResponseDialog = ref(false);
const responseMessage = ref("");

const showSuccessDialog = ref(false);

const imageSrc = (food) => {
  if (!food?.image_path) return null;
  return `/storage/${food.image_path}`;
};

const filteredFoods = computed(() => {
  const q = search.value.trim().toLowerCase();
  if (!q) return [];
  return foods.value.filter((food) =>
    (food.name ?? "").toLowerCase().includes(q)
  );
});

const paginatedFoods = computed(() =>
  filteredFoods.value.slice(first.value, first.value + rows.value)
);

watch(search, () => {
  first.value = 0;
  selectedFood.value = null;
});

const selectFood = (food) => {
  selectedFood.value = food;
  addEntryForm.food_id = food?.id ?? null;
};

const entries = computed(() => selectedDiary.value?.foods ?? []);

watch(selectedDate, async (d) => {
  const result = await loadDiary("/fdiary/diary/", d);
  addEntryForm.date = d;

  if (result.ok) {
    selectedDiary.value = result.diary;
  } else {
    selectedDiary.value = null;
  }
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
  unit: null,
  amount: 1,
});

const addSelectedFoodToSelectedDate = async () => {
  if (!selectedFood.value?.id) return;

  addEntryForm.food_id = selectedFood.value.id;
  addEntryForm.date = selectedDate.value;

  try {
    console.log(addEntryForm)
    await addEntryForm.submit();

    responseMessage.value = `Added: ${selectedFood.value.name} (${addEntryForm.meal_type}) x${addEntryForm.amount} on ${selectedDate.value}`;
    showResponseDialog.value = true;

    await loadDiary("/fdiary/diary/", selectedDate.value);
  } catch (e) {
    responseMessage.value =
      addEntryForm.errors?.food_id ||
      addEntryForm.errors?.date ||
      "Could not add entry.";
    showResponseDialog.value = true;
  }
};

const deleteEntry = async (entryId) => {
  if (!entryId) return;

  try {
    const url = `/fdiary/entry?entry_id=${encodeURIComponent(entryId)}`;
    const res = await fetch(url, {
      method: "DELETE",
      headers: {
        Accept: "application/json",
        "X-CSRF-TOKEN": page.props.csrf_token,
      },
      credentials: "same-origin",
    });

    if (!res.ok) throw new Error("Failed to delete entry.");

    responseMessage.value = "Entry deleted.";
    showResponseDialog.value = true;

    await loadDiary("/fdiary/diary/", selectedDate.value);
  } catch (e) {
    responseMessage.value = e?.message ?? "Failed to delete entry.";
    showResponseDialog.value = true;
  }
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
  image: null,
});

const onSelectImage = (event) => {
  createFoodForm.image = event.files?.[0] ?? null;
};

const onRemoveImage = () => {
  createFoodForm.image = null;
};

const onCreateFood = async () => {
  try {
    await createFoodForm.submit({ forceFormData: true });

    createFoodForm.reset();
    onRemoveImage();

    showSuccessDialog.value = true;
    router.reload({ only: ["foods"] });
  } catch (e) {
    responseMessage.value =
      createFoodForm.errors?.name ||
      createFoodForm.errors?.unit ||      // <--- Add error check
      createFoodForm.errors?.amount ||  // <--- Add error check
      createFoodForm.errors?.calorie ||
      createFoodForm.errors?.fat ||
      createFoodForm.errors?.carb ||
      createFoodForm.errors?.protein ||
      createFoodForm.errors?.note ||
      createFoodForm.errors?.image ||
      "Create failed.";
    showResponseDialog.value = true;
  }
};
</script>

<template>
  <div class="min-h-screen">
    <Navbar />

    <main class="mx-auto w-full max-w-7xl px-4 py-6">
      <!-- DATE NAV -->
      <section class="mb-6 rounded-2xl border p-5">
        <h2 class="text-lg font-semibold">Food diary date</h2>

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
          <h2 class="text-lg font-semibold">Keresés</h2>
          <p class="mt-1 text-sm">Keress név alapján.</p>

          <div class="mt-4 flex gap-2">
            <input v-model="search" type="text" placeholder="Keress ételt..."
              class="w-full rounded-lg border px-3 py-2 text-sm outline-none" />
          </div>

          <div class="mt-4">
            <ul v-if="paginatedFoods.length" class="space-y-2">
              <li v-for="food in paginatedFoods" :key="food.id ?? food.name"
                class="cursor-pointer rounded-xl border p-3" @click="selectFood(food)">
                <div class="flex items-start justify-between gap-3">
                  <div class="min-w-0">
                    <div class="truncate font-semibold">
                      {{ food.name }}
                    </div>
                    <div class="mt-1 text-xs">
                      {{ food.calorie }} kcal · Fat {{ food.fat }} g · Carb
                      {{ food.carb }} g · Protein {{ food.protein }} g
                    </div>
                  </div>

                  <div class="shrink-0 rounded-full border px-2 py-1 text-xs">
                    #{{ food.id }}
                  </div>
                </div>
              </li>
            </ul>

            <div v-else class="rounded-xl border border-dashed p-6 text-sm">
              Írj be valamit a kereséshez.
            </div>

            <div class="mt-4">
              <Paginator v-if="filteredFoods.length > rows" :first="first" :rows="rows"
                :totalRecords="filteredFoods.length" :pageLinkSize="4" @page="(e) => (first = e.first)" />
            </div>
          </div>
        </section>

        <!-- SELECTED FOOD -->
        <section class="rounded-2xl border p-5">
          <h2 class="text-lg font-semibold">Kiválasztott étel</h2>
          <p class="mt-1 text-sm">Add hozzá a kiválasztott dátumhoz.</p>

          <div v-if="selectedFood" class="mt-4 rounded-xl border p-4">
            <div class="text-xl font-semibold">{{ selectedFood.name }}</div>

            <img v-if="selectedFood.image_path" :src="imageSrc(selectedFood)"
              class="mt-4 h-44 w-full rounded-xl border object-cover" alt="Food image" />

            <div class="mt-4 space-y-3">
              <div class="space-y-1">
                <label class="text-xs font-medium">Meal type</label>

                <Select v-model="addEntryForm.meal_type" :options="mealTypeOptions" optionLabel="label"
                  optionValue="value" class="w-full" placeholder="Select meal type" />
              </div>

              <div class="grid grid-cols-2 gap-3">
                <div>
                  <FloatLabel variant="on">
                    <InputText id="food_amount" v-model="addEntryForm.amount" type="number" class="w-full"
                      />
                    <label for="food_amount">Mennyiség</label>
                  </FloatLabel>
                  <small v-if="addEntryForm.validate('amount')" class="block text-xs">
                    {{ addEntryForm.errors.amount }}
                  </small>
                </div>

                <div>
                  <FloatLabel variant="on">
                    <Select inputId="food_unit" v-model="addEntryForm.unit" :options="unitOptions" class="w-full"
                       />
                    <label for="food_unit">Mértékegység</label>
                  </FloatLabel>
                  <small v-if="addEntryForm.invalid('unit')" class="block text-xs">
                    {{ addEntryForm.errors.unit }}
                  </small>
                </div>
              </div>

              <button type="button" class="w-full rounded-lg border px-3 py-2 text-sm font-medium"
                :disabled="addEntryForm.processing" @click="addSelectedFoodToSelectedDate">
                Hozzáadás {{ selectedDate }}
              </button>
            </div>
          </div>

          <div v-else class="mt-4 rounded-xl border border-dashed p-6 text-sm">
            Nincs kiválasztott étel.
          </div>
        </section>

        <!-- CREATE FOOD -->
        <section class="rounded-2xl border p-5">
          <h2 class="text-lg font-semibold">Új étel hozzáadása</h2>

          <form class="mt-5 space-y-4" novalidate @submit.prevent="onCreateFood">
            <div class="space-y-3">
              <div>
                <FloatLabel variant="on">
                  <InputText id="food_name" v-model="createFoodForm.name" class="w-full"
                    @change="createFoodForm.validate('name')" />
                  <label for="food_name">Név</label>
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
                    <label for="food_amount">Mennyiség</label>
                  </FloatLabel>
                  <small v-if="createFoodForm.invalid('amount')" class="block text-xs">
                    {{ createFoodForm.errors.amount }}
                  </small>
                </div>

                <div>
                  <FloatLabel variant="on">
                    <Select inputId="food_unit" v-model="createFoodForm.unit" :options="unitOptions" class="w-full"
                      @change="createFoodForm.validate('unit')" />
                    <label for="food_unit">Mértékegység</label>
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
                  <label for="food_calorie">Kalória</label>
                </FloatLabel>
                <small v-if="createFoodForm.invalid('calorie')" class="block text-xs">
                  {{ createFoodForm.errors.calorie }}
                </small>
              </div>

              <div>
                <FloatLabel variant="on">
                  <InputText id="food_fat" v-model="createFoodForm.fat" class="w-full"
                    @change="createFoodForm.validate('fat')" />
                  <label for="food_fat">Zsír</label>
                </FloatLabel>
                <small v-if="createFoodForm.invalid('fat')" class="block text-xs">
                  {{ createFoodForm.errors.fat }}
                </small>
              </div>

              <div>
                <FloatLabel variant="on">
                  <InputText id="food_carb" v-model="createFoodForm.carb" class="w-full"
                    @change="createFoodForm.validate('carb')" />
                  <label for="food_carb">Szénhidrát</label>
                </FloatLabel>
                <small v-if="createFoodForm.invalid('carb')" class="block text-xs">
                  {{ createFoodForm.errors.carb }}
                </small>
              </div>

              <div>
                <FloatLabel variant="on">
                  <InputText id="food_protein" v-model="createFoodForm.protein" class="w-full"
                    @change="createFoodForm.validate('protein')" />
                  <label for="food_protein">Fehérje</label>
                </FloatLabel>
                <small v-if="createFoodForm.invalid('protein')" class="block text-xs">
                  {{ createFoodForm.errors.protein }}
                </small>
              </div>

              <div>
                <FloatLabel variant="on">
                  <InputText id="food_note" v-model="createFoodForm.note" class="w-full"
                    @change="createFoodForm.validate('note')" />
                  <label for="food_note">Megjegyzés</label>
                </FloatLabel>
                <small v-if="createFoodForm.invalid('note')" class="block text-xs">
                  {{ createFoodForm.errors.note }}
                </small>
              </div>
            </div>

            <div class="space-y-2">
              <div class="text-sm font-medium">Kép (opcionális)</div>

              <FileUpload mode="basic" name="image" accept="image/*" :maxFileSize="4_000_000"
                chooseLabel="Kép kiválasztása" customUpload class="w-full" @select="onSelectImage"
                @clear="onRemoveImage" />

              <small v-if="createFoodForm.invalid('image')" class="block text-xs">
                {{ createFoodForm.errors.image }}
              </small>
            </div>

            <Button type="submit" label="Étel mentése" class="w-full" :disabled="createFoodForm.processing" />
          </form>
        </section>
      </div>

      <!-- DIARY -->
      <section class="rounded-2xl border p-5">
        <h2 class="text-lg font-semibold">Naptár: {{ selectedDate }}</h2>

        <div v-if="entries.length" class="mt-4 space-y-3">

          <ul class="space-y-2">
            <li v-for="food in entries" :key="food.pivot?.id" class="rounded-xl border p-3">
              <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                  <div class="truncate font-semibold">{{ food.name }}</div>
                  <div class="mt-1 text-xs">
                    {{ food.calorie }} kcal · {{ food.pivot?.amount }} {{ food.pivot?.unit }}
                    · meal {{ food.pivot?.meal_type }}
                  </div>
                </div>

                <div class="flex items-center gap-2">
                  <Button label="Törlés" severity="danger" size="small" type="button"
                    @click="deleteEntry(food.pivot?.id)" />
                </div>
              </div>
            </li>
          </ul>
        </div>

        <div v-else class="mt-4 rounded-xl border border-dashed p-6 text-sm">
          No entries for this date.
        </div>
      </section>

      <!-- SUCCESS DIALOG -->
      <Dialog v-model:visible="showSuccessDialog" modal :closable="true" :draggable="false" header="Siker"
        class="w-[92vw] max-w-md">
        <p>Az étel sikeresen mentve lett.</p>

        <template #footer>
          <div class="flex w-full justify-end gap-2">
            <Button label="Close" severity="secondary" @click="showSuccessDialog = false" />
          </div>
        </template>
      </Dialog>

      <!-- RESPONSE DIALOG -->
      <Dialog v-model:visible="showResponseDialog" modal :closable="true" :draggable="false" header="Diary"
        class="w-[92vw] max-w-md">
        <p>{{ responseMessage }}</p>

        <template #footer>
          <div class="flex w-full justify-end gap-2">
            <Button label="Close" severity="secondary" @click="showResponseDialog = false" />
          </div>
        </template>
      </Dialog>
    </main>

    <Footer />
  </div>
</template>

<style scoped></style>