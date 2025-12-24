<script setup>
import { ref, computed, watch } from 'vue';

import Navbar from "../components/navbar.vue";
import Footer from "../components/footer.vue";

import Paginator from 'primevue/paginator';
import Divider from 'primevue/divider';

const search = ref('')
const first = ref(0)
const rows = ref(3) // találatok oldalanként

const foods = ref([
  //Húsok
  { name: 'Csirkemell (sült)', calories: 165, fat: 3.6, protein: 31 },
  { name: 'Pulykamell', calories: 135, fat: 1.2, protein: 30 },
  { name: 'Marhahús (sovány)', calories: 250, fat: 15, protein: 26 },
  { name: 'Sertéskaraj', calories: 242, fat: 14, protein: 27 },
  { name: 'Lazac', calories: 208, fat: 13, protein: 20 },
  { name: 'Tonhal (konzerv, víz)', calories: 116, fat: 1, protein: 26 },

  //Tojás, tejtermék
  { name: 'Tojás', calories: 155, fat: 11, protein: 13 },
  { name: 'Tojásfehérje', calories: 52, fat: 0.2, protein: 11 },
  { name: 'Tehéntúró', calories: 98, fat: 4.3, protein: 11 },
  { name: 'Görög joghurt', calories: 59, fat: 0.4, protein: 10 },
  { name: 'Sajt (trappista)', calories: 356, fat: 28, protein: 25 },

  //Köretek, gabonák
  { name: 'Rizs (főtt)', calories: 130, fat: 0.3, protein: 2.7 },
  { name: 'Barna rizs (főtt)', calories: 123, fat: 1, protein: 2.6 },
  { name: 'Bulgur (főtt)', calories: 83, fat: 0.2, protein: 3.1 },
  { name: 'Zabpehely', calories: 389, fat: 6.9, protein: 16.9 },
  { name: 'Teljes kiőrlésű kenyér', calories: 247, fat: 4.2, protein: 13 },

  //Zöldségek
  { name: 'Burgonya (főtt)', calories: 87, fat: 0.1, protein: 1.9 },
  { name: 'Édesburgonya', calories: 86, fat: 0.1, protein: 1.6 },
  { name: 'Brokkoli', calories: 34, fat: 0.4, protein: 2.8 },
  { name: 'Sárgarépa', calories: 41, fat: 0.2, protein: 0.9 },
  { name: 'Paradicsom', calories: 18, fat: 0.2, protein: 0.9 },

  //Gyümölcsök
  { name: 'Alma', calories: 52, fat: 0.2, protein: 0.3 },
  { name: 'Banán', calories: 89, fat: 0.3, protein: 1.1 },
  { name: 'Narancs', calories: 47, fat: 0.1, protein: 0.9 },
  { name: 'Eper', calories: 32, fat: 0.3, protein: 0.7 },
  { name: 'Szőlő', calories: 69, fat: 0.2, protein: 0.7 },

  //Egyéb
  { name: 'Mandula', calories: 579, fat: 50, protein: 21 },
  { name: 'Dió', calories: 654, fat: 65, protein: 15 },
  { name: 'Olívaolaj', calories: 884, fat: 100, protein: 0 },
  { name: 'Étcsokoládé', calories: 546, fat: 31, protein: 4.9 }
])


const filteredFoods = computed(() => {
  if (!search.value) return []
  return foods.value.filter(food =>
    food.name.toLowerCase().includes(search.value.toLowerCase())
  )
})

const paginatedFoods = computed(() => {
  return filteredFoods.value.slice(
    first.value,
    first.value + rows.value
  )
})

watch(search, () => {
  first.value = 0 // kereséskor vissza az első oldalra
})

const selectFood = (food) => {
  alert(`Kiválasztva: ${food.name}`)
}
</script>

<template>
  <div class="page">
    <Navbar />

    <main class="content">
      <div class="food-search">
        <!-- 🔍 Kereső -->
        <div class="search-wrapper">
          <input
            v-model="search"
            type="text"
            placeholder="Keress ételt..."
            class="search-input"
          />
          <button class="search-button">🔍</button>
        </div>

        <!-- 📋 Találatok -->
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
              🧈 {{ food.fat }} g zsír |
              💪 {{ food.protein }} g fehérje
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
    </main>
    
    <Divider />
    <Footer></Footer>
  </div>
</template>


<style scoped>
.food-search {
  max-width: 420px;
  margin: 2rem auto;
  padding: 1rem;

  background: var(--surface-0);
  border-radius: 14px;

  border: 1px solid color-mix(
    in srgb,
    var(--surface-border) 60%,
    var(--text-color) 40%
  );

  box-shadow:
    0 4px 12px rgba(0, 0, 0, 0.25),
    0 0 0 1px rgba(255, 255, 255, 0.05);
}

/* 🔍 kereső */
.search-wrapper {
  display: flex;
  gap: 0.5rem;
}

.search-input {
  flex: 1;
  padding: 0.65rem 0.75rem;
  font-size: 1rem;

  border-radius: 10px;
  border: 1px solid color-mix(
    in srgb,
    var(--surface-border) 50%,
    var(--text-color) 50%
  );

  background: var(--surface-0);
  background-color: rgba(25, 212, 118, 0.750);
  color: var(--text-color);
  outline: none;
}

.search-input::placeholder {
  color: var(--text-color-secondary);
}

.search-input:focus {
  border-color: var(--primary-color);
  box-shadow: 0 0 0 2px
    color-mix(in srgb, var(--primary-color) 35%, transparent);
}

/* 🔍 gomb */
.search-button {
  padding: 0 1rem;
  font-size: 1.1rem;

  border-radius: 10px;
  border: 1px solid color-mix(
    in srgb,
    var(--surface-border) 50%,
    var(--text-color) 50%
  );

  background: var(--surface-0);
  color: var(--text-color);
  cursor: pointer;
}

.search-button:hover {
  background: var(--surface-hover);
}

/* 📋 lista */
.results {
  list-style: none;
  padding: 0;
  margin-top: 0.75rem;
}

.result-item {
  padding: 0.65rem;
  border-bottom: 1px solid var(--surface-border);
  cursor: pointer;
}

.result-item:hover {
  background: var(--surface-hover);
}

.values {
  margin-top: 0.25rem;
  font-size: 0.85rem;
  color: var(--text-color-secondary);
}

/* 📄 paginator */
.paginator {
  margin-top: 1rem;
}

.page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.content {
  flex: 1;
  display: flex;
  align-items: flex-start;
}

</style>
