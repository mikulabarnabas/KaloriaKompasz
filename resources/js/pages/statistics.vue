<script setup>
import { ref, computed, watch } from "vue"
import { usePage, Link, router } from "@inertiajs/vue3"
import DateNavigator from "@/Components/dateNavigator.vue"
import SevendDayIntake from "@/Components/sevendDayIntake.vue"
import AppLayout from "@/Layouts/appLayout.vue"
import FoodCard from "@/Components/foodCard.vue"
import WorkoutCard from "@/Components/workoutCard.vue"

defineOptions({ layout: AppLayout });

const page = usePage()
const user = computed(() => page.props.auth?.user)

const props = defineProps({
  hasProfile: Boolean,
  targets: Object,
  todayStats: Object,
  recentActivity: Array,
  foodDiary: Array,
  workoutDiary: Array,
  filters: Object
});

const selectedDate = ref(new Date());
const isLoading = ref(false);
const formattedDate = computed(() => selectedDate.value.toISOString().slice(0, 10));


watch(selectedDate, () => {
  router.reload({
    data: { date: formattedDate.value },
    only: ['todayStats', 'recentActivity', 'foodDiary', 'workoutDiary'],
    preserveState: true,
    preserveScroll: true,
    onBefore: () => { isLoading.value = true },
    onFinish: () => { isLoading.value = false },
  });
});

const getPercent = (current, target) => Math.min(Math.round((current / (target || 1)) * 100), 100);
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
          <h1 class="flex flex-wrap items-baseline text-4xl md:text-5xl font-black tracking-tighter uppercase leading-none">
            <span class="shrink-0 mr-2">{{ $t('statistics.welcome') }},</span>
            <span class="text-primary truncate min-w-0 max-w-full block">{{ user.name }}</span>
          </h1>
          <p class="text-secondary-text mt-3 font-medium tracking-wide uppercase text-xs">
            {{ $t('statistics.subtitle', { date: formattedDate }) }}
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

        <div :class="['space-y-12 transition-all duration-500', isLoading ? 'opacity-30 blur-md pointer-events-none' : 'opacity-100 blur-0']">

          <section class="space-y-6">
            <h2 class="text-xs font-black text-main-text uppercase tracking-[0.4em] flex items-center gap-3">
              <span class="w-8 h-px bg-primary"></span>
              {{ $t('statistics.daily_progress') }}
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
              <div class="bg-neutral-dark/30 p-8 rounded-[2.5rem] border border-neutral-border flex flex-col items-center justify-center relative shadow-2xl group overflow-hidden">
                <div class="relative w-40 h-40">
                  <svg class="w-full h-full transform -rotate-90">
                    <circle cx="80" cy="80" r="72" stroke="currentColor" stroke-width="10" fill="transparent" class="text-neutral-dark" />
                    <circle cx="80" cy="80" r="72" stroke="currentColor" stroke-width="10" fill="transparent"
                      :stroke-dasharray="452.4"
                      :stroke-dashoffset="452.4 - (452.4 * getPercent(todayStats.calories, targets.calories)) / 100"
                      class="text-primary transition-all duration-1000 ease-out" stroke-linecap="round" />
                  </svg>
                  <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-4xl font-black tracking-tighter">{{ todayStats.calories }}</span>
                    <span class="text-[9px] font-black uppercase text-secondary-text tracking-[0.2em] mt-1">{{ $t('statistics.consumed') }}</span>
                  </div>
                </div>
                <div class="mt-6 text-center">
                  <p class="text-[10px] font-black text-secondary-text uppercase tracking-widest">Goal: {{ targets.calories }} kcal</p>
                  <div class="mt-2 inline-flex items-center gap-2 px-3 py-1 bg-blue-500/10 border border-blue-500/20 rounded-full">
                    <span class="material-symbols-outlined text-sm text-blue-400">bolt</span>
                    <span class="text-blue-400 text-[10px] font-black uppercase tracking-wider">-{{ todayStats.burned }} {{ $t('statistics.burned') }}</span>
                  </div>
                </div>
              </div>

              <div v-for="macro in [
                { label: $t('statistics.macros.protein'), current: todayStats.protein, target: targets.protein, color: 'text-blue-400', bg: 'bg-blue-500' },
                { label: $t('statistics.macros.carbs'), current: todayStats.carbs, target: targets.carbs, color: 'text-amber-400', bg: 'bg-amber-500' },
                { label: $t('statistics.macros.fat'), current: todayStats.fat, target: targets.fat, color: 'text-pink-400', bg: 'bg-pink-500' }
              ]" :key="macro.label"
                class="bg-neutral-dark/30 p-8 rounded-[2.5rem] border border-neutral-border flex flex-col justify-between shadow-2xl transition-all">
                <div class="flex justify-between items-start">
                  <span class="text-[10px] font-black text-secondary-text uppercase tracking-[0.2em]">{{ macro.label }}</span>
                  <span :class="[macro.color, 'text-[10px] font-black uppercase tracking-widest']">{{ getPercent(macro.current, macro.target) }}%</span>
                </div>
                <div class="mt-4">
                  <div class="text-4xl font-black tracking-tighter">{{ macro.current }}<span class="text-sm font-bold text-secondary-text ml-1">g</span></div>
                  <div class="text-[12px] font-black text-secondary-text uppercase tracking-widest mt-1">Target: {{ macro.target }}g</div>
                </div>
                <div class="w-full bg-neutral-dark h-2 rounded-full mt-6 overflow-hidden border border-white/5">
                  <div :class="[macro.bg, 'h-full transition-all duration-1000 shadow-lg']" :style="`width: ${getPercent(macro.current, macro.target)}%`"></div>
                </div>
              </div>
            </div>
          </section>

          <SevendDayIntake :food-diary="foodDiary" :workout-diary="workoutDiary" :targets="targets" />

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <div class="lg:col-span-2 space-y-6">
              <h2 class="text-xs font-black text-main-text uppercase tracking-[0.4em] flex items-center gap-3">
                <span class="w-8 h-px bg-secondary-text"></span>
                {{ $t('statistics.activity_feed') }}
              </h2>

              <div class="bg-neutral-dark/10 rounded-[2.5rem] border border-neutral-border divide-y divide-neutral-border/50 overflow-hidden shadow-2xl">
                <div v-if="recentActivity.length === 0" class="p-20 text-center">
                  <p class="text-secondary-text font-black uppercase tracking-widest text-xs">{{ $t('statistics.no_records') }}</p>
                </div>

                <div v-for="item in recentActivity" :key="item.id">
                  <WorkoutCard 
                    v-if="item.calories_per_unit" 
                    :exercise="item" 
                    :clickable="true" 
                  />
                  <FoodCard 
                    v-else 
                    :food="item" 
                    :clickable="true" 
                  />
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </main>
</template>