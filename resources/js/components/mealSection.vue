<script setup>
import { computed, ref } from 'vue';
import Button from '@/Components/button.vue';
import { trans as t } from 'laravel-vue-i18n';

const props = defineProps({
  mealConfig: Object,
  foods: Array
});

defineEmits(['delete', 'add-click']);

const mealTotal = computed(() => {
  return props.foods?.reduce((acc, f) => acc + Number(f.pivot.calorie), 0) || 0;
});

const confirmingDelete = ref(null);

const toggleConfirm = (id) => {
  if (confirmingDelete.value === id) {
    confirmingDelete.value = null;
  } else {
    confirmingDelete.value = id;
  }
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
      <div v-for="food in foods" :key="food.pivot_id"
        class="px-4 py-4 md:px-6 md:py-5 flex flex-col md:flex-row md:items-center justify-between group hover:bg-white/2 transition-all relative border-b last:border-0 border-neutral-border/30 gap-4">

        <div class="flex items-start md:items-center gap-4 flex-1">
          <div
            class="size-14 md:size-16 shrink-0 rounded-2xl bg-background-dark border border-neutral-border flex items-center justify-center overflow-hidden shadow-xl transition-transform group-hover:scale-105">
            <img v-if="food.image" :src="food.image" class="w-full h-full object-cover" />
            <span v-else class="text-2xl md:text-3xl">🥗</span>
          </div>

          <div class="flex-1 min-w-0">
            <div class="flex justify-between items-start md:block">
              <h4 class="font-black text-main-text text-sm md:text-base tracking-tight leading-tight truncate pr-2">
                {{ food.name }}
              </h4>

              <div class="text-right md:hidden">
                <p class="text-base font-black text-primary leading-none">{{ food.pivot.calorie }}</p>
                <span class="text-[8px] font-black text-secondary-text uppercase tracking-widest">{{
                  $t('foodDiary.kcal_unit') }}</span>
              </div>
            </div>

            <div class="flex flex-wrap gap-2 mt-2">
              <div
                class="flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-neutral-dark/60 border border-neutral-border/50">
                <span class="text-[9px] font-black text-blue-400">{{ $t('foodDiary.protein_short') }}</span>
                <span class="text-[11px] font-bold text-main-text">{{ food.pivot.protein }}g</span>
              </div>
              <div
                class="flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-neutral-dark/60 border border-neutral-border/50">
                <span class="text-[9px] font-black text-amber-400">{{ $t('foodDiary.carb_short') }}</span>
                <span class="text-[11px] font-bold text-main-text">{{ food.pivot.carb }}g</span>
              </div>
              <div
                class="flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-neutral-dark/60 border border-neutral-border/50">
                <span class="text-[9px] font-black text-rose-400">{{ $t('foodDiary.fat_short') }}</span>
                <span class="text-[11px] font-bold text-main-text">{{ food.pivot.fat }}g</span>
              </div>
            </div>
          </div>
        </div>

        <div
          class="flex items-center justify-between md:justify-end gap-6 md:border-0 border-t border-neutral-border/20 pt-3 md:pt-0">

          <div class="hidden md:text-right md:block">
            <p class="text-lg font-black text-primary leading-none">
              {{ food.pivot.calorie }}
            </p>
            <span class="text-[10px] font-black text-secondary-text uppercase tracking-widest">{{
              $t('foodDiary.kcal_unit') }}</span>
          </div>

          <Button :label="t('foodDiary.delete')" icon="delete"
            class="flex items-center justify-center gap-2 px-4 h-11! md:h-12! bg-rose-500/10! text-rose-500! border-rose-500/20 hover:bg-rose-500! hover:text-white! transition-colors"
            @click="$emit('delete', food.pivot.id)" />
        </div>
      </div>
    </div>

    <div v-if="!foods?.length" class="p-10 text-center border-t border-neutral-border/30">
      <div class="material-symbols-outlined text-neutral-border text-4xl mb-2">set_meal</div>
      <p class="text-secondary-text text-sm italic opacity-60">"{{ $t('foodDiary.no_entries') }}"</p>
    </div>
  </div>
</template>