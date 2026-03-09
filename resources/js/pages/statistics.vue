<script setup>
import { ref, computed } from "vue"
import { usePage } from "@inertiajs/vue3"
import DateNavigator from "@/Components/dateNavigator.vue"
import SevendDayIntake from "@/Components/sevendDayIntake.vue";

import AppLayout from "@/Layouts/AppLayout.vue";
defineOptions({ layout: AppLayout });

const page = usePage()
const foodDiary = computed(() => page.props.foodDiary ?? [])
const workoutDiary = computed(() => page.props.workoutDiary ?? [])
const user = computed(() => page.props.auth?.user)

console.log(user)


const selectedDate = ref(new Date());
const formattedDate = computed(() => selectedDate.value.toISOString().slice(0, 10));


const getEntryForDate = (dataArray) => {
  return dataArray.find(entry => {
    const entryDate = new Date(entry.date).toISOString().split('T')[0]
    return entryDate === formattedDate.value
  })
}

const targets = {
  calories: 2400,
  protein: 160,
  carbs: 250,
  fat: 80
}

const num = v => Number(v ?? 0)


const todayStats = computed(() => {
  const dayEntry = getEntryForDate(foodDiary.value)
  if (!dayEntry) return { calories: 0, protein: 0, carbs: 0, fat: 0 }

  return (dayEntry.foods || []).reduce((acc, f) => {
    acc.calories += num(f.pivot.calorie)
    acc.protein += num(f.pivot.protein)
    acc.carbs += num(f.pivot.carb)
    acc.fat += num(f.pivot.fat)
    return acc
  }, { calories: 0, protein: 0, carbs: 0, fat: 0 })
})

const todayWorkouts = computed(() => {
  const dayEntry = getEntryForDate(workoutDiary.value)
  if (!dayEntry) return { burned: 0 }

  return (dayEntry.exercises || []).reduce((acc, e) => {
    acc.burned += num(e.pivot.amount) * num(e.calories_per_unit)
    return acc
  }, { burned: 0 })
})

const recentActivity = computed(() => {
  const foodItems = (getEntryForDate(foodDiary.value)?.foods || []).map(i => ({ ...i, type: 'food' }))
  const workoutItems = (getEntryForDate(workoutDiary.value)?.exercises || []).map(i => ({ ...i, type: 'workout' }))

  return [...foodItems, ...workoutItems]
    .sort((a, b) => b.pivot.id - a.pivot.id)
    .slice(0, 4)
})

const getPercent = (current, target) => Math.min(Math.round((current / target) * 100), 100)
</script>

