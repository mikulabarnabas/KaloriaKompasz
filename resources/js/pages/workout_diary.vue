<script setup>
import { computed, ref, watch } from "vue";
import { usePage } from "@inertiajs/vue3";
import axios from "axios";

import AppLayout from "@/Layouts/AppLayout.vue"

import DateNavigator from "../components/datenavigator.vue";

import { useForm } from "laravel-precognition-vue";

import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const UNIT_TO_BASE = {
  minutes: 1,
  hours: 60,
  m: 1,
  km: 1000
};


defineOptions({ layout: AppLayout })

const pageCount = ref(0);
let searchedExercises = ref([]);

async function searchExercise(page) {
  const { data } = await axios.get(`/wdiary/getExercises/${search.value}/${page}`)
  searchedExercises.value = data.result
}

async function getExercisePageCount() {
  const { data } = await axios.get(`/wdiary/getPageCount/${search.value}`)
  pageCount.value = data.pageCount
}


const selectedDate = ref(new Date())
const formattedDate = computed(() =>
  selectedDate.value.toISOString().slice(0, 10)
)

const search = ref("")
const rows = 5

const selectedExercise = ref(null)
const loadingDiary = ref(false)

const addEntryForm = useForm("post", "/wdiary/entry", {
  date: selectedDate.value,
  exercise_id: null,
  unit: null,
  amount: 0
})


watch(search, () => {
  if (search.value === "") return
  getExercisePageCount()
  searchExercise(1)
}, { immediate: true })


async function loadDiary(date) {
  addEntryForm.date = date
  loadingDiary.value = true
  const { data } = await axios.get(`/wdiary/diary/${date}`)
  entries.value = data.diary?.exercises ?? [];
  loadingDiary.value = false
}

watch(formattedDate, (d) => {
  loadDiary(d);
}, { immediate: true });

const entries = ref([])

const unitOptions = [
  { label: t('workoutDiary.minute'), value: "minutes" },
  { label: t('workoutDiary.hour'), value: "hours" },
  { label: t('workoutDiary.km'), value: "km" },
  { label: t('workoutDiary.m'), value: "m" },
]

const selectedBurnedCalories = computed(() => {
  const amount = Number(addEntryForm.amount);

  const exerciseUnitFactor = UNIT_TO_BASE[selectedExercise.value.unit];
  const entryUnitFactor = UNIT_TO_BASE[addEntryForm.unit];

  const factor = entryUnitFactor / exerciseUnitFactor;

  const perUnit = Number(selectedExercise.value.calories_per_unit);

  return Math.round(perUnit * factor * amount * 100) / 100;
});


const selectExercise = (e) => {
  selectedExercise.value = e
  addEntryForm.exercise_id = e?.id ?? null
}

const addSelectedExercise = async () => {
  if (!selectedExercise.value) return

  await addEntryForm.submit({ only: [] })
  await loadDiary(formattedDate.value)
}

/* -------------------- DELETE -------------------- */

const confirm = useConfirm()

const deleteEntry = (id) => {
  confirm.require({
    group: "headless",
    accept: async () => {
      await axios.delete(`/wdiary/entry/${formattedDate.value}/${id}`)
      await loadDiary(formattedDate.value)
    }
  })
}

/* -------------------- CREATE EXERCISE -------------------- */

const allowedUnits = computed(() => {
  if (!selectedExercise.value) return [];

  const timeUnits = ['minutes', 'hours'];
  const distanceUnits = ['km', 'm'];

  if (timeUnits.includes(selectedExercise.value.unit)) {
    return unitOptions.filter(u => timeUnits.includes(u.value));
  }

  if (distanceUnits.includes(selectedExercise.value.unit)) {
    return unitOptions.filter(u => distanceUnits.includes(u.value));
  }

  return unitOptions;
});


const createExerciseForm = useForm("post", "/wdiary/create", {
  name: "",
  unit: "other",
  calories_per_unit: 0,
  note: ""
})

