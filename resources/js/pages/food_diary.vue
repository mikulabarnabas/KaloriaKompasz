<script setup>
import { ref, computed, watch } from "vue";

import { usePage } from "@inertiajs/vue3";

import Navbar from "../components/navbar.vue";
import Footer from "../components/footer.vue";

import Paginator from "primevue/paginator";
import Divider from "primevue/divider";

import FloatLabel from "primevue/floatlabel";
import InputText from "primevue/inputtext";
import Button from "primevue/button";
import Dialog from "primevue/dialog";

import { useForm } from "laravel-precognition-vue";

const search = ref("");
const first = ref(0);
const rows = ref(3);

const selectedFood = ref(null);

const page = usePage();
const foods = ref(page.props.foods ?? []);

// ---- Search / list ----
const filteredFoods = computed(() => {
  if (!search.value) return [];
  return foods.value.filter((food) =>
    food.name.toLowerCase().includes(search.value.toLowerCase())
  );
});

const paginatedFoods = computed(() =>
  filteredFoods.value.slice(first.value, first.value + rows.value)
);

watch(search, () => {
  first.value = 0;
  selectedFood.value = null;
});

const selectFood = (food) => {
  selectedFood.value = food;
};

// ---- Create food form ----
// Adjust endpoint to your backend route, e.g. POST /foods
const showSuccessDialog = ref(false);

const createFoodForm = useForm("post", "/fdiary/create", {
  name: "",
  fat_g: 0,
  carbs_g: 0,
  protein_g: 0,
  calories: 0,
  notes: "",
});

const onCreateFood = () =>
  createFoodForm
    .submit()
    .then((response) => {
      // If your API returns the created food, you can push it into the list:
      // Example: const created = response?.data?.data ?? response?.data;
      // if (created) foods.value.unshift(created);

      createFoodForm.reset();
      showSuccessDialog.value = true;
    })
    .catch(() => {
      // errors are available in createFoodForm.errors
    });