<template>
  <main class="min-h-screen bg-background-dark text-main-text p-4 md:p-8 font-sans">
    <div class="max-w-6xl mx-auto space-y-12">

      <header class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div>
          <h1 class="text-4xl md:text-5xl font-black tracking-tighter uppercase leading-none">
            Welcome back, <span class="text-primary">{{ user.name }}</span>
          </h1>
          <p class="text-secondary-text mt-3 font-medium tracking-wide opacity-60 uppercase text-xs">
            Ready to crush your goals for {{ formattedDate }}?
          </p>
        </div>
        <DateNavigator v-model="selectedDate" />
      </header>

      <section class="space-y-6">
        <h2 class="text-xs font-black text-main-text uppercase tracking-[0.4em] flex items-center gap-3">
          <span class="w-8 h-[1px] bg-primary"></span>
          Daily Progress
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
          <div
            class="bg-neutral-dark/30 p-8 rounded-[2.5rem] border border-neutral-border hover:border-primary/50 flex flex-col items-center justify-center relative shadow-2xl group overflow-hidden transition-all duration-500">
            <div
              class="absolute -top-12 -left-12 size-32 bg-primary/5 blur-3xl rounded-full group-hover:bg-primary/10 transition-colors duration-500">
            </div>

            <div class="relative w-40 h-40 group-hover:scale-105 transition-transform duration-700">
              <svg class="w-full h-full transform -rotate-90">
                <circle cx="80" cy="80" r="72" stroke="currentColor" stroke-width="10" fill="transparent"
                  class="text-neutral-dark" />
                <circle cx="80" cy="80" r="72" stroke="currentColor" stroke-width="10" fill="transparent"
                  :stroke-dasharray="452.4"
                  :stroke-dashoffset="452.4 - (452.4 * getPercent(todayStats.calories, targets.calories)) / 100"
                  class="text-primary transition-all duration-1000 ease-out" stroke-linecap="round" />
              </svg>
              <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-4xl font-black tracking-tighter">{{ todayStats.calories.toLocaleString() }}</span>
                <span class="text-[9px] font-black uppercase text-secondary-text tracking-[0.2em] mt-1">Consumed</span>
              </div>
            </div>

            <div class="mt-6 text-center z-10">
              <p class="text-[10px] font-black text-secondary-text uppercase tracking-widest opacity-40">Goal: {{
                targets.calories }} kcal</p>
              <div
                class="mt-2 inline-flex items-center gap-2 px-3 py-1 bg-blue-500/10 border border-blue-500/20 rounded-full group-hover:border-blue-500/40 transition-colors">
                <span class="material-symbols-outlined text-sm text-blue-400">bolt</span>
                <span class="text-blue-400 text-[10px] font-black uppercase tracking-wider">-{{ todayWorkouts.burned }}
                  kcal burned</span>
              </div>
            </div>
          </div>

          <div v-for="macro in [
            { label: 'Protein', current: todayStats.protein, target: targets.protein, color: 'text-blue-400', bg: 'bg-blue-500', glow: 'shadow-blue-500/20' },
            { label: 'Carbs', current: todayStats.carbs, target: targets.carbs, color: 'text-amber-400', bg: 'bg-amber-500', glow: 'shadow-amber-500/20' },
            { label: 'Fat', current: todayStats.fat, target: targets.fat, color: 'text-pink-400', bg: 'bg-pink-500', glow: 'shadow-pink-500/20' }
          ]" :key="macro.label"
            class="bg-neutral-dark/30 p-8 rounded-[2.5rem] border border-neutral-border hover:border-primary/50 flex flex-col justify-between shadow-2xl transition-all duration-500 group">
            <div class="flex justify-between items-start">
              <span
                class="text-[10px] font-black text-secondary-text uppercase tracking-[0.2em] opacity-60 group-hover:text-primary/60 transition-colors">{{
                macro.label }}</span>
              <span :class="[macro.color, 'text-[10px] font-black uppercase tracking-widest']">{{
                getPercent(macro.current, macro.target) }}%</span>
            </div>

            <div class="mt-4">
              <div class="text-4xl font-black tracking-tighter">{{ macro.current }}<span
                  class="text-sm font-bold text-secondary-text ml-1">g</span></div>
              <div class="text-[9px] font-black text-secondary-text uppercase tracking-widest mt-1 opacity-40">Target:
                {{ macro.target }}g</div>
            </div>

            <div class="w-full bg-neutral-dark h-2 rounded-full mt-6 overflow-hidden border border-white/5">
              <div :class="[macro.bg, 'h-full transition-all duration-1000 ease-out shadow-lg', macro.glow]"
                :style="`width: ${getPercent(macro.current, macro.target)}%`"></div>
            </div>
          </div>
        </div>
      </section>

      <SevendDayIntake v-model="foodDiary" :workout-diary="workoutDiary" :targets="userTargets" />

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <div class="lg:col-span-2 space-y-6">
          <h2 class="text-xs font-black text-main-text uppercase tracking-[0.4em] flex items-center gap-3">
            <span class="w-8 h-[1px] bg-secondary-text opacity-30"></span>
            Activity Feed
          </h2>

          <div
            class="bg-neutral-dark/10 rounded-[2.5rem] border border-neutral-border divide-y divide-neutral-border/50 overflow-hidden shadow-2xl">
            <div v-if="recentActivity.length === 0" class="p-20 text-center">
              <span class="material-symbols-outlined text-4xl text-neutral-dark mb-3">history</span>
              <p class="text-secondary-text font-black uppercase tracking-widest text-xs opacity-40">No records found
                for this date</p>
            </div>

            <div v-for="item in recentActivity" :key="item.pivot.id"
              class="p-5 flex justify-between items-center hover:bg-white/5 transition-all group">
              <div class="flex items-center gap-4">
                <div
                  :class="`w-12 h-12 rounded-2xl flex items-center justify-center border border-neutral-border transition-all duration-300 ${item.type === 'food' ? 'bg-primary/10 text-primary group-hover:bg-primary' : 'bg-blue-500/10 text-blue-400 group-hover:bg-blue-500'} group-hover:text-background-dark group-hover:scale-110 group-hover:border-primary/50`">
                  <span class="material-symbols-outlined text-xl">{{ item.type === 'food' ? 'restaurant' :
                    'fitness_center' }}</span>
                </div>
                <div>
                  <h4 class="font-black text-sm uppercase tracking-tight">{{ item.name }}</h4>
                  <p class="text-[10px] font-bold text-secondary-text uppercase tracking-[0.15em] mt-0.5">
                    {{ item.type === 'food' ? 'Nutritional Intake' : 'Physical Activity' }} • <span
                      class="text-main-text">{{ item.pivot.amount }} {{ item.unit }}</span>
                  </p>
                </div>
              </div>
              <div class="text-right">
                <div
                  :class="`${item.type === 'food' ? 'text-primary' : 'text-blue-400'} font-black text-lg tracking-tighter uppercase`">
                  {{ item.type === 'food' ? '+' : '-' }}{{ item.type === 'food' ? item.pivot.calorie :
                    (item.pivot.amount * item.calories_per_unit) }}
                  <span class="text-[10px] font-bold opacity-60 ml-0.5">kcal</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>