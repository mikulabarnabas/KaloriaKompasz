<script setup>
import { ref, watch } from "vue";
import axios from "axios";

const props = defineProps({
  placeholder: { type: String, default: "Search exercise..." }
});

const emit = defineEmits(['select']);

const search = ref("");
const searchedExercises = ref([]);
const isDropdownOpen = ref(false);

async function searchExercise(page = 1) {
  if (!search.value) {
    searchedExercises.value = [];
    return;
  }
  try {
    const { data } = await axios.get(`/wdiary/getExercises/${search.value}/${page}`);
    searchedExercises.value = data.result;
    isDropdownOpen.value = true;
  } catch (error) {
    console.error("Search failed", error);
  }
}

const selectExercise = (exercise) => {
  emit('select', exercise);
  search.value = "";
  isDropdownOpen.value = false;
};

const closeDropdown = () => {
  setTimeout(() => { isDropdownOpen.value = false; }, 200);
};

watch(search, (val) => {
  if (val) searchExercise(1);
  else searchedExercises.value = [];
});
</script>

<template>
  <div class="relative flex-1 group z-50">
    <div class="relative flex items-center">
      <span class="material-symbols-outlined absolute left-4 text-primary transition-transform group-focus-within:scale-110">
        search
      </span>
      <input 
        v-model="search" 
        type="text" 
        :placeholder="placeholder"
        @blur="closeDropdown"
        @focus="isDropdownOpen = searchedExercises.length > 0"
        class="w-full pl-12 pr-12 py-3.5 bg-neutral-dark/40 backdrop-blur-md border border-neutral-border rounded-[1.25rem] focus:ring-2 focus:ring-primary/20 focus:border-primary text-main-text placeholder-secondary-text/50 transition-all outline-none" 
      />
    </div>

    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="translate-y-2 opacity-0"
      enter-to-class="translate-y-0 opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="translate-y-0 opacity-100"
      leave-to-class="translate-y-2 opacity-0"
    >
      <div v-if="isDropdownOpen && searchedExercises.length > 0" 
           class="absolute top-full left-0 right-0 mt-3 bg-background-dark/95 backdrop-blur-xl border border-neutral-border rounded-[1.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.5)] overflow-hidden z-50">
        
        <div class="max-h-96 overflow-y-auto custom-scrollbar divide-y divide-neutral-border/50">
          <button 
            v-for="exercise in searchedExercises" 
            :key="exercise.id"
            @click="selectExercise(exercise)"
            class="w-full flex items-center gap-4 p-4 hover:bg-primary/10 transition-all text-left group/item"
          >
            <div class="w-12 h-12 rounded-xl bg-neutral-dark flex items-center justify-center shrink-0 border border-neutral-border group-hover/item:border-primary/50 transition-colors">
              <span class="material-symbols-outlined text-xl text-primary/70 group-hover/item:text-primary group-hover/item:scale-110 transition-all">
                fitness_center
              </span>
            </div>

            <div class="flex-1 min-w-0">
              <h4 class="text-main-text font-black text-sm uppercase tracking-tight group-hover/item:text-primary transition-colors truncate">
                {{ exercise.name }}
              </h4>
              <p class="text-[10px] font-black text-secondary-text uppercase tracking-widest mt-0.5 opacity-70 truncate">
                <span class="text-primary/90">{{ exercise.calories_per_unit }} {{ $t('workoutDiary.calorie_label') }}</span> 
                • {{ exercise.unit }}
              </p>
            </div>

            <span class="material-symbols-outlined text-primary opacity-0 -translate-x-2 group-hover/item:opacity-100 group-hover/item:translate-x-0 transition-all text-sm">
              add_circle
            </span>
          </button>
        </div>
      </div>
    </Transition>
  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(13, 242, 89, 0.1);
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(13, 242, 89, 0.3);
}
</style>