<script setup>
import { ref, computed } from 'vue'
import Navbar from "../components/navbar.vue";

const search = ref('')

const foods = ref([
  {
    name: 'Csirkemell',
    calories: 165,
    fat: 3.6,
    protein: 31
  },
  {
    name: 'Rizs (főtt)',
    calories: 130,
    fat: 0.3,
    protein: 2.7
  },
  {
    name: 'Tojás',
    calories: 155,
    fat: 11,
    protein: 13
  },
  {
    name: 'Alma',
    calories: 52,
    fat: 0.2,
    protein: 0.3
  },
  {
    name: 'Golden alma',
    calories: 31,
    fat: 0,
    protein: 0.4
  }
])

const filteredFoods = computed(() => {
  if (!search.value) return []
  return foods.value.filter(food =>
    food.name.toLowerCase().includes(search.value.toLowerCase())
  )
})

const selectFood = (food) => {
  alert(`Kiválasztva: ${food.name}`)
}
</script>

<template>

    <Navbar></Navbar>
  <div class="food-search">
    <input
      v-model="search"
      type="text"
      placeholder="Keress ételt..."
      class="search-input"
    />

    <ul v-if="filteredFoods.length" class="results">
      <li
        v-for="food in filteredFoods"
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
  </div>
</template>

<style scoped>
.food-search {
  max-width: 400px;
  margin: 2rem auto;
  padding: 1rem;

  border: 1px solid color-mix(in srgb,
    var(--surface-border) 60%,
    var(--text-color) 40%
  );

  border-radius: 12px;
  background: var(--surface-0);

  box-shadow:
    0 4px 10px rgba(0, 0, 0, 0.25),
    0 0 0 1px rgba(255, 255, 255, 0.05);
}


.search-input {
  width: 100%;
  padding: 0.6rem;
  font-size: 1rem;

  border: 1px solid color-mix(in srgb,
    var(--surface-border) 50%,
    var(--text-color) 50%
  );

  border-radius: 8px;
  background: var(--surface-0);
  color: var(--text-color);
  outline: none;
}

.search-input:focus {
  border-color: var(--primary-color);
  box-shadow: 0 0 0 2px
    color-mix(in srgb, var(--primary-color) 40%, transparent);
}


.search-input::placeholder {
  color: var(--text-color-secondary);
}

.results {
  list-style: none;
  padding: 0;
  margin-top: 0.75rem;
  border-top: 1px solid var(--surface-border);
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


</style>
