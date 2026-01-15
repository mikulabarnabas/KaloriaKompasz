<script setup>
import { computed, ref, watch } from "vue";
import { router, usePage } from "@inertiajs/vue3";

import Navbar from "../components/navbar.vue";
import Footer from "../components/footer.vue";

import Paginator from "primevue/paginator";
import FloatLabel from "primevue/floatlabel";
import InputText from "primevue/inputtext";
import Button from "primevue/button";
import Dialog from "primevue/dialog";
import Select from "primevue/select";

import { useForm } from "laravel-precognition-vue";

const page = usePage();

const exercises = ref(page.props.exercises ?? []);
const selectedDate = ref(
  page.props.selectedDate ?? new Date().toISOString().slice(0, 10)
);
const selectedDiary = ref(page.props.selectedDiary ?? null);

const search = ref("");
const first = ref(0);
const rows = ref(5);
const selectedExercise = ref(null);

const showResponseDialog = ref(false);
const responseMessage = ref("");

const showSuccessDialog = ref(false);

const filteredExercises = computed(() => {
  const q = search.value.trim().toLowerCase();
  if (!q) return [];
  return exercises.value.filter((e) =>
    (e.name ?? "").toLowerCase().includes(q)
  );
});

const paginatedExercises = computed(() =>
  filteredExercises.value.slice(first.value, first.value + rows.value)
);

watch(search, () => {
  first.value = 0;
  selectedExercise.value = null;
});

const selectExercise = (exercise) => {
  selectedExercise.value = exercise;
  addEntryForm.exercise_id = exercise?.id ?? null;
};

const entries = computed(() => selectedDiary.value?.exercises ?? []);

const totals = computed(() => {
  const list = entries.value;

  return {
    burned_calories: list.reduce(
      (s, e) =>
        s +
        Number(e.pivot?.burned_calories ?? 0) *
          1,
      0
    ),
    quantity: list.reduce((s, e) => s + Number(e.pivot?.quantity ?? 1), 0),
  };
});

const loadingDiary = ref(false);

