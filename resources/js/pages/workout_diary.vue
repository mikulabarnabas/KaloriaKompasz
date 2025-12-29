<script setup>
import { ref, computed, watch } from 'vue'

import Navbar from "../components/navbar.vue"
import Footer from "../components/footer.vue"

import Paginator from 'primevue/paginator'
import Divider from 'primevue/divider'

const search = ref('')
const first = ref(0)
const rows = ref(3)

const selectedExercise = ref(null)

const exercises = ref([
  
])


const filteredExercises = computed(() => {
  if (!search.value) return []
  return exercises.value.filter(e =>
    e.name.toLowerCase().includes(search.value.toLowerCase())
  )
})

const paginatedExercises = computed(() =>
  filteredExercises.value.slice(first.value, first.value + rows.value)
)

watch(search, () => {
  first.value = 0
  selectedExercise.value = null
})

const selectExercise = (exercise) => {
  selectedExercise.value = exercise
}
</script>

<template>
  <div class="page">
    <Navbar />

    <main class="content">
      <div class="exercise-search">

        <div class="search-layout">

          <!-- ⬅️ Bal oldal -->
          <div class="left-panel">
            <div class="search-wrapper">
              <input
                v-model="search"
                type="text"
                placeholder="Keress edzést..."
                class="search-input"
              />
              <button class="search-button">🔍</button>
            </div>

            <ul v-if="paginatedExercises.length" class="results">
              <li
                v-for="exercise in paginatedExercises"
                :key="exercise.name"
                @click="selectExercise(exercise)"
                class="result-item"
              >
                <strong>{{ exercise.name }}</strong>
                <div class="values">
                  💪 {{ exercise.muscle }} |
                  ⚡ {{ exercise.difficulty }}
                </div>
              </li>
            </ul>

            <Paginator
              v-if="filteredExercises.length > rows"
              :first="first"
              :rows="rows"
              :totalRecords="filteredExercises.length"
              :pageLinkSize="4"
              @page="e => first = e.first"
            />
          </div>

          <!-- ➡️ Jobb oldal -->
          <div v-if="selectedExercise" class="right-panel">
            <div class="details-card">
              <h3>{{ selectedExercise.name }}</h3>

              <p><strong>Izomcsoport:</strong> {{ selectedExercise.muscle }}</p>
              <p><strong>Nehézség:</strong> {{ selectedExercise.difficulty }}</p>
              <p><strong>Kalória:</strong> ~{{ selectedExercise.calories }} kcal / perc</p>

              <Divider />

              <p class="desc">{{ selectedExercise.description }}</p>

              <Divider />

              <p><strong>Variációk:</strong></p>
              <ul>
                <li v-for="v in selectedExercise.variations" :key="v">
                  • {{ v }}
                </li>
              </ul>
            </div>
          </div>

        </div>
      </div>
    </main>

    <Divider />
    <Footer />
  </div>
</template>

<style scoped>

</style>
