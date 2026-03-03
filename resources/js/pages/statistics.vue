<script setup>
import { computed } from "vue"
import { usePage } from "@inertiajs/vue3"


const page = usePage()
const foodDiary = computed(() => page.props.foodDiary ?? [])
const workoutDiary = computed(() => page.props.workoutDiary ?? [])

const targets = {
  calories: 2400,
  protein: 160,
  carbs: 250,
  fat: 80
}

const num = v => Number(v ?? 0)

// Calculate Totals for "Today" (Assuming the last entry is today for this view)
const todayStats = computed(() => {
  const latestDay = foodDiary.value[foodDiary.value.length - 1]
  if (!latestDay) return { calories: 0, protein: 0, carbs: 0, fat: 0 }
  
  let calories = 0, protein = 0, carbs = 0, fat = 0
  latestDay.foods?.forEach(f => {
    calories += num(f.pivot.calorie)
    protein += num(f.pivot.protein)
    carbs += num(f.pivot.carb)
    fat += num(f.pivot.fat)
  })
  return { calories, protein, carbs, fat }
})

const getPercent = (current, target) => Math.min(Math.round((current / target) * 100), 100)
</script>

<template>
  <main class="min-h-screen bg-[#0a0c0d] text-gray-100 p-6">
    <div class="max-w-6xl mx-auto space-y-8">
      
      <header>
        <h1 class="text-4xl font-bold tracking-tight">Welcome back, Alex!</h1>
        <p class="text-gray-400 mt-2">
          Ready to crush your goals today? You're already 
          <span class="text-green-400 font-semibold">{{ getPercent(todayStats.protein, targets.protein) }}%</span> 
          of the way to your protein target!
        </p>
      </header>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-[#121619] p-6 rounded-2xl border border-gray-800 flex justify-between items-center group cursor-pointer hover:border-green-500/50 transition">
          <div>
            <h3 class="text-xl font-bold">Log Meal</h3>
            <p class="text-gray-500 text-sm">Track your breakfast, lunch, or dinner.</p>
            <button class="mt-4 bg-[#00ff66] text-black px-4 py-2 rounded-full font-bold flex items-center gap-2">
              <i class="pi pi-plus-circle"></i> Add Entry
            </button>
          </div>
          <i class="pi pi-utensils text-4xl text-gray-800 group-hover:text-green-900 transition"></i>
        </div>

        <div class="bg-[#121619] p-6 rounded-2xl border border-gray-800 flex justify-between items-center group cursor-pointer hover:border-blue-500/50 transition">
          <div>
            <h3 class="text-xl font-bold">Log Workout</h3>
            <p class="text-gray-500 text-sm">Record your training and active sets.</p>
            <button class="mt-4 bg-white text-black px-4 py-2 rounded-full font-bold flex items-center gap-2">
              <i class="pi pi-play"></i> Start Session
            </button>
          </div>
          <i class="pi pi-bolt text-4xl text-gray-800 group-hover:text-blue-900 transition"></i>
        </div>
      </div>

      <section>
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-bold flex items-center gap-2">
            <span class="w-2 h-5 bg-green-500 rounded-full"></span> Daily Progress
          </h2>
          <span class="bg-green-900/30 text-green-400 px-3 py-1 rounded-md text-xs font-mono">AUGUST 24, 2024</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div class="bg-[#121619] p-6 rounded-2xl border border-gray-800 flex flex-col items-center justify-center relative">
            <div class="relative w-32 h-32">
              <svg class="w-full h-full transform -rotate-90">
                <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="8" fill="transparent" class="text-gray-800" />
                <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="8" fill="transparent" 
                  :stroke-dasharray="364.4" 
                  :stroke-dashoffset="364.4 - (364.4 * getPercent(todayStats.calories, targets.calories)) / 100"
                  class="text-green-500 transition-all duration-1000" stroke-linecap="round" />
              </svg>
              <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-2xl font-bold">{{ todayStats.calories.toLocaleString() }}</span>
                <span class="text-[10px] uppercase text-gray-500 tracking-widest">Calories</span>
              </div>
            </div>
            <p class="mt-4 text-xs text-gray-500 uppercase tracking-widest">Goal: {{ targets.calories }}</p>
          </div>

          <div class="bg-[#121619] p-6 rounded-2xl border border-gray-800 flex flex-col justify-between">
            <div class="flex justify-between items-start">
              <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Protein</span>
              <span class="text-blue-400 text-xs font-bold">{{ getPercent(todayStats.protein, targets.protein) }}%</span>
            </div>
            <div class="mt-4">
              <div class="text-3xl font-bold">{{ todayStats.protein }}<span class="text-sm font-normal text-gray-500">g</span></div>
              <div class="text-[10px] text-gray-600 uppercase mt-1">Target: {{ targets.protein }}g</div>
            </div>
            <div class="w-full bg-gray-800 h-1.5 rounded-full mt-4 overflow-hidden">
              <div class="bg-blue-500 h-full transition-all" :style="`width: ${getPercent(todayStats.protein, targets.protein)}% shadow-lg`"></div>
            </div>
          </div>

          <div class="bg-[#121619] p-6 rounded-2xl border border-gray-800 flex flex-col justify-between">
            <div class="flex justify-between items-start">
              <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Carbs</span>
              <span class="text-yellow-400 text-xs font-bold">{{ getPercent(todayStats.carbs, targets.carbs) }}%</span>
            </div>
            <div class="mt-4">
              <div class="text-3xl font-bold">{{ todayStats.carbs }}<span class="text-sm font-normal text-gray-500">g</span></div>
              <div class="text-[10px] text-gray-600 uppercase mt-1">Target: {{ targets.carbs }}g</div>
            </div>
            <div class="w-full bg-gray-800 h-1.5 rounded-full mt-4 overflow-hidden">
              <div class="bg-yellow-500 h-full transition-all" :style="`width: ${getPercent(todayStats.carbs, targets.carbs)}% shadow-lg`"></div>
            </div>
          </div>

          <div class="bg-[#121619] p-6 rounded-2xl border border-gray-800 flex flex-col justify-between">
            <div class="flex justify-between items-start">
              <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Fat</span>
              <span class="text-pink-400 text-xs font-bold">{{ getPercent(todayStats.fat, targets.fat) }}%</span>
            </div>
            <div class="mt-4">
              <div class="text-3xl font-bold">{{ todayStats.fat }}<span class="text-sm font-normal text-gray-500">g</span></div>
              <div class="text-[10px] text-gray-600 uppercase mt-1">Target: {{ targets.fat }}g</div>
            </div>
            <div class="w-full bg-gray-800 h-1.5 rounded-full mt-4 overflow-hidden">
              <div class="bg-pink-500 h-full transition-all" :style="`width: ${getPercent(todayStats.fat, targets.fat)}% shadow-lg`"></div>
            </div>
          </div>
        </div>
      </section>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
          <div class="flex justify-between items-center mb-4">
             <h2 class="text-xl font-bold flex items-center gap-2">
              <i class="pi pi-history text-green-500"></i> Recent Activity
            </h2>
            <button class="text-green-500 text-sm font-bold">View All</button>
          </div>
          
          <div class="space-y-3">
            <div class="bg-[#121619] p-4 rounded-xl border border-gray-800 flex justify-between items-center">
              <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-green-900/20 rounded-lg flex items-center justify-center text-green-500">
                   <i class="pi pi-shopping-bag"></i>
                </div>
                <div>
                  <h4 class="font-bold">Grilled Chicken Salad</h4>
                  <p class="text-xs text-gray-500">Lunch • 1:45 PM</p>
                </div>
              </div>
              <div class="text-right">
                <div class="text-green-400 font-bold">+450 kcal</div>
                <div class="text-[10px] text-gray-500">42g Protein</div>
              </div>
            </div>

            <div class="bg-[#121619] p-4 rounded-xl border border-gray-800 flex justify-between items-center">
              <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-blue-900/20 rounded-lg flex items-center justify-center text-blue-500">
                   <i class="pi pi-bolt"></i>
                </div>
                <div>
                  <h4 class="font-bold">Push Workout (Strength)</h4>
                  <p class="text-xs text-gray-500">Active • 10:30 AM</p>
                </div>
              </div>
              <div class="text-right">
                <div class="text-blue-400 font-bold">-320 kcal</div>
                <div class="text-[10px] text-gray-500">45 mins</div>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-[#00ff66] rounded-2xl p-6 text-black flex flex-col justify-between shadow-[0_0_50px_-12px_rgba(0,255,102,0.3)]">
          <div>
            <div class="flex items-center gap-2 mb-4 font-bold">
              <i class="pi pi-lightbulb"></i> Daily Tip
            </div>
            <p class="text-sm font-medium leading-relaxed">
              Drinking a glass of water before your meal can help digestion and naturally control calorie intake. You've only logged 1.2L of water today—aim for 2.5L!
            </p>
          </div>
          <button class="w-full bg-black text-white font-bold py-3 rounded-xl mt-6">
            Add Water Log
          </button>
        </div>
      </div>

    </div>
  </main>
</template>

<style scoped>
/* Optional: Adding a soft glow to the ring */
circle.text-green-500 {
  filter: drop-shadow(0 0 6px rgba(34, 197, 94, 0.4));
}
</style>