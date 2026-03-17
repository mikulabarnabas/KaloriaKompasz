<script setup>
import { computed } from 'vue';
import { getActiveLanguage } from "laravel-vue-i18n";

const props = defineProps({
  food: { type: Object, required: true },
  clickable: { type: Boolean, default: false },
  border: { type: Boolean, default: false }
});

const displayName = computed(() => {
  if (getActiveLanguage() === 'hu' && props.food.name_hu) {
    return props.food.name_hu;
  }
  return props.food.name;
});
</script>

<template>
  <div 
    class="w-full flex items-center gap-4 p-4 transition-all group/food-card"
    :class="[
      clickable ? 'hover:bg-primary/10 cursor-pointer' : '',
      border ? 'border border-neutral-border rounded-2xl bg-neutral-dark/10' : ''
    ]"
  >
    <div
      class="w-12 h-12 rounded-xl bg-neutral-dark shrink-0 overflow-hidden border border-neutral-border group-hover/food-card:border-primary/50 transition-colors"
    >
      <img v-if="food.image" :src="food.image" class="w-full h-full object-cover" />
      <div v-else class="w-full h-full flex items-center justify-center bg-primary/10 text-primary">
        <span class="material-symbols-outlined text-xl">restaurant</span>
      </div>
    </div>

    <div class="flex-1 min-w-0">
      <h4 class="text-main-text font-bold truncate group-hover/food-card:text-primary transition-colors">
        {{ displayName }}
      </h4>
      <p class="text-[12px] font-bold text-secondary-text uppercase tracking-tight truncate">
        <span class="text-primary">{{ food.calorie }} kcal</span> 
        <span class="mx-1">•</span> {{ food.protein }}g {{ $t('foodDiary.protein_short') }} 
        <span class="mx-1">•</span> {{ food.carb }}g {{ $t('foodDiary.carb_short') }} 
        <span class="mx-1">•</span> {{ food.fat }}g {{ $t('foodDiary.fat_short') }}
      </p>
    </div>

    <slot name="action"></slot>
  </div>
</template>