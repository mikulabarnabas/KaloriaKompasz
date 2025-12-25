<script setup>
import { ref, computed, watch } from 'vue'

import Navbar from "../components/navbar.vue"
import Footer from "../components/footer.vue"

import Paginator from 'primevue/paginator'
import Divider from 'primevue/divider'

const search = ref('')
const first = ref(0)
const rows = ref(3)

const selectedFood = ref(null)

const foods = ref([
  { name: 'Csirkemell (sült)', calories: 165, fat: 3.6, protein: 31 },
  { name: 'Pulykamell', calories: 135, fat: 1.2, protein: 30 },
  { name: 'Marhahús (sovány)', calories: 250, fat: 15, protein: 26 },
  { name: 'Sertéskaraj', calories: 242, fat: 14, protein: 27 },
  { name: 'Lazac', calories: 208, fat: 13, protein: 20 },
  { name: 'Tonhal (konzerv, víz)', calories: 116, fat: 1, protein: 26 },

  { name: 'Tojás', calories: 155, fat: 11, protein: 13 },
  { name: 'Tojásfehérje', calories: 52, fat: 0.2, protein: 11 },
  { name: 'Tehéntúró', calories: 98, fat: 4.3, protein: 11 },
  { name: 'Görög joghurt', calories: 59, fat: 0.4, protein: 10 },
  { name: 'Sajt (trappista)', calories: 356, fat: 28, protein: 25 },

  { name: 'Rizs (főtt)', calories: 130, fat: 0.3, protein: 2.7 },
  { name: 'Barna rizs (főtt)', calories: 123, fat: 1, protein: 2.6 },
  { name: 'Bulgur (főtt)', calories: 83, fat: 0.2, protein: 3.1 },
  { name: 'Zabpehely', calories: 389, fat: 6.9, protein: 16.9 },
  { name: 'Teljes kiőrlésű kenyér', calories: 247, fat: 4.2, protein: 13 },

  { name: 'Alma', calories: 52, fat: 0.2, protein: 0.3 },
  { name: 'Banán', calories: 89, fat: 0.3, protein: 1.1 },
  { name: 'Narancs', calories: 47, fat: 0.1, protein: 0.9 },
  { name: 'Eper', calories: 32, fat: 0.3, protein: 0.7 },
  { name: 'Szőlő', calories: 69, fat: 0.2, protein: 0.7 }
])

const filteredFoods = computed(() => {
  if (!search.value) return []
  return foods.value.filter(food =>
    food.name.toLowerCase().includes(search.value.toLowerCase())
  )
})

const paginatedFoods = computed(() =>
  filteredFoods.value.slice(first.value, first.value + rows.value)
)

watch(search, () => {
  first.value = 0
  selectedFood.value = null
})

const selectFood = (food) => {
  selectedFood.value = food
}
</script>

<template>
  <div class="page">
    <Navbar />

    <main class="content">
      <div class="food-search">

        <div class="search-layout">

          <!-- ⬅️ Bal oldal -->
          <div class="left-panel">
            <div class="search-wrapper">
              <input
                v-model="search"
                type="text"
                placeholder="Keress ételt..."
                class="search-input"
              />
              <button class="search-button">🔍</button>
            </div>

            <ul v-if="paginatedFoods.length" class="results">
              <li
                v-for="food in paginatedFoods"
                :key="food.name"
                @click="selectFood(food)"
                class="result-item"
              >
                <strong>{{ food.name }}</strong>
                <div class="values">
                  🔥 {{ food.calories }} kcal |
                  🧈 {{ food.fat }} g |
                  💪 {{ food.protein }} g
                </div>
              </li>
            </ul>

            <Paginator
              v-if="filteredFoods.length > rows"
              :first="first"
              :rows="rows"
              :totalRecords="filteredFoods.length"
              :pageLinkSize="4"
              @page="e => first = e.first"
              class="paginator"
            />
          </div>

          <!-- ➡️ Jobb oldal -->
          <div v-if="selectedFood" class="right-panel">
            <div class="food-card">
              <h3>{{ selectedFood.name }}</h3>

              <div class="macro">
                <span>🔥</span>
                <strong>{{ selectedFood.calories }}</strong>
                <small>kcal</small>
              </div>

              <div class="macro">
                <span>🧈</span>
                <strong>{{ selectedFood.fat }}</strong>
                <small>g zsír</small>
              </div>

              <div class="macro">
                <span>💪</span>
                <strong>{{ selectedFood.protein }}</strong>
                <small>g fehérje</small>
              </div>
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
.page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.content {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: flex-start;
}

/* Layout */
.search-layout {
  display: flex;
  gap: 1.5rem;
  align-items: flex-start;
}

.left-panel {
  width: 420px;
}

.right-panel {
  min-width: 240px;
}

/* Search card */
.food-search {
  margin: 2rem auto;
}

/* Kereső */
.search-wrapper {
  display: flex;
  gap: 0.5rem;
}

.search-input {
  flex: 1;
  padding: 0.65rem;
  border-radius: 10px;
  border: none;
  background-color: rgba(25, 212, 118, 0.75);
}

.search-button {
  padding: 0 1rem;
  border-radius: 10px;
  border: none;
  background-color: rgba(25, 212, 118, 0.9);
  cursor: pointer;
}

/* Lista */
.results {
  list-style: none;
  padding: 0;
  margin-top: 0.75rem;
}

.result-item {
  padding: 0.6rem;
  cursor: pointer;
  border-bottom: 1px solid var(--surface-border);
}

.result-item:hover {
  background: var(--surface-hover);
}

.values {
  font-size: 0.85rem;
  color: var(--text-color-secondary);
}

/* Jobb oldali kártya */
.food-card {
  padding: 1rem;
  border-radius: 14px;
  background: var(--surface-0);
  border: 1px solid color-mix(
    in srgb,
    var(--surface-border) 60%,
    var(--primary-color) 40%
  );
  box-shadow:
    0 6px 18px rgba(0, 0, 0, 0.35),
    0 0 0 1px rgba(255, 255, 255, 0.05);
}

.food-card h3 {
  margin-bottom: 0.75rem;
}

.macro {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.4rem;
}

</style>
