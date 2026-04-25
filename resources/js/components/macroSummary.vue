<script setup>
import { computed } from 'vue';
import { trans as t } from 'laravel-vue-i18n';

const props = defineProps({
  stats: Object,
  targets: Object,
  complex: { type: Boolean, default: false },
});

const macros = computed(() => [
  { id: "protein",  label: t('statistics.macros.protein'), current: props.stats.protein, target: props.targets.protein, color: 'text-blue-400', bg: 'bg-blue-500' },
  { id: "carbs", label: t('statistics.macros.carbs'), current: props.stats.carbs, target: props.targets.carbs, color: 'text-amber-400', bg: 'bg-amber-500' },
  { id: "fat", label: t('statistics.macros.fat'), current: props.stats.fat, target: props.targets.fat, color: 'text-pink-400', bg: 'bg-pink-500' }
]);

const getPercent = (current, target) => Math.min(Math.round((current / (target || 1)) * 100), 100);
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <div
      class="bg-neutral-dark/30 p-8 rounded-[2.5rem] border border-neutral-border flex flex-col items-center justify-center relative shadow-2xl group overflow-hidden">
      <div class="relative" :class="complex ? 'w-40 h-40' : 'w-24 h-24'">
        <svg v-if="complex" class="w-full h-full transform -rotate-90">
          <circle cx="80" cy="80" r="72" stroke="currentColor" stroke-width="10" fill="transparent"
            class="text-neutral-dark" />
          <circle cx="80" cy="80" r="72" stroke="currentColor" stroke-width="10" fill="transparent"
            :stroke-dasharray="452.4"
            :stroke-dashoffset="452.4 - (452.4 * getPercent(stats.calories, targets.calories)) / 100"
            class="text-primary transition-all duration-1000 ease-out" stroke-linecap="round" />
        </svg>
        <div class="absolute inset-0 flex flex-col items-center justify-center">
          <span class="text-4xl font-black tracking-tighter">{{ stats.calories }}</span>
          <span class="text-[9px] font-black uppercase text-secondary-text tracking-[0.2em] mt-1">{{
            $t('statistics.consumed') }}</span>
        </div>
      </div>
      <div class="mt-6 text-center">
        <p class="text-[13px] font-black text-secondary-text uppercase tracking-widest">{{ $t('statistics.goal')}}: {{
          targets.calories }} kcal</p>
        <div v-if="complex"
          class="mt-2 inline-flex items-center gap-2 px-3 py-1 bg-blue-500/10 border border-blue-500/20 rounded-full">
          <span class="material-symbols-outlined text-sm text-blue-400">bolt</span>
          <span class="text-blue-400 text-[10px] font-black uppercase tracking-wider">-{{ stats.burned }}
            {{ $t('statistics.burned') }}</span>
        </div>
      </div>
    </div>

    <div v-for="macro in macros" :key="macro.id"
      class="bg-neutral-dark/30 p-8 rounded-[2.5rem] border border-neutral-border flex flex-col justify-between shadow-2xl transition-all">
      <div class="flex justify-between items-start">
        <span class="text-[10px] font-black text-secondary-text uppercase tracking-[0.2em]">{{ macro.label
          }}</span>
        <span :class="[macro.color, 'text-[10px] font-black uppercase tracking-widest']">{{
          getPercent(macro.current, macro.target) }}%</span>
      </div>
      <div class="mt-4">
        <div class="text-4xl font-black tracking-tighter">{{ Math.round(macro.current * 100) / 100 }}<span
            class="text-sm font-bold text-secondary-text ml-1">g</span></div>
        <div class="text-[12px] font-black text-secondary-text uppercase tracking-widest mt-1">{{ $t('statistics.target')}}: {{
          macro.target }}g</div>
      </div>
      <div class="w-full bg-neutral-dark h-2 rounded-full mt-6 overflow-hidden border border-white/5">
        <div :class="[macro.bg, 'h-full transition-all duration-1000 shadow-lg']"
          :style="`width: ${getPercent(macro.current, macro.target)}%`"></div>
      </div>
    </div>
  </div>
</template>
