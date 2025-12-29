<script setup>
import { ref, computed, watch } from "vue";
import { usePage, router } from "@inertiajs/vue3";

import Navbar from "../components/navbar.vue";
import Footer from "../components/footer.vue";

import Paginator from "primevue/paginator";
import Divider from "primevue/divider";

import FloatLabel from "primevue/floatlabel";
import InputText from "primevue/inputtext";
import Button from "primevue/button";
import Dialog from "primevue/dialog";
import FileUpload from "primevue/fileupload";

import { useForm } from "laravel-precognition-vue";

const search = ref("");
const first = ref(0);
const rows = ref(3);

const selectedFood = ref(null);

const page = usePage();
const foods = ref(page.props.foods ?? []);

// ---- Search / list ----
const filteredFoods = computed(() => {
  const q = search.value.trim().toLowerCase();
  if (!q) return [];
  return foods.value.filter((food) =>
    (food.name ?? "").toLowerCase().includes(q)
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
const showSuccessDialog = ref(false);

const createFoodForm = useForm("post", "/fdiary/create", {
  name: "",
  fat_g: 0,
  carbs_g: 0,
  protein_g: 0,
  calories: 0,
  notes: "",
  image: null,
});

const onSelectImage = (event) => {
  createFoodForm.image = event.files?.[0] ?? null;
};

const onRemoveImage = () => {
  createFoodForm.image = null;
};

const onCreateFood = () =>
  createFoodForm
    .submit({
      forceFormData: true,
    })
    .then(() => {
      createFoodForm.reset();
      onRemoveImage();
      showSuccessDialog.value = true;

      router.reload({ only: ["foods"] });
    })
    .catch(() => { });

const imageSrc = (food) => {
  if (!food?.image_path) return null;
  return `/storage/${food.image_path}`;
};
</script>

<template>
  <div class="min-h-screen">
    <Navbar />

    <main class="mx-auto w-full max-w-7xl px-4 py-6">
      <!-- 3x1 grid -->
      <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <section class="rounded-2xl border p-5">
          <h2 class="text-lg font-semibold">Keresés</h2>
          <p class="mt-1 text-sm">Keress név alapján.</p>

          <div class="mt-4 flex gap-2">
            <input v-model="search" type="text" placeholder="Keress ételt..."
              class="w-full rounded-lg border px-3 py-2 text-sm outline-none" />
            <button type="button" class="rounded-lg border px-3 py-2 text-sm">
              🔍
            </button>
          </div>

          <div class="mt-4">
            <ul v-if="paginatedFoods.length" class="space-y-2">
              <li v-for="food in paginatedFoods" :key="food.id ?? food.name" @click="selectFood(food)"
                class="cursor-pointer rounded-xl border p-3">
                <div class="flex items-start justify-between gap-3">
                  <div class="min-w-0">
                    <div class="truncate font-semibold">
                      {{ food.name }}
                    </div>
                    <div class="mt-1 text-xs">
                      {{ food.calories }} kcal · Zsír {{ food.fat_g }} g · Fehérje
                      {{ food.protein_g }} g
                    </div>
                  </div>

                  <div class="shrink-0 rounded-full border px-2 py-1 text-xs">
                    #{{ food.id }}
                  </div>
                </div>
              </li>
            </ul>

            <div v-else class="rounded-xl border border-dashed p-6 text-sm">
              Írj be valamit a kereséshez.
            </div>

            <div class="mt-4">
              <Paginator v-if="filteredFoods.length > rows" :first="first" :rows="rows"
                :totalRecords="filteredFoods.length" :pageLinkSize="4" @page="(e) => (first = e.first)" />
            </div>
          </div>
        </section>

        <!-- RIGHT: Selected food -->
        <section class="rounded-2xl border p-5">
          <h2 class="text-lg font-semibold">Kiválasztott étel</h2>
          <p class="mt-1 text-sm">Kattints egy ételre középen.</p>

          <div v-if="selectedFood" class="mt-4 rounded-xl border p-4">
            <div class="flex items-start justify-between gap-3">
              <div>
                <div class="text-xl font-semibold">{{ selectedFood.name }}</div>
                <div class="mt-1 text-xs">ID: {{ selectedFood.id }}</div>
              </div>
            </div>

            <img v-if="selectedFood.image_path" :src="imageSrc(selectedFood)"
              class="mt-4 h-44 w-full rounded-xl border object-cover" alt="Food image" />

            <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
              <div class="rounded-lg border p-3">
                <div class="text-xs">Kalória</div>
                <div class="mt-1 font-semibold">
                  {{ selectedFood.calories }} kcal
                </div>
              </div>

              <div class="rounded-lg border p-3">
                <div class="text-xs">Zsír</div>
                <div class="mt-1 font-semibold">{{ selectedFood.fat_g }} g</div>
              </div>

              <div class="rounded-lg border p-3">
                <div class="text-xs">Szénhidrát</div>
                <div class="mt-1 font-semibold">
                  {{ selectedFood.carbs_g }} g
                </div>
              </div>

              <div class="rounded-lg border p-3">
                <div class="text-xs">Fehérje</div>
                <div class="mt-1 font-semibold">
                  {{ selectedFood.protein_g }} g
                </div>
              </div>
            </div>

            <div v-if="selectedFood.notes" class="mt-4 rounded-lg border p-3">
              <div class="text-xs font-medium">Megjegyzés</div>
              <div class="mt-1 whitespace-pre-wrap text-sm">
                {{ selectedFood.notes }}
              </div>
            </div>
          </div>

          <div v-else class="mt-4 rounded-xl border border-dashed p-6 text-sm">
            Nincs kiválasztott étel.
          </div>
        </section>

        <section class="rounded-2xl border p-5">
          <h2 class="text-lg font-semibold">Új étel hozzáadása</h2>
          <p class="mt-1 text-sm">Add meg a makrókat és opcionálisan egy képet.</p>

          <form class="mt-5 space-y-4" @submit.prevent="onCreateFood" novalidate>
            <div class="space-y-1">
              <FloatLabel variant="on">
                <InputText id="food_name" v-model="createFoodForm.name" class="w-full"
                  @change="createFoodForm.validate('name')" />
                <label for="food_name">Név</label>
              </FloatLabel>

              <small v-if="createFoodForm.invalid('name')" class="block text-xs">
                {{ createFoodForm.errors.name }}
              </small>
            </div>

            <div class="space-y-1">
              <FloatLabel variant="on">
                <InputText id="calories" v-model="createFoodForm.calories" type="number" inputmode="numeric"
                  class="w-full" @change="createFoodForm.validate('calories')" />
                <label for="calories">Kalória (kcal)</label>
              </FloatLabel>

              <small v-if="createFoodForm.invalid('calories')" class="block text-xs">
                {{ createFoodForm.errors.calories }}
              </small>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 lg:grid-cols-1">
              <div class="space-y-1">
                <FloatLabel variant="on">
                  <InputText id="fat_g" v-model="createFoodForm.fat_g" type="number" inputmode="numeric" class="w-full"
                    @change="createFoodForm.validate('fat_g')" />
                  <label for="fat_g">Zsír (g)</label>
                </FloatLabel>

                <small v-if="createFoodForm.invalid('fat_g')" class="block text-xs">
                  {{ createFoodForm.errors.fat_g }}
                </small>
              </div>

              <div class="space-y-1">
                <FloatLabel variant="on">
                  <InputText id="carbs_g" v-model="createFoodForm.carbs_g" type="number" inputmode="numeric"
                    class="w-full" @change="createFoodForm.validate('carbs_g')" />
                  <label for="carbs_g">Szénhidrát (g)</label>
                </FloatLabel>

                <small v-if="createFoodForm.invalid('carbs_g')" class="block text-xs">
                  {{ createFoodForm.errors.carbs_g }}
                </small>
              </div>

              <div class="space-y-1">
                <FloatLabel variant="on">
                  <InputText id="protein_g" v-model="createFoodForm.protein_g" type="number" inputmode="numeric"
                    class="w-full" @change="createFoodForm.validate('protein_g')" />
                  <label for="protein_g">Fehérje (g)</label>
                </FloatLabel>

                <small v-if="createFoodForm.invalid('protein_g')" class="block text-xs">
                  {{ createFoodForm.errors.protein_g }}
                </small>
              </div>
            </div>

            <div class="space-y-1">
              <FloatLabel variant="on">
                <InputText id="notes" v-model="createFoodForm.notes" class="w-full"
                  @change="createFoodForm.validate('notes')" />
                <label for="notes">Megjegyzés (opcionális)</label>
              </FloatLabel>

              <small v-if="createFoodForm.invalid('notes')" class="block text-xs">
                {{ createFoodForm.errors.notes }}
              </small>
            </div>

            <div class="space-y-2">
              <div class="text-sm font-medium">Kép (opcionális)</div>

              <FileUpload mode="basic" name="image" accept="image/*" :maxFileSize="4_000_000"
                chooseLabel="Kép kiválasztása" customUpload @select="onSelectImage" @clear="onRemoveImage"
                class="w-full" />

              <small v-if="createFoodForm.invalid('image')" class="block text-xs">
                {{ createFoodForm.errors.image }}
              </small>

              <div v-if="createFoodForm.image" class="text-xs">
                Selected: {{ createFoodForm.image.name }}
              </div>
            </div>

            <Button type="submit" label="Étel mentése" class="w-full" :disabled="createFoodForm.processing" />
          </form>
        </section>
      </div>

      <Divider class="my-8" />

      <Dialog v-model:visible="showSuccessDialog" modal :closable="true" :draggable="false" header="Siker"
        class="w-[92vw] max-w-md">
        <p>Az étel sikeresen mentve lett.</p>

        <template #footer>
          <div class="flex w-full justify-end gap-2">
            <Button label="Close" severity="secondary" @click="showSuccessDialog = false" />
          </div>
        </template>
      </Dialog>
    </main>

    <Footer />
  </div>
</template>

<style scoped></style>