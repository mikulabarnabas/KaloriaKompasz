<script setup>
import { ref, computed, watch } from "vue";
import { usePage, router } from "@inertiajs/vue3";

import Navbar from "../components/navbar.vue";
import Footer from "../components/footer.vue";

import Paginator from "primevue/paginator";
import Divider from "primevue/divider";

import FloatLabel from "primevue/floatlabel";
import InputText from "primevue/inputtext";
import Button from "primevue/button";
import Dialog from "primevue/dialog";
import FileUpload from "primevue/fileupload";

import { useForm } from "laravel-precognition-vue";

const page = usePage();

const foods = ref(page.props.foods ?? []);
const selectedDate = ref(page.props.selectedDate ?? new Date().toISOString().slice(0, 10));
const selectedDiary = ref(page.props.selectedDiary ?? null);

const search = ref("");
const first = ref(0);
const rows = ref(3);
const selectedFood = ref(null);

// feedback dialog
const showResponseDialog = ref(false);
const responseMessage = ref("");

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

const totals = computed(() => {
  const list = entries.value;

  return {
    calories: list.reduce(
      (s, f) => s + Number(f.calories ?? 0) * Number(f.pivot?.quantity ?? 1),
      0
    ),
    fat_g: list.reduce(
      (s, f) => s + Number(f.fat_g ?? 0) * Number(f.pivot?.quantity ?? 1),
      0
    ),
    carbs_g: list.reduce(
      (s, f) => s + Number(f.carbs_g ?? 0) * Number(f.pivot?.quantity ?? 1),
      0
    ),
    protein_g: list.reduce(
      (s, f) => s + Number(f.protein_g ?? 0) * Number(f.pivot?.quantity ?? 1),
      0
    ),
  };
});

// ---- Load diary for a date (API) ----
const loadingDiary = ref(false);

const loadDiary = async (date) => {
  loadingDiary.value = true;

  try {
    const url = `/fdiary/diary?date=${encodeURIComponent(date)}`;
    const res = await fetch(url, {
      headers: {
        Accept: "application/json",
      },
      credentials: "same-origin",
    });

    if (!res.ok) throw new Error("Failed to load diary.");

    const data = await res.json();
    selectedDiary.value = data.diary ?? null;
  } catch (e) {
    selectedDiary.value = null;
    responseMessage.value = e?.message ?? "Failed to load diary.";
    showResponseDialog.value = true;
  } finally {
    loadingDiary.value = false;
  }
};

watch(selectedDate, (d) => {
  // keep URL in sync so refresh works
  router.get(
    "/fdiary",
    { date: d },
    { preserveState: true, replace: true, only: ["selectedDate", "selectedDiary"] }
  );

  // also load immediately (so UI updates without relying on inertia partial reload)
  loadDiary(d);
});

// arrows
const shiftDate = (days) => {
  const dt = new Date(selectedDate.value);
  dt.setDate(dt.getDate() + days);
  selectedDate.value = dt.toISOString().slice(0, 10);
};

// ---- Add entry to selected date (API) ----
const addEntryForm = useForm("post", "/fdiary/entry", {
  date: selectedDate.value,
  food_id: null,
  meal_type: "other",
  quantity: 1,
});

watch(selectedDate, (d) => {
  addEntryForm.date = d;
});

const addSelectedFoodToSelectedDate = async () => {
  if (!selectedFood.value?.id) return;

  addEntryForm.food_id = selectedFood.value.id;
  addEntryForm.date = selectedDate.value;

  try {
    await addEntryForm.submit({
      // Precognition form submits via inertia/axios internally; keep as-is.
      // But endpoint returns JSON. That's OK; still works for submit, but
      // easiest is to use fetch for pure JSON. If submit causes issues,
      // tell me and we switch this to fetch with CSRF.
    });

    responseMessage.value = `Added: ${selectedFood.value.name} (${addEntryForm.meal_type}) x${addEntryForm.quantity} on ${selectedDate.value}`;
    showResponseDialog.value = true;

    await loadDiary(selectedDate.value);
  } catch (e) {
    responseMessage.value = "Could not add entry.";
    showResponseDialog.value = true;
  }
};

// ---- Delete entry by pivot id (API) ----
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

    await loadDiary(selectedDate.value);
  } catch (e) {
    responseMessage.value = e?.message ?? "Failed to delete entry.";
    showResponseDialog.value = true;
  }
};

// ---- Create food form ----
const showSuccessDialog = ref(false);

