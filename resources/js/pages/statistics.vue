<script setup>
import AppLayout from "@/Layouts/AppLayout.vue"
import { computed } from "vue"
import { usePage } from "@inertiajs/vue3"

import Card from "primevue/card"
import Chart from "primevue/chart"
import Tabs from "primevue/tabs"
import TabList from "primevue/tablist"
import Tab from "primevue/tab"
import TabPanels from "primevue/tabpanels"
import TabPanel from "primevue/tabpanel"
import ProgressBar from "primevue/progressbar"

defineOptions({ layout: AppLayout })

const page = usePage()

const foodDiary = computed(() => page.props.foodDiary ?? [])
const workoutDiary = computed(() => page.props.workoutDiary ?? [])

const num = v => Number(v ?? 0)

// -------------------- MACRO TOTALS --------------------

const macroTotals = computed(() => {
  let calories = 0
  let protein = 0
  let carbs = 0
  let fat = 0

  foodDiary.value.forEach(day => {
    day.foods?.forEach(food => {
      calories += num(food.calorie)
      protein += num(food.protein)
      carbs += num(food.carb)
      fat += num(food.fat)
    })
  })

  return { calories, protein, carbs, fat }
})

// -------------------- MACRO PIE --------------------

const macroChartData = computed(() => ({
  labels: ["Protein", "Carbs", "Fat"],
  datasets: [
    {
      data: [
        macroTotals.value.protein,
        macroTotals.value.carbs,
        macroTotals.value.fat
      ]
    }
  ]
}))

const pieOptions = {
  plugins: {
    legend: { position: "bottom" }
  }
}

// -------------------- CALORIES PER DAY --------------------

const caloriesPerDay = computed(() => {
  const map = {}

  foodDiary.value.forEach(day => {
    let total = 0
    day.foods?.forEach(food => {
      total += num(food.calorie)
    })
    map[day.date] = total
  })

  return map
})

const caloriesChart = computed(() => ({
  labels: Object.keys(caloriesPerDay.value),
  datasets: [
    {
      label: "Calories",
      data: Object.values(caloriesPerDay.value)
    }
  ]
}))

// -------------------- WORKOUT CALORIES --------------------

const workoutCaloriesChart = computed(() => ({
  labels: workoutDiary.value.map(d => d.date),
  datasets: [
    {
      label: "Burned kcal",
      data: workoutDiary.value.map(d => num(d.burned_calories))
    }
  ]
}))

const totalBurned = computed(() =>
  workoutDiary.value.reduce((s, d) => s + num(d.burned_calories), 0)
)

console.log(workoutDiary)
console.log(foodDiary)
</script>

<template>
  <main class="mx-auto w-full max-w-7xl px-4 py-6 space-y-6">

    <h1 class="text-2xl font-bold">Statistics Dashboard</h1>

    <Tabs value="nutrition">

      <TabList>
        <Tab value="nutrition">Nutrition</Tab>
        <Tab value="workouts">Workouts</Tab>
      </TabList>

      <TabPanels>

        <!-- ================= NUTRITION ================= -->

        <TabPanel value="nutrition">

          <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

            <Card>
              <template #title>Calories</template>
              <template #content>
                <div class="text-3xl font-bold">
                  {{ macroTotals.calories }}
                </div>
              </template>
            </Card>

            <Card>
              <template #title>Protein</template>
              <template #content>
                <ProgressBar :value="macroTotals.protein" />
                {{ macroTotals.protein }} g
              </template>
            </Card>

            <Card>
              <template #title>Carbs</template>
              <template #content>
                <ProgressBar :value="macroTotals.carbs" />
                {{ macroTotals.carbs }} g
              </template>
            </Card>

            <Card>
              <template #title>Fat</template>
              <template #content>
                <ProgressBar :value="macroTotals.fat" />
                {{ macroTotals.fat }} g
              </template>
            </Card>

          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

            <Card>
              <template #title>Macro Split</template>
              <template #content>
                <Chart type="pie" :data="macroChartData" :options="pieOptions" />
              </template>
            </Card>

            <Card>
              <template #title>Calories Per Day</template>
              <template #content>
                <Chart type="bar" :data="caloriesChart" />
              </template>
            </Card>

          </div>

        </TabPanel>

        <!-- ================= WORKOUT ================= -->

        <TabPanel value="workouts">

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <Card>
              <template #title>Burned Calories Over Time</template>
              <template #content>
                <Chart type="line" :data="workoutCaloriesChart" />
              </template>
            </Card>

            <Card>
              <template #title>Total Burned</template>
              <template #content>
                <div class="text-3xl font-bold">
                  {{ totalBurned }} kcal
                </div>
              </template>
            </Card>

          </div>

        </TabPanel>

      </TabPanels>

    </Tabs>

  </main>
</template>
