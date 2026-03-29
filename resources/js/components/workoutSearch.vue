<script setup>
import { ref, watch } from "vue";
import axios from "axios";
import WorkoutCard from "@/Components/workoutCard.vue"

const props = defineProps({
  placeholder: { type: String, default: "Search exercise..." }
});

const emit = defineEmits(['select']);

const search = ref("");
const searchedExercises = ref([]);
const isDropdownOpen = ref(false);
const isLoading = ref(false);
const hasMore = ref(true);
let currentPage = 0;
let timeout = null;
let abortController = null;

async function searchExercises() {
  if (isLoading.value || (currentPage > 0 && !hasMore.value)) return;

  // Előző kérés megszakítása
  if (abortController) abortController.abort();
  abortController = new AbortController();

  try {
    const trimmedSearch = search.value.trim();
    if (trimmedSearch === '') {
      searchedExercises.value = [];
      hasMore.value = true;
      isDropdownOpen.value = false;
      return;
    }

    isLoading.value = true;
    currentPage++;

    const { data } = await axios.get(`/wdiary/getExercises/${trimmedSearch}/${currentPage}`, {
      signal: abortController.signal
    });

    if (currentPage === 1) {
      searchedExercises.value = data.result;
    } else {
      searchedExercises.value.push(...data.result);
    }

    hasMore.value = data.result.length >= 10;
    isDropdownOpen.value = searchedExercises.value.length > 0;

  } catch (error) {
    if (axios.isCancel(error)) return;
    console.error("Search failed", error);
  } finally {
    isLoading.value = false;
  }
}

const handleScroll = async (e) => {
  const { scrollTop, offsetHeight, scrollHeight } = e.target;
  if (scrollHeight - scrollTop <= offsetHeight + 200 && !isLoading.value && hasMore.value) {
    searchExercises();
  }
};

const selectExercise = (exercise) => {
  emit('select', exercise);
};

const closeDropdown = () => {
  setTimeout(() => { isDropdownOpen.value = false; }, 200);
};

watch(search, () => {
  clearTimeout(timeout);
  timeout = setTimeout(() => {
    currentPage = 0;
    hasMore.value = true;
    searchExercises();
  }, 200);
});
</script>

<template>
  <div class="relative flex-1 group">
    <div class="relative flex items-center">
      <span class="material-symbols-outlined absolute left-4 text-primary">search</span>
      <input v-model="search" type="text" :placeholder="placeholder" @blur="closeDropdown"
        @focus="isDropdownOpen = searchedExercises.length > 0"
        class="w-full pl-12 pr-12 py-3 bg-neutral-dark/40 border rounded-xl ring-2 ring-primary border-transparent text-main-text placeholder-secondary-text/50 transition-all outline-none" />
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