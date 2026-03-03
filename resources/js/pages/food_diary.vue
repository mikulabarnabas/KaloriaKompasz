<script setup>
import { computed, ref, watch } from "vue";
import axios from "axios";
import { useI18n } from 'vue-i18n';

import AppLayout from "@/Layouts/AppLayout.vue";
import MacroSummary from "@/Components/macroSummary.vue";
import MealSection from "@/Components/mealSection.vue";
import Search from "@/Components/search.vue"
import AddEntryOverlay from "@/Components/addEntryOverlay.vue";
import AddFoodOverlay from "@/Components/addFoodOverlay.vue";

const { t } = useI18n();
defineOptions({ layout: AppLayout });

const mealTypeOptions = [
  { label: t('foodDiary.breakfast'), value: "breakfast", icon: 'light_mode', color: 'text-orange-400' },
  { label: t('foodDiary.lunch'), value: "lunch", icon: 'wb_sunny', color: 'text-primary' },
  { label: t('foodDiary.dinner'), value: "dinner", icon: 'dark_mode', color: 'text-indigo-400' },
  { label: t('foodDiary.snack'), value: "snack", icon: 'styler', color: 'text-pink-400' },
];

const selectedDate = ref(new Date());
const formattedDate = computed(() => selectedDate.value.toISOString().slice(0, 10));
const entries = ref([]);

const isEntryModalOpen = ref(false);
const isCreateModalOpen = ref(false);
const selectedFoodForEntry = ref(null);

const dailyTotals = computed(() => {
  let totals = { kcal: 0, protein: 0, carbs: 0, fats: 0 };
  Object.values(entries.value).flat().forEach(food => {
    totals.kcal += Number(food.pivot.calorie || 0);
    totals.protein += Number(food.pivot.protein || 0);
    totals.carbs += Number(food.pivot.carb || 0);
    totals.fats += Number(food.pivot.fat || 0);
  });
  return totals;
});

const fetchDiary = async () => {
  const { data } = await axios.get(`/fdiary/diary/${formattedDate.value}`);
  entries.value = data.diary ?? [];
};

const onFoodSelect = (food) => {
  selectedFoodForEntry.value = food;
  isEntryModalOpen.value = true;
};

const openCreateFoodModal = () => {
  isCreateModalOpen.value = true;
};

const onSaved = () => {
  fetchDiary();
  isCreateModalOpen.value = false;
  isEntryModalOpen.value = false;
};

const deleteEntry = async (entryId) => {
  await axios.delete(`/fdiary/entry/${formattedDate.value}/${entryId}`);
  fetchDiary();
};

watch(formattedDate, fetchDiary, { immediate: true });
</script>

<template>
  <div class="bg-background-dark text-white relative min-h-screen">

    <main class="flex flex-col min-w-0">
      <header class="p-6 border-b border-primary/10 bg-background-dark/50 backdrop-blur-md sticky top-0 z-40">
        <div class="max-w-4xl mx-auto flex gap-4 items-center">

          <Search class="flex-1" :placeholder="$t('foodDiary.search_placeholder')" @select="onFoodSelect" />

          <button @click="openCreateFoodModal"
            class="flex items-center gap-2 px-4 py-3 bg-primary/10 border border-primary/20 rounded-xl text-primary hover:bg-primary/20 transition-all active:scale-95 whitespace-nowrap">
            <span class="material-symbols-outlined text-xl">add_circle</span>
            <span class="hidden sm:inline font-medium text-sm">
              {{ $t('foodDiary.create_food_title') }}
            </span>
          </button>

        </div>
      </header>

      <div class="p-6 space-y-8 pb-32">
        <div class="max-w-4xl mx-auto space-y-8">

          <MacroSummary :totals="dailyTotals" :goal="2500" />

          <div class="space-y-6">
            <MealSection v-for="meal in mealTypeOptions" :key="meal.value" :meal-config="meal"
              :foods="entries[meal.value]" @delete="deleteEntry" @add-click="search = ''" />
          </div>
        </div>
      </div>
    </main>

    <AddEntryOverlay :show="isEntryModalOpen" :food="selectedFoodForEntry" :date="formattedDate"
      :meal-types="mealTypeOptions" @close="isEntryModalOpen = false" @saved="onSaved" />

    <AddFoodOverlay :show="isCreateModalOpen" :date="formattedDate" :meal-types="mealTypeOptions"
      @close="isCreateModalOpen = false" @saved="onSaved" />

    <div class="fixed bottom-6 right-6 w-72 z-50 hidden lg:block">
    </div>
  </div>
</template>