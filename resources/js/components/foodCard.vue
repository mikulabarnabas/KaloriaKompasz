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
  <div class="w-full flex items-center gap-4 p-4 transition-all group/food-card" :class="[
    clickable ? 'hover:bg-primary/10 cursor-pointer' : '',
    border ? 'border border-neutral-border rounded-2xl bg-neutral-dark/10' : ''
  ]">
    <div
      class="w-12 h-12 rounded-xl bg-neutral-dark shrink-0 overflow-hidden border border-neutral-border group-hover/food-card:border-primary/50 transition-colors">
      <img v-if="food.image" :src="food.image" class="w-full h-full object-cover" />
      <div v-else class="w-full h-full flex items-center justify-center bg-primary/10 text-primary">
        <span class="material-symbols-outlined text-xl">restaurant</span>
      </div>
    </div>

    <div class="flex-1 min-w-0">
      <h4 class="text-main-text font-bold truncate mb-0.5">
        {{ displayName }}
      </h4>
      <div class="flex flex-wrap items-center gap-y-0.5 gap-x-2 text-[11px] font-bold text-secondary-text uppercase">
        <span class="text-primary whitespace-nowrap">{{ food.calorie }} kcal</span>
        <div class="flex gap-2 opacity-80">
          <span class="whitespace-nowrap">{{ food.protein }}g P</span>
          <span class="whitespace-nowrap">{{ food.carb }}g CH</span>
          <span class="whitespace-nowrap">{{ food.fat }}g ZS</span>
        </div>
      </div>
    </div>

    <slot name="action"></slot>
  </div>
</template>