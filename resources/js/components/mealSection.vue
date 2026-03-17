<script setup>
import { computed, ref } from 'vue';
import Button from '@/Components/button.vue';
import FoodCard from '@/Components/foodCard.vue';
import { getActiveLanguage } from 'laravel-vue-i18n';

const props = defineProps({
  mealConfig: Object,
  foods: Array
});

defineEmits(['delete', 'add-click']);

const mealTotal = computed(() => {
  return props.foods?.reduce((acc, f) => acc + Number(f.pivot.calorie), 0) || 0;
});


const getDisplayName = (item) => {
  if (getActiveLanguage() === 'hu' && item.name_hu) {
    return item.name_hu;
  }
  return item.name;
};
</script>

<template>
  <div
    class="overflow-hidden bg-neutral-dark/40 backdrop-blur-md border border-neutral-border rounded-2xl transition-all hover:border-primary/30">

    <div class="px-6 py-4 flex justify-between items-center bg-background-dark/40 border-b border-neutral-border">
      <h2 class="text-lg font-bold flex items-center gap-3 text-main-text">
        <span class="material-symbols-outlined" :class="mealConfig.color">{{ mealConfig.icon }}</span>
        {{ mealConfig.label }}
      </h2>
      <p class="text-sm font-black text-primary" v-if="foods?.length">
        {{ mealTotal }} <span class="text-[10px] uppercase tracking-tighter opacity-70">{{ $t('foodDiary.kcal_unit')
          }}</span>
      </p>
    </div>

    <div class="divide-y divide-neutral-border/50">
      <FoodCard v-for="food in foods" :key="food.pivot.id" :food="{
        ...food,
        calorie: food.pivot.calorie,
        protein: food.pivot.protein,
        carb: food.pivot.carb,
        fat: food.pivot.fat
      }">
        <template #action>
          <div class="flex items-center gap-4 shrink-0 px-2">
            <button @click="$emit('delete', food.pivot.id)"
              class="w-10 h-10 flex items-center justify-center rounded-full text-secondary-text/30 hover:text-red-400 hover:bg-red-400/10 transition-all active:scale-90">
              <span class="material-symbols-outlined text-xl">delete</span>
            </button>
          </div>
        </template>

      </FoodCard>
    </div>

    <div v-if="!foods?.length" class="p-10 text-center border-t border-neutral-border/30">
      <div class="material-symbols-outlined text-neutral-border text-4xl mb-2">set_meal</div>
      <p class="text-secondary-text text-sm italic opacity-60">"{{ $t('foodDiary.no_entries') }}"</p>
    </div>
  </div>
</template>