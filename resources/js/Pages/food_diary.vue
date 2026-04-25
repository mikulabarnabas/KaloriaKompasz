<script setup>
import { computed, ref, watch } from "vue";
import axios from "axios";

import AppLayout from "@/Layouts/appLayout.vue";
import MacroSummary from "@/Components/macroSummary.vue";
import MealSection from "@/Components/mealSection.vue";
import Search from "@/Components/foodSearch.vue"
import AddFoodEntry from "@/Components/addFoodEntry.vue";
import AddFood from "@/Components/addFood.vue";
import DateNavigator from "@/Components/dateNavigator.vue"
import { trans as t } from 'laravel-vue-i18n';
import Button from "@/Components/button.vue"

defineOptions({ layout: AppLayout });

const props = defineProps({
  hasProfile: Boolean,
  targets: Object,
});

const mealTypeOptions = computed(() => [
  { label: t('foodDiary.breakfast'), value: "breakfast", icon: 'light_mode', color: 'text-orange-400' },
  { label: t('foodDiary.lunch'), value: "lunch", icon: 'wb_sunny', color: 'text-primary' },
  { label: t('foodDiary.dinner'), value: "dinner", icon: 'dark_mode', color: 'text-indigo-400' },
  { label: t('foodDiary.snack'), value: "snack", icon: 'styler', color: 'text-pink-400' },
]);

const selectedDate = ref(new Date());
const formattedDate = computed(() => selectedDate.value.toISOString().slice(0, 10));
const entries = ref([]);
const dailyTotals = ref([]);

const isEntryModalOpen = ref(false);
const isCreateModalOpen = ref(false);
const selectedFoodForEntry = ref(null);

const isLoading = ref(false);

const fetchDiary = async () => {
  isLoading.value = true;
  const { data } = await axios.get(`/fdiary/diary/${formattedDate.value}`);
  entries.value = data.diary ?? [];
  dailyTotals.value = data.totals ?? [];
  isLoading.value = false;
};

const onFoodSelect = (food) => {
    console.log(food)
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
  <div class="bg-background-dark text-main-text relative min-h-screen">

    <main class="flex flex-col min-w-0">
      <header class="p-6 border-b border-neutral-border bg-background-dark/60">
        <div
          class="max-w-4xl mx-auto flex flex-wrap lg:flex-nowrap items-center justify-center lg:justify-between gap-4">

          <Search class="w-full lg:flex-1 order-1" :placeholder="$t('foodDiary.search_placeholder')"
            @select="onFoodSelect" />

          <div class="flex flex-wrap sm:flex-nowrap items-center justify-center gap-4 order-2 w-full lg:w-auto">
            <DateNavigator v-model="selectedDate" class="shrink-0" />

            <Button :label="$t('foodDiary.create_food_title')" icon="add_circle" @click="openCreateFoodModal"
              class="w-full sm:w-48 h-14! shrink-0" />

          </div>

        </div>
      </header>

      <div class="relative p-6 pb-32 min-h-100">

        <div v-if="isLoading"
          class="absolute inset-0 z-50 flex flex-col items-center pt-20 bg-background-dark/10 backdrop-blur-[2px] transition-all duration-300">
          <div class="w-16 h-16 border-4 border-primary/10 border-t-primary rounded-full animate-spin"></div>
          <p class="mt-4 text-[10px] font-black uppercase tracking-[0.5em] text-primary">{{ $t('foodDiary.update') }}</p>
        </div>

        <div class="max-w-4xl mx-auto space-y-10 transition-all duration-500"
          :class="[isLoading ? 'opacity-30 blur-md pointer-events-none' : 'opacity-100 blur-0']">

          <div class="animate-fly-in" style="animation-delay: 100ms">
            <MacroSummary :targets="targets" :stats="dailyTotals"></MacroSummary>
          </div>

          <div class="space-y-6 animate-fly-in" style="animation-delay: 200ms">
            <div class="flex items-center gap-4 mb-2">
              <h3 class="text-xs font-black uppercase tracking-[0.2em] text-secondary-text">
                {{ t('foodDiary.meal_log_title') }}
              </h3>
              <div class="h-px flex-1 bg-neutral-border/50"></div>
            </div>

            <MealSection v-for="meal in mealTypeOptions" :key="meal.value" :meal-config="meal"
              :foods="entries[meal.value]" @delete="deleteEntry" @add-click="search = ''" />
          </div>

        </div>
      </div>
    </main>

    <AddFoodEntry :show="isEntryModalOpen" :food="selectedFoodForEntry" :date="formattedDate"
      :meal-types="mealTypeOptions" @close="isEntryModalOpen = false" @saved="onSaved" />

    <AddFood :show="isCreateModalOpen" :date="formattedDate" :meal-types="mealTypeOptions"
      @close="isCreateModalOpen = false" @saved="onSaved" />

  </div>
</template>
