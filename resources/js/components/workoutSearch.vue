<script setup>
import { ref, watch } from "vue";
import axios from "axios";
import { getActiveLanguage } from "laravel-vue-i18n";
import WorkoutCard from "@/Components/workoutCard.vue"

const props = defineProps({
  placeholder: { type: String, default: "Search exercise..." }
});

const emit = defineEmits(['select']);

const search = ref("");
const searchedExercises = ref([]);
const isDropdownOpen = ref(false);
const isLoading = ref(false);
let currentPage = 0;

async function searchExercise() {
  if (isLoading.value || !search.value) return;

  try {
    isLoading.value = true;
    currentPage++;
    const { data } = await axios.get(`/wdiary/getExercises/${search.value}/${currentPage}`);

    if (currentPage === 1) {
      searchedExercises.value = data.result;
    } else {
      searchedExercises.value.push(...data.result);
    }

    isDropdownOpen.value = searchedExercises.value.length > 0;
    isLoading.value = false;
  } catch (error) {
    console.error("Search failed", error);
    isLoading.value = false;
  }
}

const handleScroll = async (e) => {
  const { scrollTop, offsetHeight, scrollHeight } = e.target;
  if (scrollHeight - scrollTop <= offsetHeight + 200 && !isLoading.value) {
    searchExercise();
  }
};

const selectExercise = (exercise) => {
  emit('select', exercise);
};

const closeDropdown = () => {
  setTimeout(() => { isDropdownOpen.value = false; }, 200);
};

watch(search, (val) => {
  currentPage = 0;
  if (val) {
    searchExercise();
  } else {
    searchedExercises.value = [];
    isDropdownOpen.value = false;
  }
});

const getDisplayName = (item) => {
  console.log(getActiveLanguage())
  if (getActiveLanguage() === 'hu' && item.name_hu) {
    return item.name_hu;
  }
  return item.name;
};
</script>

<template>
  <div class="relative flex-1 group">
    <div class="relative flex items-center">
      <span class="material-symbols-outlined absolute left-4 text-primary">search</span>
      <input v-model="search" type="text" :placeholder="placeholder" @blur="closeDropdown"
        @focus="isDropdownOpen = searchedExercises.length > 0"
        class="w-full pl-12 pr-12 py-3 bg-neutral-dark/40 border border-neutral-border rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent text-main-text placeholder-secondary-text/50 transition-all outline-none" />
    </div>

    <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="translate-y-1 opacity-0"
      enter-to-class="translate-y-0 opacity-100" leave-active-class="transition duration-150 ease-in"
      leave-from-class="translate-y-0 opacity-100" leave-to-class="translate-y-1 opacity-0">
      <div v-if="isDropdownOpen && searchedExercises.length > 0"
        class="absolute top-full left-0 right-0 mt-2 bg-background-dark/95 backdrop-blur-xl border border-neutral-border rounded-2xl shadow-2xl overflow-hidden z-100">

        <div class="max-h-100 overflow-y-auto divide-y divide-neutral-border/50" @scroll="handleScroll">
          <WorkoutCard v-for="exercise in searchedExercises" :key="exercise.id" :exercise="exercise" clickable
            @click="selectExercise(exercise)" />
        </div>
      </div>
    </Transition>
  </div>
</template>