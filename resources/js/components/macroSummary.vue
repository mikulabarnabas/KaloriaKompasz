<script setup>
import { computed } from 'vue';

const props = defineProps({
  totals: Object,
  goal: { type: Number, default: 2500 }
});

const macroConfig = computed(() => [
  { label: 'Protein', value: props.totals.protein },
  { label: 'Carbs', value: props.totals.carbs },
  { label: 'Fats', value: props.totals.fats },
]);
</script>

<template>
  <section class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <div class="bg-primary/10 border border-primary/20 rounded-xl p-4">
      <p class="text-[10px] font-bold text-primary uppercase tracking-wider">Remaining</p>
      <h3 class="text-xl font-bold mt-1">
        {{ goal - totals.kcal }} 
        <span class="text-xs font-normal opacity-60">kcal</span>
      </h3>
    </div>

    <div 
      v-for="macro in macroConfig" 
      :key="macro.label"
      class="bg-white/5 border border-white/10 rounded-xl p-4"
    >
      <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ macro.label }}</p>
      <h3 class="text-xl font-bold mt-1">{{ macro.value }}g</h3>
    </div>
  </section>
</template>