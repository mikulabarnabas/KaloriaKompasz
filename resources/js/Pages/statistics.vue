<script setup>
import { ref, computed, watch } from "vue"
import { usePage, Link } from "@inertiajs/vue3"
import DateNavigator from "@/Components/dateNavigator.vue"
import SevendDayIntake from "@/Components/sevendDayIntake.vue"
import AppLayout from "@/Layouts/appLayout.vue"
import FoodCard from "@/Components/foodCard.vue"
import WorkoutCard from "@/Components/workoutCard.vue"
import axios from "axios";
import MacroSummary from "@/Components/macroSummary.vue";

defineOptions({ layout: AppLayout });

const page = usePage()
const user = computed(() => page.props.auth?.user)

const props = defineProps({
  hasProfile: Boolean,
  targets: Object,
});

const todayStats = ref([]);
const foodDiary = ref([]);
const recentActivity = ref([]);
const workoutDiary = ref([]);

const selectedDate = ref(new Date());
const isLoading = ref(false);
const formattedDate = computed(() => selectedDate.value.toISOString().slice(0, 10));

async function getData() {
  isLoading.value = true;
  const { data } = await axios.get(`/stats/getData/${formattedDate.value}`);
  todayStats.value = data.todayStats ?? [];
  foodDiary.value = data.foodDiary ?? [];
  recentActivity.value = data.recentActivity ?? [];
  workoutDiary.value = data.workoutDiary ?? [];
  isLoading.value = false;
}


watch(selectedDate, getData, { immediate: true });
</script>

<template>
  <main class="min-h-screen bg-background-dark text-main-text p-4 md:p-8 font-sans">
    <div class="max-w-6xl mx-auto space-y-12">

      <transition enter-active-class="transition duration-500" enter-from-class="opacity-0 -translate-y-2">
        <div v-if="!hasProfile"
          class="bg-primary/10 border border-primary/20 p-6 rounded-4xl flex flex-col md:flex-row items-center justify-between gap-4">
          <div class="flex items-center gap-4">
            <span class="material-symbols-outlined text-primary text-3xl">info</span>
            <p class="text-sm font-medium">
              <span class="font-black uppercase block">
                {{ $t('statistics.profile_alert.title') }}
              </span>
              {{ $t('statistics.profile_alert.description') }}
            </p>
          </div>
          <Link href="/profile"
            class="bg-primary text-background-dark px-6 py-2 rounded-xl text-xs font-black uppercase">
            {{ $t('statistics.profile_alert.button') }}
          </Link>
        </div>
      </transition>

      <header class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 overflow-hidden">
        <div class="w-full min-w-0">
          <h1
            class="flex flex-wrap items-baseline text-4xl md:text-5xl font-black tracking-tighter uppercase leading-none">
            <span class="shrink-0 mr-2">{{ $t('statistics.welcome') }},</span>
            <span class="text-primary truncate min-w-0 max-w-full block">{{ user.name }}</span>
          </h1>
          <p class="text-secondary-text mt-3 font-medium tracking-wide uppercase text-xs">
            {{ $t('statistics.subtitle') }}
          </p>
        </div>
        <DateNavigator v-model="selectedDate" class="shrink-0" />
      </header>

      <div class="relative min-h-100">
        <div v-if="isLoading"
          class="absolute inset-0 z-50 flex flex-col items-center pt-20 bg-background-dark/10 backdrop-blur-[2px] transition-all duration-300">
          <div class="w-16 h-16 border-4 border-primary/10 border-t-primary rounded-full animate-spin"></div>
          <p class="mt-4 text-[10px] font-black uppercase tracking-[0.5em] text-primary">Updating Data</p>
        </div>

        <div
          :class="['space-y-12 transition-all duration-500', isLoading ? 'opacity-30 blur-md pointer-events-none' : 'opacity-100 blur-0']">

          <section class="space-y-6">
            <h2 class="text-xs font-black text-main-text uppercase tracking-[0.4em] flex items-center gap-3">
              <span class="w-8 h-px bg-primary"></span>
              {{ $t('statistics.daily_progress') }}
            </h2>

            <MacroSummary :targets="targets" :stats="todayStats" complex></MacroSummary>
          </section>

          <SevendDayIntake :date="formattedDate" :targets="targets" />

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <div class="lg:col-span-2 space-y-6">
              <h2 class="text-xs font-black text-main-text uppercase tracking-[0.4em] flex items-center gap-3">
                <span class="w-8 h-px bg-secondary-text"></span>
                {{ $t('statistics.activity_feed') }}
              </h2>

              <div
                class="bg-neutral-dark/10 rounded-[2.5rem] border border-neutral-border divide-y divide-neutral-border/50 overflow-hidden shadow-2xl">
                <div v-if="recentActivity.length === 0" class="p-20 text-center">
                  <p class="text-secondary-text font-black uppercase tracking-widest text-xs">{{
                    $t('statistics.no_records') }}</p>
                </div>

                <div v-for="item in recentActivity" :key="item.id">
                  <WorkoutCard v-if="item.calories_per_unit" :exercise="item" />
                  <FoodCard v-else :food="{
                    ...item,
                    calorie: item.pivot.calorie,
                    protein: item.pivot.protein,
                    carb: item.pivot.carb,
                    fat: item.pivot.fat
                  }"/>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </main>
</template>