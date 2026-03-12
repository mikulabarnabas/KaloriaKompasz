<script setup>
import { computed } from 'vue';
import { trans as t } from 'laravel-vue-i18n';

const props = defineProps({
  totals: Object,
  goal: { type: Number, default: 2500 }
});

const macroConfig = computed(() => [
  { label: t('foodDiary.protein_label'), value: props.totals.protein },
  { label: t('foodDiary.carb_label'), value: props.totals.carbs },
  { label: t('foodDiary.fat_label'), value: props.totals.fats },
]);
</script>

<template>
  <section class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <div class="bg-primary/10 border border-primary/20 rounded-2xl p-4 backdrop-blur-md relative overflow-hidden">
      <p class="text-[10px] font-black text-primary uppercase tracking-widest">
        {{ $t('foodDiary.remaining_label') }}
      </p>
      <h3 class="text-2xl font-black mt-1 text-main-text">
        {{ goal - totals.kcal }}
        <span class="text-xs font-normal opacity-60">kcal</span>
      </h3>
      <div class="absolute -right-2 -bottom-2 size-12 bg-primary/10 blur-2xl rounded-full"></div>
    </div>

    <div v-for="macro in macroConfig" :key="macro.label"
      class="bg-neutral-dark/40 border border-neutral-border rounded-2xl p-4 backdrop-blur-md group hover:border-primary/30 transition-colors">
      <p
        class="text-[10px] font-black text-secondary-text uppercase tracking-widest group-hover:text-primary transition-colors">
        {{ macro.label }}
      </p>
      <h3 class="text-2xl font-black mt-1 text-main-text">
        {{ macro.value }}<span class="text-xs font-normal opacity-60 ml-0.5">g</span>
      </h3>

      <div class="w-full h-1 bg-white/5 rounded-full mt-3 overflow-hidden">
        <div class="h-full bg-secondary-text/20 rounded-full group-hover:bg-primary/40 transition-all w-2/3"></div>
      </div>
    </div>
  </section>
</template>