const onCreateExercise = async () => {
  await createExerciseForm.submit()
  createExerciseForm.reset()
}
</script>

<template>
  <main class="mx-auto w-full max-w-7xl px-4 py-6">

    <!-- DATE NAV -->
    <section class="mb-6 rounded-2xl border p-5">
      <h2 class="text-lg font-semibold">
        {{ $t('workoutDiary.date') }}
      </h2>

      <DateNavigator v-model="selectedDate"/>
    </section>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 mb-6">

      <!-- SEARCH -->
      <section class="rounded-2xl border p-5">
        <h2 class="text-lg font-semibold">
          {{ $t('workoutDiary.search_title') }}
        </h2>

        <InputText class="mt-4 flex gap-2 w-full" v-model="search" type="text"
          :placeholder="$t('workoutDiary.search_placeholder')" />

        <div class="mt-4">

          <ul class="space-y-2">
            <li v-for="exercise in searchedExercises" :key="exercise.id" class="cursor-pointer rounded-xl border p-3"
              @click="selectExercise(exercise)">
              <div class="flex items-start justify-between gap-3">

                <div class="min-w-0">
                  <div class="truncate font-semibold">
                    {{ exercise.name }}
                  </div>

                  <div class="mt-1 text-xs">
                    {{ exercise.calories_per_unit }}
                    {{ $t('workoutDiary.calorie_label') }}
                    / {{ exercise.unit }}
                  </div>
                </div>

                <div class="shrink-0 rounded-full border px-2 py-1 text-xs">
                  #{{ exercise.id }}
                </div>

              </div>
            </li>
          </ul>

          <Paginator v-if="searchedExercises.length" class="mt-8" :rows="rows" :totalRecords="pageCount"
            @page="(e) => searchExercise(e.page + 1)">
            <template #container="{ first, last, page, pageCount, prevPageCallback, nextPageCallback, totalRecords }">
              <div
                class="flex items-center border border-primary bg-transparent rounded-full w-full py-1 px-2 justify-between">
                <Button icon="pi pi-chevron-left" rounded variant="text" @click="prevPageCallback"
                  :disabled="page === 0" />

                <div class="text-color font-medium">
                  <span class="hidden sm:block">
                    {{ $t('workoutDiary.paginator_visible_range',
                      { first: first, last: last, total: totalRecords }) }}
                  </span>

                  <span class="block sm:hidden">
                    {{ $t('workoutDiary.paginator_page_of',
                      { page: page + 1, pageCount: pageCount }) }}
                  </span>
                </div>

                <Button icon="pi pi-chevron-right" rounded variant="text" @click="nextPageCallback"
                  :disabled="page === pageCount - 1" />
              </div>
            </template>
          </Paginator>

        </div>
      </section>


      <!-- SELECTED -->
      <section class="rounded-2xl border p-5">
        <h2 class="text-lg font-semibold">
          {{ $t('workoutDiary.selected_exercise_title') }}
        </h2>

        <p class="mt-1 text-sm">
          {{ $t('workoutDiary.selected_exercise_subtitle') }}
        </p>

        <div v-if="selectedExercise" class="mt-4 space-y-3 rounded-xl border p-4">

          <div class="text-xl font-semibold">{{ selectedExercise.name }}</div>

          <div>{{ selectedExercise.note }}</div>

          <div class="grid grid-cols-2 gap-3">
            <!-- amount -->
            <div>
              <FloatLabel variant="on">
                <InputText v-model="addEntryForm.amount" class="w-full" />
                <label>{{ $t('workoutDiary.time_label') }} / {{ $t('workoutDiary.distance_label') }}</label>
              </FloatLabel>
            </div>

            <div>
              <FloatLabel variant="on">
                <Select v-model="addEntryForm.unit" :options="allowedUnits" optionLabel="label" class="w-full"
                  optionValue="value" />
                <label>{{ $t('workoutDiary.unit_label') }}</label>
              </FloatLabel>
            </div>

          </div>
                      <div v-if="addEntryForm.unit && addEntryForm.amount"">
              {{ $t('workoutDiary.burned_label') }}: {{ selectedBurnedCalories }} kcal
            </div>

          <Button class=" w-full" :disabled="addEntryForm.processing" @click="addSelectedExercise">
              {{ $t('workoutDiary.add_button') }} {{ formattedDate }}
              </Button>

            </div>

            <div v-else class="mt-4 border-dashed border rounded-xl p-6 text-sm">
              {{ $t('workoutDiary.no_selected_exercise') }}
            </div>

      </section>

      <!-- CREATE -->
      <section class="rounded-2xl border p-5">
        <h2 class="text-lg font-semibold">
          {{ $t('workoutDiary.create_exercise_title') }}
        </h2>

        <form class="mt-4 space-y-3" @submit.prevent="onCreateExercise">

          <FloatLabel variant="on">
            <InputText v-model="createExerciseForm.name" class="w-full" />
            <label>{{ $t('workoutDiary.exercise_name_label') }}</label>
          </FloatLabel>

          <div class="grid grid-cols-2 gap-3">
            <!-- amount -->
            <div>
              <FloatLabel variant="on">
                <InputText v-model.number="createExerciseForm.calories_per_unit" class="w-full" />
                <label>{{ $t('workoutDiary.calorie_label') }}</label>
              </FloatLabel>
            </div>

            <div>
              <FloatLabel variant="on">
                <Select v-model="createExerciseForm.unit" :options="unitOptions" optionLabel="label" class="w-full"
                  optionValue="value" />
                <label>{{ $t('workoutDiary.unit_label') }}</label>
              </FloatLabel>
            </div>
          </div>

          <FloatLabel variant="on">
            <InputText v-model="createExerciseForm.note" class="w-full" @change="createExerciseForm.validate('note')" />
            <label for="food_note">{{ $t('foodDiary.note_label') }}</label>
          </FloatLabel>

          <Button type="submit" class="w-full">
            {{ $t('workoutDiary.save_exercise') }}
          </Button>

        </form>
      </section>

    </div>

    <!-- DIARY -->
    <section class="rounded-2xl border p-5">
      <h2 class="text-lg font-semibold">
        {{ $t('workoutDiary.diary_title') }} — {{ formattedDate }}
      </h2>

      <div v-if="entries.length" class="mt-4 space-y-4">

        <ul class="space-y-2">
          <li v-for="exercise in entries" :key="exercise.id" class="rounded-xl border p-3">

            <div class="flex items-center gap-4">

              <!-- CENTER -->
              <div class="flex-1 min-w-0">
                <div class="font-semibold truncate">
                  {{ exercise.name }}
                </div>
                <div class="text-sm opacity-80">
                  {{ exercise.pivot.amount }} {{ exercise.pivot.unit }}
                </div>
              </div>

              <div class="hidden sm:flex flex-col text-lg text-right leading-tight">
                <div>{{ exercise.pivot.burned_calories }} kcal</div>
              </div>

              <!-- DELETE -->
              <Button icon="pi pi-trash" severity="danger" text @click="deleteEntry(exercise.pivot.id)" />

            </div>

          </li>
        </ul>

      </div>

      <div v-else class="mt-4 border-dashed border rounded-xl p-6 text-sm">
        {{ $t('workoutDiary.no_entries') }}
      </div>

    </section>

    <ConfirmPopup group="headless">
      <template #container="{ acceptCallback, rejectCallback }">
        <div class="rounded p-4">
          <span>{{ $t('workoutDiary.delete_confirmation') }}</span>
          <div class="flex gap-2 mt-4">
            <Button :label="$t('workoutDiary.delete')" severity="danger" size="small" @click="acceptCallback" />
            <Button :label="$t('workoutDiary.dialog_close')" size="small" text @click="rejectCallback" />
          </div>
        </div>
      </template>
    </ConfirmPopup>

  </main>
</template>
