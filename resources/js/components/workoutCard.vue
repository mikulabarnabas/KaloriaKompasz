<script setup>
import { computed } from 'vue';
import { getActiveLanguage } from "laravel-vue-i18n";

const props = defineProps({
  exercise: { type: Object, required: true },
  clickable: { type: Boolean, default: false },
  border: { type: Boolean, default: false }
});

const displayName = computed(() => {
  if (getActiveLanguage() === 'hu' && props.exercise.name_hu) {
    return props.exercise.name_hu;
  }
  return props.exercise.name;
});
</script>

<template>
  <div 
    class="w-full flex items-center gap-4 p-4 transition-all group/ex-card"
    :class="[
      clickable ? 'hover:bg-primary/10 cursor-pointer' : '',
      border ? 'border border-neutral-border rounded-2xl bg-neutral-dark/10' : ''
    ]"
  >
    <div
      class="w-12 h-12 rounded-xl bg-neutral-dark shrink-0 flex items-center justify-center border border-neutral-border group-hover/ex-card:border-primary/50 transition-colors"
    >
      <div class="w-full h-full flex items-center justify-center bg-primary/10 text-primary">
        <span class="material-symbols-outlined text-xl transition-transform group-hover/ex-card:scale-110">
          fitness_center
        </span>
      </div>
    </div>

    <div class="flex-1 min-w-0">
      <h4 class="text-main-text font-bold truncate group-hover/ex-card:text-primary transition-colors">
        {{ displayName }}
      </h4>
      <p class="text-[12px] font-bold text-secondary-text uppercase tracking-tight truncate">
        <span class="text-primary">
          {{ exercise.pivot?.burned_calories || exercise.calories_per_unit }} {{ $t('workoutDiary.calorie_label') }}
        </span>
        
        <span class="mx-1">•</span>
        
        <span>
          {{ exercise.pivot?.amount }} {{ $t(`workoutDiary.${exercise.pivot?.unit || exercise.unit}`) }}
        </span>
      </p>
    </div>

    <slot name="action"></slot>
  </div>
</template>