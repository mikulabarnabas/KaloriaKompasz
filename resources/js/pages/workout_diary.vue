<script setup>
import { computed, ref, watch } from "vue";
import axios from "axios";
import { getActiveLanguage } from "laravel-vue-i18n";


import WorkoutSearch from "@/Components/workoutSearch.vue";
import AddWorkoutEntryOverlay from "@/Components/addWorkoutEntry.vue";
import AddExerciseOverlay from "@/Components/addWorkout.vue";
import DateNavigator from "@/Components/dateNavigator.vue";
import Button from "@/COmponents/button.vue";
import WorkoutCard from "@/Components/workoutCard.vue"

import { trans as t } from 'laravel-vue-i18n';
import AppLayout from "@/Layouts/AppLayout.vue";
defineOptions({ layout: AppLayout });

const selectedDate = ref(new Date());
const formattedDate = computed(() => selectedDate.value.toISOString().slice(0, 10));
const entries = ref([]);

const isEntryModalOpen = ref(false);
const isCreateModalOpen = ref(false);
const selectedExerciseForEntry = ref(null);

const totalBurned = computed(() => {
  return entries.value.reduce((total, exercise) => {
    return total + Number(exercise.pivot.burned_calories || 0);
  }, 0);
});

const totalDuration = computed(() => {
  return entries.value.reduce((total, exercise) => {
    if (exercise.pivot.unit == "hours") return total + Number(exercise.pivot.amount * 60)
    return total + Number(exercise.pivot.amount);
  }, 0);
});

const isLoading = ref(false);

const loadDiary = async () => {
  isLoading.value = true;
  const { data } = await axios.get(`/wdiary/diary/${formattedDate.value}`);
  entries.value = data.diary?.exercises ?? [];
  isLoading.value = false;
};

const onExerciseSelect = (exercise) => {
  selectedExerciseForEntry.value = exercise;
  isEntryModalOpen.value = true;
};

const openCreateExerciseModal = () => {
  isCreateModalOpen.value = true;
};

const onSaved = () => {
  loadDiary();
  isCreateModalOpen.value = false;
  isEntryModalOpen.value = false;
};

const deleteEntry = async (entryId) => {
  if (confirm(t('workoutDiary.delete_confirmation'))) {
    await axios.delete(`/wdiary/entry/${formattedDate.value}/${entryId}`);
    loadDiary();
  }
};

watch(formattedDate, loadDiary, { immediate: true });

const getDisplayName = (item) => {
  console.log(getActiveLanguage())
  if (getActiveLanguage() === 'hu' && item.name_hu) {
    return item.name_hu;
  }
  return item.name;
};
</script>