function closeSuccessDialog() {
  showSuccessDialog.value = false;
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
                :key="food.id ?? food.name"
                @click="selectFood(food)"
                class="result-item"
              >
                <strong>{{ food.name }}</strong>
                <div class="values">
                  {{ food.calories }} kcal |
                  {{ food.fat_g }} g |
                  {{ food.protein_g }} g
                </div>
              </li>
            </ul>

            <Paginator
              v-if="filteredFoods.length > rows"
              :first="first"
              :rows="rows"
              :totalRecords="filteredFoods.length"
              :pageLinkSize="4"
              @page="(e) => (first = e.first)"
              class="paginator"
            />
          </div>

          <!-- ➡️ Jobb oldal -->
          <div v-if="selectedFood" class="right-panel">
            <div class="food-card">
              <h3>{{ selectedFood.name }}</h3>

              <div class="macro">
                <strong>{{ selectedFood.calories }}</strong>
                <small>kcal</small>
              </div>

              <div class="macro">
                <strong>{{ selectedFood.fat_g }}</strong>
                <small>g zsír</small>
              </div>

              <div class="macro">
                <strong>{{ selectedFood.carbs_g }}</strong>
                <small>g szénhidrát</small>
              </div>

              <div class="macro">
                <strong>{{ selectedFood.protein_g }}</strong>
                <small>g fehérje</small>
              </div>
            </div>
          </div>
        </div>

        <!-- ➕ Create new food form -->
        <Divider />

        <section class="create-food">
          <h2 class="text-xl font-semibold">Új étel hozzáadása</h2>

          <form class="mt-4 space-y-4" @submit.prevent="onCreateFood" novalidate>
            <!-- Name -->
            <div class="space-y-1">
              <FloatLabel variant="on">
                <InputText
                  id="food_name"
                  v-model="createFoodForm.name"
                  class="w-full"
                  @change="createFoodForm.validate('name')"
                />
                <label for="food_name">Név</label>
              </FloatLabel>

              <small
                v-if="createFoodForm.invalid('name')"
                class="block text-xs text-red-600"
              >
                {{ createFoodForm.errors.name }}
              </small>
            </div>

            <!-- Calories -->
            <div class="space-y-1">
              <FloatLabel variant="on">
                <InputText
                  id="calories"
                  v-model="createFoodForm.calories"
                  type="number"
                  inputmode="numeric"
                  class="w-full"
                  @change="createFoodForm.validate('calories')"
                />
                <label for="calories">Kalória (kcal)</label>
              </FloatLabel>

              <small
                v-if="createFoodForm.invalid('calories')"
                class="block text-xs text-red-600"
              >
                {{ createFoodForm.errors.calories }}
              </small>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
              <!-- Fat -->
              <div class="space-y-1">
                <FloatLabel variant="on">
                  <InputText
                    id="fat_g"
                    v-model="createFoodForm.fat_g"
                    type="number"
                    inputmode="numeric"
                    class="w-full"
                    @change="createFoodForm.validate('fat_g')"
                  />
                  <label for="fat_g">Zsír (g)</label>
                </FloatLabel>

                <small
                  v-if="createFoodForm.invalid('fat_g')"
                  class="block text-xs text-red-600"
                >
                  {{ createFoodForm.errors.fat_g }}
                </small>
              </div>

              <!-- Carbs -->
              <div class="space-y-1">
                <FloatLabel variant="on">
                  <InputText
                    id="carbs_g"
                    v-model="createFoodForm.carbs_g"
                    type="number"
                    inputmode="numeric"
                    class="w-full"
                    @change="createFoodForm.validate('carbs_g')"
                  />
                  <label for="carbs_g">Szénhidrát (g)</label>
                </FloatLabel>

                <small
                  v-if="createFoodForm.invalid('carbs_g')"
                  class="block text-xs text-red-600"
                >
                  {{ createFoodForm.errors.carbs_g }}
                </small>
              </div>

              <!-- Protein -->
              <div class="space-y-1">
                <FloatLabel variant="on">
                  <InputText
                    id="protein_g"
                    v-model="createFoodForm.protein_g"
                    type="number"
                    inputmode="numeric"
                    class="w-full"
                    @change="createFoodForm.validate('protein_g')"
                  />
                  <label for="protein_g">Fehérje (g)</label>
                </FloatLabel>

                <small
                  v-if="createFoodForm.invalid('protein_g')"
                  class="block text-xs text-red-600"
                >
                  {{ createFoodForm.errors.protein_g }}
                </small>
              </div>
            </div>

            <!-- Image path (optional) -->
            <div class="space-y-1">
              <FloatLabel variant="on">
                <InputText
                  id="notes"
                  v-model="createFoodForm.notes"
                  class="w-full"
                  @change="createFoodForm.validate('notes')"
                />
                <label for="notes">Megjegyzés (opcionális)</label>
              </FloatLabel>

              <small
                v-if="createFoodForm.invalid('notes')"
                class="block text-xs text-red-600"
              >
                {{ createFoodForm.errors.notes }}
              </small>
            </div>

            <Button
              type="submit"
              label="Étel mentése"
              class="w-full sm:w-auto"
              :disabled="createFoodForm.processing"
            />
          </form>
        </section>

        <Dialog
          v-model:visible="showSuccessDialog"
          modal
          :closable="true"
          :draggable="false"
          header="Siker"
          class="w-[92vw] max-w-md"
          @hide="closeSuccessDialog"
        >
          <p class="text-slate-700">Az étel sikeresen mentve lett.</p>

          <template #footer>
            <div class="flex w-full justify-end gap-2">
              <Button
                label="Close"
                severity="secondary"
                @click="closeSuccessDialog"
              />
            </div>
          </template>
        </Dialog>
      </div>
    </main>

    <Divider />
    <Footer />
  </div>
</template>

<style scoped></style>