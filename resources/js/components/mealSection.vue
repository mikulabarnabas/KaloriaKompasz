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
  <div
    class="overflow-hidden bg-neutral-dark/40 backdrop-blur-md border border-neutral-border rounded-2xl transition-all hover:border-primary/30">

    <div class="px-6 py-4 flex justify-between items-center bg-background-dark/40 border-b border-neutral-border">
      <h2 class="text-lg font-bold flex items-center gap-3 text-main-text">
        <span class="material-symbols-outlined" :class="mealConfig.color">{{ mealConfig.icon }}</span>
        {{ mealConfig.label }}
      </h2>
      <p class="text-sm font-black text-primary" v-if="foods?.length">
        {{ mealTotal }} <span class="text-[10px] uppercase tracking-tighter opacity-70">kcal</span>
      </p>
    </div>

    <div class="divide-y divide-neutral-border/50">
      <div v-for="food in foods" :key="food.pivot_id"
        class="px-6 py-4 flex items-center justify-between group hover:bg-primary/5 transition-colors">

        <div class="flex items-center gap-4">
          <div
            class="size-12 rounded-xl bg-background-dark border border-neutral-border flex items-center justify-center text-xl shadow-inner">
            🥗
          </div>

          <div>
            <h4 class="font-bold text-main-text group-hover:text-primary transition-colors text-sm md:text-base">
              {{ food.name }}
            </h4>
            <div class="flex gap-2 mt-1.5">
              <span
                class="text-[10px] font-mono px-2 py-0.5 rounded-md bg-blue-500/10 text-blue-400 border border-blue-500/20">P:
                {{ food.pivot.protein }}g</span>
              <span
                class="text-[10px] font-mono px-2 py-0.5 rounded-md bg-amber-500/10 text-amber-400 border border-amber-500/20">C:
                {{ food.pivot.carb }}g</span>
              <span
                class="text-[10px] font-mono px-2 py-0.5 rounded-md bg-rose-500/10 text-rose-400 border border-rose-500/20">F:
                {{ food.pivot.fat }}g</span>
            </div>
          </div>
        </div>

        <div class="flex items-center gap-6">
          <p class="text-sm font-black text-main-text">
            {{ food.pivot.calorie }}
            <span class="text-[10px] font-normal text-secondary-text">kcal</span>
          </p>

          <button @click="$emit('delete', food.pivot.id)"
            class="text-secondary-text hover:text-red-400 transition-colors group/btn">
            <span class="material-symbols-outlined text-lg">delete</span>
          </button>
        </div>
      </div>
    </div>

    <div v-if="!foods?.length" class="p-10 text-center border-t border-neutral-border/30">
      <div class="material-symbols-outlined text-neutral-border text-4xl mb-2">set_meal</div>
      <p class="text-secondary-text text-sm italic opacity-60">"{{ $t('foodDiary.no_selected_food') }}"</p>
    </div>
  </div>
</template>