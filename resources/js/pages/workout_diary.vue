<script setup>
import { computed, ref, watch } from "vue";
import { router, usePage } from "@inertiajs/vue3";

import Navbar from "../components/navbar.vue";
import Footer from "../components/footer.vue";

import Paginator from "primevue/paginator";
import Divider from "primevue/divider";

import FloatLabel from "primevue/floatlabel";
import InputText from "primevue/inputtext";
import Button from "primevue/button";
import Dialog from "primevue/dialog";

import { useForm } from "laravel-precognition-vue";

const page = usePage();

// expects: props.exercises from controller
const exercises = ref(page.props.exercises ?? []);

const search = ref("");
const first = ref(0);
const rows = ref(5);

const selectedExercise = ref(null);

// ---- Search / list ----
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
};

const showSuccessDialog = ref(false);

const createExerciseForm = useForm("post", "/wdiary/create", {
  name: "",
  quantity: "",
  burned_calories: 0,
  note: "",
});

const onCreateExercise = () =>
  createExerciseForm
    .submit()
    .then(() => {
      createExerciseForm.reset();
      showSuccessDialog.value = true;

      // if controller redirects back with new props
      router.reload({ only: ["exercises"] });
    })
    .catch(() => {});

function closeSuccessDialog() {
  showSuccessDialog.value = false;
}
</script>

<template>
  <div class="min-h-screen">
    <Navbar />

    <main class="mx-auto w-full max-w-7xl px-4 py-6">
      <!-- 3 columns like Food Diary -->
      <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- LEFT: Create -->
        <section class="rounded-2xl border p-5">
          <h2 class="text-lg font-semibold">Új edzés hozzáadása</h2>
          <p class="mt-1 text-sm">
            Add meg a mennyiséget, kalóriát és opcionális megjegyzést.
          </p>

          <form
            class="mt-5 space-y-4"
            @submit.prevent="onCreateExercise"
            novalidate
          >
            <div class="space-y-1">
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

            <div class="space-y-1">
              <FloatLabel variant="on">
                <InputText
                  id="quantity"
                  v-model="createExerciseForm.quantity"
                  class="w-full"
                  @change="createExerciseForm.validate('quantity')"
                />
                <label for="quantity">Mennyiség (pl. 30 perc, 3x12)</label>
              </FloatLabel>

              <small
                v-if="createExerciseForm.invalid('quantity')"
                class="block text-xs"
              >
                {{ createExerciseForm.errors.quantity }}
              </small>
            </div>

            <div class="space-y-1">
              <FloatLabel variant="on">
                <InputText
                  id="burned_calories"
                  v-model="createExerciseForm.burned_calories"
                  type="number"
                  inputmode="numeric"
                  class="w-full"
                  @change="createExerciseForm.validate('burned_calories')"
                />
                <label for="burned_calories">Elégetett kalória</label>
              </FloatLabel>

              <small
                v-if="createExerciseForm.invalid('burned_calories')"
                class="block text-xs"
              >
                {{ createExerciseForm.errors.burned_calories }}
              </small>
            </div>

            <div class="space-y-1">
              <FloatLabel variant="on">
                <InputText
                  id="note"
                  v-model="createExerciseForm.note"
                  class="w-full"
                  @change="createExerciseForm.validate('note')"
                />
                <label for="note">Megjegyzés (opcionális)</label>
              </FloatLabel>

              <small
                v-if="createExerciseForm.invalid('note')"
                class="block text-xs"
              >
                {{ createExerciseForm.errors.note }}
              </small>
            </div>

            <Button
              type="submit"
              label="Edzés mentése"
              class="w-full"
              :disabled="createExerciseForm.processing"
            />
          </form>
        </section>

        <!-- MIDDLE: Search + list -->
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
            <button type="button" class="rounded-lg border px-3 py-2 text-sm">
              🔍
            </button>
          </div>

          <div class="mt-4">
            <ul v-if="paginatedExercises.length" class="space-y-2">
              <li
                v-for="exercise in paginatedExercises"
                :key="exercise.id ?? exercise.name"
                @click="selectExercise(exercise)"
                class="cursor-pointer rounded-xl border p-3"
              >
                <div class="flex items-start justify-between gap-3">
                  <div class="min-w-0">
                    <div class="truncate font-semibold">
                      {{ exercise.name }}
                    </div>
                    <div class="mt-1 text-xs">
                      {{ exercise.quantity }} · {{ exercise.burned_calories }} kcal
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

        <!-- RIGHT: Selected -->
        <section class="rounded-2xl border p-5">
          <h2 class="text-lg font-semibold">Kiválasztott edzés</h2>
          <p class="mt-1 text-sm">Kattints egy edzésre középen.</p>

          <div v-if="selectedExercise" class="mt-4 rounded-xl border p-4">
            <div class="text-xl font-semibold">
              {{ selectedExercise.name }}
            </div>
            <div class="mt-2 text-sm">
              <div><strong>Mennyiség:</strong> {{ selectedExercise.quantity }}</div>
              <div class="mt-1">
                <strong>Elégetett kalória:</strong>
                {{ selectedExercise.burned_calories }} kcal
              </div>
            </div>

            <div
              v-if="selectedExercise.note"
              class="mt-4 rounded-lg border p-3 text-sm whitespace-pre-wrap"
            >
              {{ selectedExercise.note }}
            </div>
          </div>

          <div v-else class="mt-4 rounded-xl border border-dashed p-6 text-sm">
            Nincs kiválasztott edzés.
          </div>
        </section>
      </div>

      <Divider class="my-8" />

      <Dialog
        v-model:visible="showSuccessDialog"
        modal
        :closable="true"
        :draggable="false"
        header="Siker"
        class="w-[92vw] max-w-md"
        @hide="closeSuccessDialog"
      >
        <p>Az edzés sikeresen mentve lett.</p>

        <template #footer>
          <div class="flex w-full justify-end gap-2">
            <Button
              label="Close"
              severity="secondary"
              @click="closeSuccessDialog"
            />
          </div>
        </template>
      </Dialog>
    </main>

    <Footer />
  </div>
</template>

<style scoped></style>