<template>
  <div class="bg-background-dark text-white relative min-h-screen">
    <main class="flex flex-col min-w-0">
      <header class="p-6 border-b border-neutral-border bg-background-dark/60">
        <div
          class="max-w-4xl mx-auto flex flex-wrap lg:flex-nowrap items-center justify-center lg:justify-between gap-4">
          <WorkoutSearch class="w-full lg:flex-1 order-1" :placeholder="$t('workoutDiary.search_placeholder')"
            @select="onExerciseSelect" />

          <div class="flex flex-wrap sm:flex-nowrap items-center justify-center gap-4 order-2 w-full lg:w-auto">
            <DateNavigator v-model="selectedDate" class="shrink-0" />
            <Button :label="$t('workoutDiary.create_exercise_title')" icon="add_circle" @click="openCreateExerciseModal"
              class="w-full sm:w-48 h-14! shrink-0"></Button>
          </div>
        </div>
      </header>

      <div class="relative p-6 space-y-10 pb-32 min-h-100">

        <div v-if="isLoading"
          class="absolute inset-0 z-50 flex flex-col items-center pt-20 bg-background-dark/10 backdrop-blur-[2px] transition-all duration-300">
          <div class="w-16 h-16 border-4 border-primary/10 border-t-primary rounded-full animate-spin"></div>
          <p class="mt-4 text-[10px] font-black uppercase tracking-[0.5em] text-primary">{{ $t('workoutDiary.update') }}</p>
        </div>

        <div
          :class="['max-w-4xl mx-auto space-y-10 transition-all duration-500', isLoading ? 'opacity-30 blur-md pointer-events-none' : 'opacity-100 blur-0']">

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div
              class="relative overflow-hidden bg-neutral-dark/30 p-8 rounded-4xl border border-neutral-border shadow-2xl group">
              <h3 class="text-secondary-text text-[10px] font-black uppercase tracking-[0.2em] mb-2">{{
                $t('workoutDiary.burned_label') }}</h3>
              <div class="relative z-10 flex items-baseline gap-2">
                <span class="text-5xl font-black text-primary tracking-tighter transition-transform duration-500">{{
                  totalBurned }}</span>
                <span class="text-primary/40 font-bold uppercase tracking-widest text-sm">kcal</span>
              </div>
              <div class="absolute -right-4 -bottom-4 size-24 bg-primary/5 blur-3xl rounded-full"></div>
            </div>

            <div
              class="relative overflow-hidden bg-neutral-dark/30 p-8 rounded-4xl border border-neutral-border shadow-2xl group">
              <h3 class="text-secondary-text text-[10px] font-black uppercase tracking-[0.2em] mb-2">{{
                $t('workoutDiary.duration_label') }}</h3>
              <div class="relative z-10 flex items-baseline gap-2">
                <span class="text-5xl font-black text-blue-400 tracking-tighter transition-transform duration-500">{{
                  totalDuration }}</span>
                <span class="text-blue-400/40 font-bold uppercase tracking-widest text-sm">min</span>
              </div>
              <div class="absolute -right-4 -bottom-4 size-24 bg-blue-500/5 blur-3xl rounded-full"></div>
            </div>
          </div>

          <div class="space-y-6">
            <div class="flex items-center justify-between px-2">
              <h3 class="text-sm font-black text-main-text uppercase tracking-[0.3em]">{{ $t('workoutDiary.diary_title')
                }}</h3>
              <div class="h-px flex-1 bg-neutral-border mx-4 opacity-50"></div>
            </div>

            <div v-if="entries.length === 0"
              class="text-center p-16 border-2 border-dashed border-neutral-border rounded-[2.5rem]">
              <span class="material-symbols-outlined text-4xl text-secondary-text/30 mb-4 block">fitness_center</span>
              <p class="text-secondary-text font-medium">{{ $t('workoutDiary.no_entries') }}</p>
            </div>

            <div v-else class="space-y-3">
              <WorkoutCard v-for="exercise in entries" :key="exercise.pivot.id" :exercise="exercise" border>
                <template #action>
                  <div class="flex items-center gap-4 shrink-0 px-2">
                    <div class="text-right hidden sm:block">
                      <div class="text-main-text font-black text-lg tracking-tighter">
                        {{ exercise.pivot.burned_calories }}
                        <span class="text-[10px] ml-1 opacity-40">kcal</span>
                      </div>
                    </div>
                    <button @click="deleteEntry(exercise.pivot.id)"
                      class="w-10 h-10 flex items-center justify-center rounded-full text-secondary-text/30 hover:text-red-400 hover:bg-red-400/10 transition-all active:scale-90">
                      <span class="material-symbols-outlined text-xl">delete</span>
                    </button>
                  </div>
                </template>
              </WorkoutCard>
            </div>
          </div>

        </div>
      </div>
    </main>

    <AddWorkoutEntryOverlay :show="isEntryModalOpen" :exercise="selectedExerciseForEntry" :date="formattedDate"
      @close="isEntryModalOpen = false" @saved="onSaved" />
    <AddExerciseOverlay :show="isCreateModalOpen" @close="isCreateModalOpen = false" @saved="onSaved" />
  </div>
</template>