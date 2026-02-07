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


// ==================== FOOD TOTALS ====================

const macroTotals = computed(() => {
  let calories = 0
  let protein = 0
  let carbs = 0
  let fat = 0

  foodDiary.value.forEach(day => {
    day.foods?.forEach(food => {
      calories += num(food.pivot.calorie)
      protein += num(food.pivot.protein)
      carbs += num(food.pivot.carb)
      fat += num(food.pivot.fat)
    })
  })

  return { calories, protein, carbs, fat }
})


// ==================== MACRO PIE ====================

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


// ==================== FOOD CALORIES PER DAY ====================

const caloriesPerDay = computed(() => {
  const map = {}

  foodDiary.value.forEach(day => {
    let total = 0

    day.foods?.forEach(food => {
      total += num(food.pivot.calorie)
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


// ==================== WORKOUT TOTALS (MATCH FOOD LOGIC) ====================

const workoutTotals = computed(() => {
  let burned = 0

  workoutDiary.value.forEach(day => {
    day.exercises?.forEach(ex => {
      burned += num(ex.pivot.burned_calories)
    })
  })

  return { burned }
})


// ==================== WORKOUT PER DAY ====================

const burnedPerDay = computed(() => {
  const map = {}

  workoutDiary.value.forEach(day => {
    let total = 0

    day.exercises?.forEach(ex => {
      total += num(ex.pivot.burned_calories)
    })

    map[day.date] = total
  })

  return map
})

const workoutCaloriesChart = computed(() => ({
  labels: Object.keys(burnedPerDay.value),
  datasets: [
    {
      label: "Burned kcal",
      data: Object.values(burnedPerDay.value)
    }
  ]
}))

const totalBurned = computed(() => workoutTotals.value.burned)

console.log(workoutDiary)

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

          <div v-if="foodDiary.length">
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
          </div>
          <div v-else class="mt-4 h-40 flex items-center justify-center rounded-xl border border-dashed text-sm">
            You don't have any recorded food.
          </div>

        </TabPanel>


        <!-- ================= WORKOUT ================= -->

        <TabPanel value="workouts">

          <div v-if="workoutDiary.length">
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
          </div>
          <div v-else class="mt-4 h-40 flex items-center justify-center rounded-xl border border-dashed text-sm">
            You don't have any recorded sport activity.
          </div>

        </TabPanel>

      </TabPanels>

    </Tabs>

  </main>
</template>
