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
  // 🔥 Saját testsúlyos
  {
    name: 'Fekvőtámasz',
    muscle: 'Mell, tricepsz, váll',
    difficulty: 'Közepes',
    calories: 8,
    description: 'Alap felsőtest erősítő gyakorlat.',
    variations: ['Szűk', 'Széles', 'Emelt lábas']
  },
  {
    name: 'Guggolás',
    muscle: 'Comb, farizom',
    difficulty: 'Könnyű',
    calories: 7,
    description: 'Alsótest alapgyakorlat saját testsúllyal.',
    variations: ['Sumo', 'Ugró', 'Egy lábas']
  },
  {
    name: 'Kitörés',
    muscle: 'Comb, farizom',
    difficulty: 'Közepes',
    calories: 8,
    description: 'Egyensúlyt és láberőt fejleszt.',
    variations: ['Előre', 'Hátra', 'Oldalra']
  },
  {
    name: 'Plank',
    muscle: 'Törzs',
    difficulty: 'Közepes',
    calories: 5,
    description: 'Statikus törzserősítő gyakorlat.',
    variations: ['Oldalsó', 'Emelt lábas']
  },
  {
    name: 'Burpee',
    muscle: 'Teljes test',
    difficulty: 'Nehéz',
    calories: 12,
    description: 'Magas intenzitású teljes testes gyakorlat.',
    variations: ['Ugrással', 'Fekvőtámasszal']
  },
  {
    name: 'Mountain climber',
    muscle: 'Törzs, láb',
    difficulty: 'Közepes',
    calories: 10,
    description: 'Gyors tempójú kardió gyakorlat.',
    variations: ['Gyors', 'Keresztirányú']
  },

  // 💪 Súlyzós
  {
    name: 'Fekvenyomás',
    muscle: 'Mell, tricepsz',
    difficulty: 'Közepes',
    calories: 9,
    description: 'Alap mellgyakorlat rúddal.',
    variations: ['Egyenes pad', 'Ferde pad']
  },
  {
    name: 'Bicepsz curl',
    muscle: 'Bicepsz',
    difficulty: 'Könnyű',
    calories: 6,
    description: 'Karerősítő gyakorlat súlyzóval.',
    variations: ['Kalapács', 'Koncentrált']
  },
  {
    name: 'Tricepsz lenyomás',
    muscle: 'Tricepsz',
    difficulty: 'Könnyű',
    calories: 6,
    description: 'Tricepsz izolációs gyakorlat.',
    variations: ['Köteles', 'Egykezes']
  },
  {
    name: 'Vállból nyomás',
    muscle: 'Váll',
    difficulty: 'Közepes',
    calories: 8,
    description: 'Vállerősítő alapgyakorlat.',
    variations: ['Ülve', 'Állva']
  },
  {
    name: 'Oldalemelés',
    muscle: 'Váll',
    difficulty: 'Könnyű',
    calories: 5,
    description: 'Oldalsó deltát célozza.',
    variations: ['Egykezes', 'Kábeles']
  },
  {
    name: 'Evezés rúddal',
    muscle: 'Hát',
    difficulty: 'Közepes',
    calories: 9,
    description: 'Hátizom vastagítás.',
    variations: ['Széles', 'Szűk fogás']
  },
  {
    name: 'Lehúzás mellhez',
    muscle: 'Hát',
    difficulty: 'Könnyű',
    calories: 7,
    description: 'Széles hátizmot dolgoztat.',
    variations: ['Széles', 'Fordított fogás']
  },

  // 🏃 Kardió
  {
    name: 'Futás',
    muscle: 'Láb, szív- és érrendszer',
    difficulty: 'Közepes',
    calories: 10,
    description: 'Állóképesség fejlesztés.',
    variations: ['Sík', 'Intervall']
  },
  {
    name: 'Kerékpározás',
    muscle: 'Láb',
    difficulty: 'Könnyű',
    calories: 8,
    description: 'Ízületkímélő kardió.',
    variations: ['Szobabicikli', 'Szabadtéri']
  },
  {
    name: 'Ugrókötél',
    muscle: 'Teljes test',
    difficulty: 'Közepes',
    calories: 12,
    description: 'Magas pulzusú kardió.',
    variations: ['Gyors', 'Váltott lábas']
  },
  {
    name: 'Evezőpad',
    muscle: 'Teljes test',
    difficulty: 'Közepes',
    calories: 11,
    description: 'Kardió és erő kombináció.',
    variations: ['Egyenletes', 'Intervall']
  },

  // 🧘 Core / mobilitás
  {
    name: 'Hasprés',
    muscle: 'Has',
    difficulty: 'Könnyű',
    calories: 5,
    description: 'Alap hasizom gyakorlat.',
    variations: ['Lábemeléssel', 'Súlyzóval']
  },
  {
    name: 'Lábemelés',
    muscle: 'Has, csípő',
    difficulty: 'Közepes',
    calories: 6,
    description: 'Alsó hasizmot célozza.',
    variations: ['Földön', 'Függeszkedve']
  },
  {
    name: 'Hiperhajlítás',
    muscle: 'Derék',
    difficulty: 'Könnyű',
    calories: 4,
    description: 'Alsó hát erősítés.',
    variations: ['Súly nélkül', 'Súlyzóval']
  },
  {
    name: 'Nyújtás',
    muscle: 'Teljes test',
    difficulty: 'Könnyű',
    calories: 3,
    description: 'Regeneráció és mobilitás.',
    variations: ['Statikus', 'Dinamikus']
  }
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
  min-width: 280px;
}

/* Wrapper */
.exercise-search {
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

/* Részletek kártya */
.details-card {
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

.desc {
  font-size: 0.9rem;
  color: var(--text-color-secondary);
}
</style>
