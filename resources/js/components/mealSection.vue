<script setup>
import { computed } from 'vue';

const props = defineProps({
  mealConfig: Object,
  foods: Array
});

defineEmits(['delete', 'add-click']);

const mealTotal = computed(() => {
  return props.foods?.reduce((acc, f) => acc + Number(f.pivot.calorie), 0) || 0;
});
</script>

<template>
  <div class="bg-white/5 border border-white/10 rounded-2xl transition-all hover:border-white/20">
    <div class="px-6 py-4 flex justify-between items-center bg-white/5 border-b border-white/5">
      <h2 class="text-lg font-bold flex items-center gap-3">
        <span class="material-symbols-outlined" :class="mealConfig.color">{{ mealConfig.icon }}</span>
        {{ mealConfig.label }}
      </h2>
      <p class="text-sm font-medium text-primary" v-if="foods?.length">
        {{ mealTotal }} kcal
      </p>
    </div>

    <div class="divide-y divide-white/5">
      <div v-for="food in foods" :key="food.pivot_id"
        class="px-6 py-4 flex items-center justify-between group hover:bg-white/5 transition-colors">
        <div class="flex items-center gap-4">
          <div class="size-12 rounded-xl bg-slate-800 flex items-center justify-center text-xl">🥗</div>
          <div>
            <h4 class="font-bold text-slate-100">{{ food.name }}</h4>
            <div class="flex gap-2 mt-1">
              <span class="text-[9px] px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/20">P: {{ food.pivot.protein }}g</span>
              <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20">C: {{ food.pivot.carb }}g</span>
              <span class="text-[9px] px-2 py-0.5 rounded-full bg-purple-500/10 text-purple-400 border border-purple-500/20">F: {{ food.pivot.fat }}g</span>
            </div>
          </div>
        </div>
        <div class="flex items-center gap-6">
          <p class="text-sm font-black">{{ food.pivot.calorie }} <span class="text-[10px] font-normal text-slate-500">kcal</span></p>
          <button @click="$emit('delete', food.pivot.id)" class="text-slate-600 hover:text-red-400 transition-colors">
            <span class="material-symbols-outlined text-lg">delete</span>
          </button>
        </div>
      </div>
    </div>

    <div v-if="!foods?.length" class="p-8 text-center">
      <p class="text-slate-500 text-sm italic">"{{ $t('foodDiary.no_selected_food') }}"</p>
    </div>
  </div>
</template>