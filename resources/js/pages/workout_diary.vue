<script setup>
import { computed, ref, watch } from "vue";
import axios from "axios";
import { useI18n } from 'vue-i18n';


import WorkoutSearch from "@/Components/workoutSearch.vue";
import AddWorkoutEntryOverlay from "@/Components/addWorkoutEntryOverlay.vue";
import AddExerciseOverlay from "@/Components/addExerciseOverlay.vue";
import DateNavigator from "@/Components/dateNavigator.vue"

const { t } = useI18n();
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

const loadDiary = async () => {
  const { data } = await axios.get(`/wdiary/diary/${formattedDate.value}`);
  entries.value = data.diary?.exercises ?? [];
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
</script>

<template>
  <div class="bg-background-dark text-white relative min-h-screen">
    <main class="flex flex-col min-w-0">
      
      <header class="p-4 md:p-6 border-b border-neutral-border bg-background-dark/80 backdrop-blur-xl sticky top-0 z-40">
        <div class="max-w-4xl mx-auto flex gap-4 items-center">
          
          <WorkoutSearch class="flex-1" :placeholder="$t('workoutDiary.search_placeholder')" @select="onExerciseSelect" />

          <DateNavigator v-model="selectedDate" />

          <button @click="openCreateExerciseModal"
            class="flex items-center justify-center w-12 h-12 md:w-auto md:px-5 md:h-12 bg-primary/10 border border-primary/20 rounded-2xl text-primary hover:bg-primary hover:text-background-dark transition-all duration-300 active:scale-95 group">
            <span class="material-symbols-outlined text-2xl group-hover:rotate-90 transition-transform duration-300">add_circle</span>
            <span class="hidden md:inline ml-2 font-black uppercase tracking-widest text-xs">
              {{ $t('workoutDiary.create_exercise_title') }}
            </span>
          </button>

        </div>
      </header>

      <div class="p-6 space-y-10 pb-32">
        <div class="max-w-4xl mx-auto space-y-10">

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="relative overflow-hidden bg-neutral-dark/30 p-8 rounded-[2rem] border border-neutral-border shadow-2xl group">
               <h3 class="text-secondary-text text-[10px] font-black uppercase tracking-[0.2em] mb-2">{{ $t('workoutDiary.burned_label') }}</h3>
               <div class="relative z-10 flex items-baseline gap-2">
                 <span class="text-5xl font-black text-primary tracking-tighter group-hover:scale-105 transition-transform duration-500">{{ totalBurned }}</span>
                 <span class="text-primary/40 font-bold uppercase tracking-widest text-sm">kcal</span>
               </div>
               <div class="absolute -right-4 -bottom-4 size-24 bg-primary/5 blur-3xl rounded-full"></div>
            </div>

            <div class="relative overflow-hidden bg-neutral-dark/30 p-8 rounded-[2rem] border border-neutral-border shadow-2xl group">
               <h3 class="text-secondary-text text-[10px] font-black uppercase tracking-[0.2em] mb-2">{{ $t('workoutDiary.duration_label') }}</h3>
               <div class="relative z-10 flex items-baseline gap-2">
                 <span class="text-5xl font-black text-blue-400 tracking-tighter group-hover:scale-105 transition-transform duration-500">{{ totalDuration }}</span>
                 <span class="text-blue-400/40 font-bold uppercase tracking-widest text-sm">min</span>
               </div>
               <div class="absolute -right-4 -bottom-4 size-24 bg-blue-500/5 blur-3xl rounded-full"></div>
            </div>
          </div>

          <div class="space-y-6">
            <div class="flex items-center justify-between px-2">
              <h3 class="text-sm font-black text-main-text uppercase tracking-[0.3em]">{{ $t('workoutDiary.diary_title') }}</h3>
              <div class="h-[1px] flex-1 bg-neutral-border mx-4 opacity-50"></div>
            </div>
            
            <div v-if="entries.length === 0" 
                 class="group text-center p-16 border-2 border-dashed border-neutral-border rounded-[2.5rem] hover:border-primary/30 transition-colors duration-500">
               <div class="w-20 h-20 bg-neutral-dark/50 rounded-full flex items-center justify-center mx-auto mb-4 border border-neutral-border group-hover:scale-110 transition-transform">
                  <span class="material-symbols-outlined text-4xl text-secondary-text/30 group-hover:text-primary transition-colors">fitness_center</span>
               </div>
               <p class="text-secondary-text font-medium tracking-wide">{{ $t('workoutDiary.no_entries') }}</p>
            </div>

            <div v-else class="space-y-3">
              <div v-for="exercise in entries" :key="exercise.pivot.id" 
                class="bg-neutral-dark/20 backdrop-blur-sm border border-neutral-border p-4 rounded-2xl flex items-center justify-between hover:border-primary/40 transition-all group">
                
                <div class="flex items-center gap-4 min-w-0">
                  <div class="w-14 h-14 rounded-2xl bg-neutral-dark flex items-center justify-center border border-neutral-border shrink-0 group-hover:bg-primary/5 transition-colors">
                    <span class="material-symbols-outlined text-2xl text-primary/60 group-hover:text-primary group-hover:scale-110 transition-all">fitness_center</span>
                  </div>
                  <div class="truncate">
                    <h4 class="text-main-text font-black text-sm uppercase tracking-tight truncate">{{ exercise.name }}</h4>
                    <p class="text-[10px] font-bold text-secondary-text uppercase tracking-[0.15em] mt-1">
                      <span class="text-primary/80">{{ exercise.pivot.amount }}</span> {{ exercise.pivot.unit }}
                    </p>
                  </div>
                </div>

                <div class="flex items-center gap-6 shrink-0">
                  <div class="text-right">
                    <div class="text-main-text font-black text-lg tracking-tighter">{{ exercise.pivot.burned_calories }}<span class="text-[10px] ml-1 opacity-40">kcal</span></div>
                  </div>
                  
                  <button @click="deleteEntry(exercise.pivot.id)" 
                    class="w-10 h-10 flex items-center justify-center rounded-full text-secondary-text/30 hover:text-red-400 hover:bg-red-400/10 transition-all active:scale-90">
                    <span class="material-symbols-outlined text-xl">delete</span>
                  </button>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </main>

    <AddWorkoutEntryOverlay 
      :show="isEntryModalOpen" 
      :exercise="selectedExerciseForEntry" 
      :date="formattedDate"
      @close="isEntryModalOpen = false" 
      @saved="onSaved" 
    />

    <AddExerciseOverlay 
      :show="isCreateModalOpen" 
      @close="isCreateModalOpen = false" 
      @saved="onSaved" 
    />

  </div>
</template>