const loadDiary = async (date) => {
  loadingDiary.value = true;

  try {
    const url = `/wdiary/diary?date=${encodeURIComponent(date)}`;
    const res = await fetch(url, {
      headers: { Accept: "application/json" },
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
  router.get(
    "/wdiary",
    { date: d },
    { preserveState: true, replace: true, only: ["selectedDate", "selectedDiary"] }
  );

  loadDiary(d);
});

const shiftDate = (days) => {
  const dt = new Date(selectedDate.value);
  dt.setDate(dt.getDate() + days);
  selectedDate.value = dt.toISOString().slice(0, 10);
};

const addEntryForm = useForm("post", "/wdiary/entry", {
  date: selectedDate.value,
  exercise_id: null,
  quantity: 1,
  burned_calories: 0,
  note: "",
});

watch(selectedDate, (d) => {
  addEntryForm.date = d;
});

const addSelectedExerciseToSelectedDate = async () => {
  if (!selectedExercise.value?.id) return;

  addEntryForm.exercise_id = selectedExercise.value.id;
  addEntryForm.date = selectedDate.value;

  try {
    await addEntryForm.submit();

    responseMessage.value = `Added: ${selectedExercise.value.name} x${addEntryForm.quantity} on ${selectedDate.value}`;
    showResponseDialog.value = true;

    await loadDiary(selectedDate.value);
  } catch (e) {
    responseMessage.value =
      addEntryForm.errors?.exercise_id ||
      addEntryForm.errors?.date ||
      addEntryForm.errors?.quantity ||
      addEntryForm.errors?.burned_calories ||
      addEntryForm.errors?.note ||
      "Could not add entry.";
    showResponseDialog.value = true;
  }
};

const deleteEntry = async (entryId) => {
  if (!entryId) return;

  try {
    const url = `/wdiary/entry?entry_id=${encodeURIComponent(entryId)}`;
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

const createExerciseForm = useForm("post", "/wdiary/create", {
  name: "",
  unit: "other",
  calories_per_unit: 0,
  note: "",
});

const unitOptions = [
  { label: "Reps", value: "reps" },
  { label: "Sets", value: "sets" },
  { label: "Minutes", value: "minutes" },
  { label: "Seconds", value: "seconds" },
  { label: "Kilometers", value: "km" },
  { label: "Meters", value: "m" },
  { label: "Other", value: "other" },
];

const onCreateExercise = async () => {
  try {
    await createExerciseForm.submit();

    createExerciseForm.reset();
    showSuccessDialog.value = true;

    router.reload({ only: ["exercises"] });
  } catch (e) {
    responseMessage.value =
      createExerciseForm.errors?.name ||
      createExerciseForm.errors?.unit ||
      createExerciseForm.errors?.calories_per_unit ||
      createExerciseForm.errors?.note ||
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
        <h2 class="text-lg font-semibold">Workout diary date</h2>

        <div
          class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between"
        >
          <div class="flex items-center gap-2">
            <Button
              label="←"
              severity="secondary"
              type="button"
              :disabled="loadingDiary"
              @click="shiftDate(-1)"
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
              :disabled="loadingDiary"
              @click="shiftDate(1)"
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

      <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- SEARCH -->
        <section class="rounded-2xl border p-5">
          <h2 class="text-lg font-semibold">Keresés</h2>
          <p class="mt-1 text-sm">Keress edzést név alapján.</p>

          <div class="mt-4 flex gap-2">
            <input
              v-model="search"
              type="text"
              placeholder="Keress edzést..."
              class="w-full rounded-lg border px-3 py-2 text-sm outline-none"
            />
          </div>

          <div class="mt-4">
            <ul v-if="paginatedExercises.length" class="space-y-2">
              <li
                v-for="exercise in paginatedExercises"
                :key="exercise.id ?? exercise.name"
                class="cursor-pointer rounded-xl border p-3"
                @click="selectExercise(exercise)"
              >
                <div class="flex items-start justify-between gap-3">
                  <div class="min-w-0">
                    <div class="truncate font-semibold">
                      {{ exercise.name }}
                    </div>
                    <div class="mt-1 text-xs">
                      Unit: {{ exercise.unit }} · {{ exercise.calories_per_unit }}
                      kcal/unit
                    </div>
                  </div>

                  <div class="shrink-0 rounded-full border px-2 py-1 text-xs">
                    #{{ exercise.id }}
                  </div>
                </div>
              </li>
            </ul>

            <div v-else class="rounded-xl border border-dashed p-6 text-sm">
              Írj be valamit a kereséshez.
            </div>

            <div class="mt-4">
              <Paginator
                v-if="filteredExercises.length > rows"
                :first="first"
                :rows="rows"
                :totalRecords="filteredExercises.length"
                :pageLinkSize="4"
                @page="(e) => (first = e.first)"
              />
            </div>
          </div>
        </section>

        <!-- SELECTED EXERCISE -->
        <section class="rounded-2xl border p-5">
          <h2 class="text-lg font-semibold">Kiválasztott edzés</h2>
          <p class="mt-1 text-sm">Add hozzá a kiválasztott dátumhoz.</p>

          <div v-if="selectedExercise" class="mt-4 rounded-xl border p-4">
            <div class="text-xl font-semibold">
              {{ selectedExercise.name }}
            </div>

            <div class="mt-4 space-y-3">
              <div class="text-sm">
                Unit: <b>{{ selectedExercise.unit }}</b> · Calories/unit:
                <b>{{ selectedExercise.calories_per_unit }}</b>
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

              <div class="space-y-1">
                <label class="text-xs font-medium">
                  Burned calories (optional override)
                </label>
                <input
                  v-model.number="addEntryForm.burned_calories"
                  type="number"
                  min="0"
                  step="1"
                  class="w-full rounded-lg border px-3 py-2 text-sm"
                />
                <div class="text-xs text-gray-500">
                  If 0, you can calculate server-side from calories/unit ×
                  quantity.
                </div>
              </div>

              <div class="space-y-1">
                <label class="text-xs font-medium">Note (optional)</label>
                <input
                  v-model="addEntryForm.note"
                  type="text"
                  class="w-full rounded-lg border px-3 py-2 text-sm"
                />
              </div>

              <button
                type="button"
                class="w-full rounded-lg border px-3 py-2 text-sm font-medium"
                :disabled="addEntryForm.processing"
                @click="addSelectedExerciseToSelectedDate"
              >
                Hozzáadás {{ selectedDate }}
              </button>
            </div>
          </div>

          <div v-else class="mt-4 rounded-xl border border-dashed p-6 text-sm">
            Nincs kiválasztott edzés.
          </div>
        </section>

        <!-- CREATE EXERCISE -->
        <section class="rounded-2xl border p-5">
          <h2 class="text-lg font-semibold">Új edzés típus</h2>

          <form class="mt-5 space-y-4" novalidate @submit.prevent="onCreateExercise">
            <div class="space-y-3">
              <div>
                <FloatLabel variant="on">
                  <InputText
                    id="ex_name"
                    v-model="createExerciseForm.name"
                    class="w-full"
                    @change="createExerciseForm.validate('name')"
                  />
                  <label for="ex_name">Név</label>
                </FloatLabel>
                <small
                  v-if="createExerciseForm.invalid('name')"
                  class="block text-xs"
                >
                  {{ createExerciseForm.errors.name }}
                </small>
              </div>

              <div>
                <FloatLabel variant="on">
                  <Select
                    inputId="ex_unit"
                    v-model="createExerciseForm.unit"
                    :options="unitOptions"
                    optionLabel="label"
                    optionValue="value"
                    class="w-full"
                    @change="createExerciseForm.validate('unit')"
                  />
                  <label for="ex_unit">Mértékegység</label>
                </FloatLabel>
                <small
                  v-if="createExerciseForm.invalid('unit')"
                  class="block text-xs"
                >
                  {{ createExerciseForm.errors.unit }}
                </small>
              </div>

              <div>
                <FloatLabel variant="on">
                  <InputText
                    id="ex_cpu"
                    v-model.number="createExerciseForm.calories_per_unit"
                    type="number"
                    inputmode="numeric"
                    class="w-full"
                    @change="createExerciseForm.validate('calories_per_unit')"
                  />
                  <label for="ex_cpu">Calories / unit</label>
                </FloatLabel>
                <small
                  v-if="createExerciseForm.invalid('calories_per_unit')"
                  class="block text-xs"
                >
                  {{ createExerciseForm.errors.calories_per_unit }}
                </small>
              </div>

              <div>
                <FloatLabel variant="on">
                  <InputText
                    id="ex_note"
                    v-model="createExerciseForm.note"
                    class="w-full"
                    @change="createExerciseForm.validate('note')"
                  />
                  <label for="ex_note">Megjegyzés</label>
                </FloatLabel>
                <small
                  v-if="createExerciseForm.invalid('note')"
                  class="block text-xs"
                >
                  {{ createExerciseForm.errors.note }}
                </small>
              </div>
            </div>

            <Button
              type="submit"
              label="Edzés mentése"
              class="w-full"
              :disabled="createExerciseForm.processing"
            />
          </form>
        </section>
      </div>

      <!-- DIARY -->
      <section class="rounded-2xl border p-5">
        <h2 class="text-lg font-semibold">Naptár: {{ selectedDate }}</h2>

        <div v-if="entries.length" class="mt-4 space-y-3">
          <div class="rounded-xl border p-4 text-sm">
            <div class="font-semibold">Totals</div>
            <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2">
              <div class="rounded-lg border p-2">
                <div class="text-xs">Burned calories</div>
                <div class="font-semibold">
                  {{ totals.burned_calories }} kcal
                </div>
              </div>
              <div class="rounded-lg border p-2">
                <div class="text-xs">Total quantity (sum)</div>
                <div class="font-semibold">{{ totals.quantity }}</div>
              </div>
            </div>
          </div>

          <ul class="space-y-2">
            <li
              v-for="exercise in entries"
              :key="exercise.pivot?.id ?? `${exercise.id}-${exercise.pivot?.created_at}`"
              class="rounded-xl border p-3"
            >
              <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                  <div class="truncate font-semibold">
                    {{ exercise.name }}
                  </div>
                  <div class="mt-1 text-xs">
                    qty {{ exercise.pivot?.quantity ?? 1 }}
                    · burned {{ exercise.pivot?.burned_calories ?? 0 }} kcal
                  </div>
                  <div
                    v-if="exercise.pivot?.note"
                    class="mt-2 text-xs whitespace-pre-wrap"
                  >
                    {{ exercise.pivot.note }}
                  </div>
                </div>

                <div class="flex items-center gap-2">
                  <Button
                    label="Törlés"
                    severity="danger"
                    size="small"
                    type="button"
                    @click="deleteEntry(exercise.pivot?.id)"
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

      <!-- SUCCESS DIALOG -->
      <Dialog
        v-model:visible="showSuccessDialog"
        modal
        :closable="true"
        :draggable="false"
        header="Siker"
        class="w-[92vw] max-w-md"
      >
        <p>Az edzés sikeresen mentve lett.</p>

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

      <!-- RESPONSE DIALOG -->
      <Dialog
        v-model:visible="showResponseDialog"
        modal
        :closable="true"
        :draggable="false"
        header="Workout diary"
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