const createFoodForm = useForm("post", "/fdiary/create", {
  name: "",
  fat_g: 0,
  carbs_g: 0,
  protein_g: 0,
  calories: 0,
  notes: "",
  image: null,
});

const onSelectImage = (event) => {
  createFoodForm.image = event.files?.[0] ?? null;
};

const onRemoveImage = () => {
  createFoodForm.image = null;
};

const onCreateFood = () =>
  createFoodForm
    .submit({ forceFormData: true })
    .then(() => {
      createFoodForm.reset();
      onRemoveImage();
      showSuccessDialog.value = true;
      router.reload({ only: ["foods"] });
    })
    .catch(() => {});
</script>

<template>
  <div class="min-h-screen">
    <Navbar />

    <main class="mx-auto w-full max-w-7xl px-4 py-6">
      <!-- DATE NAV ROW (calendar + arrows) -->
      <section class="mb-6 rounded-2xl border p-5">
        <h2 class="text-lg font-semibold">Food diary date</h2>

        <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
          <div class="flex items-center gap-2">
            <Button
              label="←"
              severity="secondary"
              type="button"
              @click="shiftDate(-1)"
              :disabled="loadingDiary"
            />
            <div class="space-y-1">
              <input
                v-model="selectedDate"
                type="date"
                class="w-full rounded-lg border px-3 py-2 text-sm"
                :disabled="loadingDiary"
              />
            </div>
            <Button
              label="→"
              severity="secondary"
              type="button"
              @click="shiftDate(1)"
              :disabled="loadingDiary"
            />
          </div>

          <div class="text-sm">
            <span v-if="loadingDiary">Loading diary...</span>
            <span v-else>
              Entries: <b>{{ entries.length }}</b>
            </span>
          </div>
        </div>
      </section>

      <!-- EXISTING 3-COLUMN GRID -->
      <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- SEARCH -->
        <section class="rounded-2xl border p-5">
          <h2 class="text-lg font-semibold">Keresés</h2>
          <p class="mt-1 text-sm">Keress név alapján.</p>

          <div class="mt-4 flex gap-2">
            <input
              v-model="search"
              type="text"
              placeholder="Keress ételt..."
              class="w-full rounded-lg border px-3 py-2 text-sm outline-none"
            />
            <button type="button" class="rounded-lg border px-3 py-2 text-sm">
              🔍
            </button>
          </div>

          <div class="mt-4">
            <ul v-if="paginatedFoods.length" class="space-y-2">
              <li
                v-for="food in paginatedFoods"
                :key="food.id ?? food.name"
                @click="selectFood(food)"
                class="cursor-pointer rounded-xl border p-3"
              >
                <div class="flex items-start justify-between gap-3">
                  <div class="min-w-0">
                    <div class="truncate font-semibold">{{ food.name }}</div>
                    <div class="mt-1 text-xs">
                      {{ food.calories }} kcal · Zsír {{ food.fat_g }} g ·
                      Fehérje {{ food.protein_g }} g
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
              <Paginator
                v-if="filteredFoods.length > rows"
                :first="first"
                :rows="rows"
                :totalRecords="filteredFoods.length"
                :pageLinkSize="4"
                @page="(e) => (first = e.first)"
              />
            </div>
          </div>
        </section>

        <!-- SELECTED FOOD + ADD TO SELECTED DATE -->
        <section class="rounded-2xl border p-5">
          <h2 class="text-lg font-semibold">Kiválasztott étel</h2>
          <p class="mt-1 text-sm">Add hozzá a kiválasztott dátumhoz.</p>

          <div v-if="selectedFood" class="mt-4 rounded-xl border p-4">
            <div class="text-xl font-semibold">{{ selectedFood.name }}</div>

            <img
              v-if="selectedFood.image_path"
              :src="imageSrc(selectedFood)"
              class="mt-4 h-44 w-full rounded-xl border object-cover"
              alt="Food image"
            />

            <div class="mt-4 space-y-3">
              <div class="space-y-1">
                <label class="text-xs font-medium">Meal type</label>
                <select
                  v-model="addEntryForm.meal_type"
                  class="w-full rounded-lg border px-3 py-2 text-sm"
                >
                  <option value="breakfast">breakfast</option>
                  <option value="lunch">lunch</option>
                  <option value="dinner">dinner</option>
                  <option value="snack">snack</option>
                  <option value="other">other</option>
                </select>
              </div>

              <div class="space-y-1">
                <label class="text-xs font-medium">Quantity</label>
                <input
                  v-model.number="addEntryForm.quantity"
                  type="number"
                  min="1"
                  step="1"
                  class="w-full rounded-lg border px-3 py-2 text-sm"
                />
              </div>

              <button
                type="button"
                class="w-full rounded-lg border px-3 py-2 text-sm font-medium"
                :disabled="addEntryForm.processing"
                @click="addSelectedFoodToSelectedDate"
              >
                Add to {{ selectedDate }}
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

          <form class="mt-5 space-y-4" @submit.prevent="onCreateFood" novalidate>
            <div class="space-y-1">
              <FloatLabel variant="on">
                <InputText
                  id="food_name"
                  v-model="createFoodForm.name"
                  class="w-full"
                  @change="createFoodForm.validate('name')"
                />
                <label for="food_name">Név</label>
              </FloatLabel>

              <small v-if="createFoodForm.invalid('name')" class="block text-xs">
                {{ createFoodForm.errors.name }}
              </small>
            </div>

            <div class="space-y-2">
              <div class="text-sm font-medium">Kép (opcionális)</div>

              <FileUpload
                mode="basic"
                name="image"
                accept="image/*"
                :maxFileSize="4_000_000"
                chooseLabel="Kép kiválasztása"
                customUpload
                @select="onSelectImage"
                @clear="onRemoveImage"
                class="w-full"
              />
            </div>

            <Button
              type="submit"
              label="Étel mentése"
              class="w-full"
              :disabled="createFoodForm.processing"
            />
          </form>
        </section>
      </div>

      <Divider class="my-8" />

      <!-- DIARY LIST FOR SELECTED DATE -->
      <section class="rounded-2xl border p-5">
        <h2 class="text-lg font-semibold">Diary: {{ selectedDate }}</h2>

        <div v-if="entries.length" class="mt-4 space-y-3">
          <div class="rounded-xl border p-4 text-sm">
            <div class="font-semibold">Totals</div>
            <div class="mt-2 grid grid-cols-2 gap-2 sm:grid-cols-4">
              <div class="rounded-lg border p-2">
                <div class="text-xs">Calories</div>
                <div class="font-semibold">{{ totals.calories }} kcal</div>
              </div>
              <div class="rounded-lg border p-2">
                <div class="text-xs">Fat</div>
                <div class="font-semibold">{{ totals.fat_g }} g</div>
              </div>
              <div class="rounded-lg border p-2">
                <div class="text-xs">Carbs</div>
                <div class="font-semibold">{{ totals.carbs_g }} g</div>
              </div>
              <div class="rounded-lg border p-2">
                <div class="text-xs">Protein</div>
                <div class="font-semibold">{{ totals.protein_g }} g</div>
              </div>
            </div>
          </div>

          <ul class="space-y-2">
            <li
              v-for="food in entries"
              :key="food.pivot?.id ?? `${food.id}-${food.pivot?.created_at}`"
              class="rounded-xl border p-3"
            >
              <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                  <div class="truncate font-semibold">{{ food.name }}</div>
                  <div class="mt-1 text-xs">
                    {{ food.calories }} kcal · qty {{ food.pivot?.quantity ?? 1 }}
                    · meal {{ food.pivot?.meal_type ?? "other" }}
                  </div>
                </div>

                <div class="flex items-center gap-2">
                  <Button
                    label="Delete"
                    severity="danger"
                    size="small"
                    type="button"
                    @click="deleteEntry(food.pivot?.id)"
                  />
                </div>
              </div>
            </li>
          </ul>
        </div>

        <div v-else class="mt-4 rounded-xl border border-dashed p-6 text-sm">
          No entries for this date.
        </div>
      </section>

      <Dialog
        v-model:visible="showSuccessDialog"
        modal
        :closable="true"
        :draggable="false"
        header="Siker"
        class="w-[92vw] max-w-md"
      >
        <p>Az étel sikeresen mentve lett.</p>

        <template #footer>
          <div class="flex w-full justify-end gap-2">
            <Button
              label="Close"
              severity="secondary"
              @click="showSuccessDialog = false"
            />
          </div>
        </template>
      </Dialog>

      <Dialog
        v-model:visible="showResponseDialog"
        modal
        :closable="true"
        :draggable="false"
        header="Diary"
        class="w-[92vw] max-w-md"
      >
        <p>{{ responseMessage }}</p>

        <template #footer>
          <div class="flex w-full justify-end gap-2">
            <Button
              label="Close"
              severity="secondary"
              @click="showResponseDialog = false"
            />
          </div>
        </template>
      </Dialog>
    </main>

    <Footer />
  </div>
</template>

<style scoped></style>