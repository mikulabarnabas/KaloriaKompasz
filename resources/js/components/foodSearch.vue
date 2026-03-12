<script setup>
import { ref, watch } from "vue";
import axios from "axios";

const props = defineProps({
  placeholder: { type: String, default: "Search food..." }
});

const emit = defineEmits(['select']);

const search = ref("");
const searchedFoods = ref([]);
const isDropdownOpen = ref(false);
const isLoading = ref(false);
let currentPage = 0;

async function searchFood() {
  if (isLoading.value) return;

  try {
    isLoading.value = true;
    currentPage++
    const { data } = await axios.get(`/fdiary/getFoods/${search.value}/${currentPage}`);

    if (currentPage === 1) {
      searchedFoods.value = data.result;
    } else {
      searchedFoods.value.push(...data.result);
    }

    isDropdownOpen.value = searchedFoods.value.length > 0;
    isLoading.value = false;
  } catch (error) {
    console.error("Search failed", error);
  }
}

const handleScroll = async (e) => {
  const { scrollTop, offsetHeight, scrollHeight } = e.target;
  if (scrollHeight - scrollTop <= offsetHeight + 200 && !isLoading.value) {
    searchFood();
  }
};

const selectFood = (food) => {
  emit('select', food);
};

const closeDropdown = () => {
  setTimeout(() => { isDropdownOpen.value = false; }, 200);
};

watch(search, (val) => {
  currentPage = 0;
  searchFood();
});
</script>

<template>
  <div class="relative flex-1 group">
    <div class="relative flex items-center">
      <span class="material-symbols-outlined absolute left-4 text-primary">search</span>
      <input v-model="search" type="text" :placeholder="placeholder" @blur="closeDropdown"
        @focus="isDropdownOpen = searchedFoods.length > 0"
        class="w-full pl-12 pr-12 py-3 bg-neutral-dark/40 border border-neutral-border rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent text-main-text placeholder-secondary-text/50 transition-all outline-none" />
    </div>

    <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="translate-y-1 opacity-0"
      enter-to-class="translate-y-0 opacity-100" leave-active-class="transition duration-150 ease-in"
      leave-from-class="translate-y-0 opacity-100" leave-to-class="translate-y-1 opacity-0">
      <div v-if="isDropdownOpen && searchedFoods.length > 0"
        class="absolute top-full left-0 right-0 mt-2 bg-background-dark/95 backdrop-blur-xl border border-neutral-border rounded-2xl shadow-2xl overflow-hidden z-100">

        <div class="max-h-100 overflow-y-auto divide-y divide-neutral-border/50" @scroll="handleScroll">
          <button v-for="food in searchedFoods" :key="food.id" @click="selectFood(food)"
            class="w-full flex items-center gap-4 p-4 hover:bg-primary/10 transition-colors text-left group/item">
            <div
              class="w-12 h-12 rounded-xl bg-neutral-dark shrink-0 overflow-hidden border border-neutral-border group-hover/item:border-primary/50 transition-colors">
              <img v-if="food.image" :src="food.image" class="w-full h-full object-cover" />
              <div v-else class="w-full h-full flex items-center justify-center bg-primary/10 text-primary">
                <span class="material-symbols-outlined text-xl">restaurant</span>
              </div>
            </div>

            <div class="flex-1 min-w-0">
              <h4 class="text-main-text font-bold truncate group-hover/item:text-primary transition-colors">
                {{ food.name }}
              </h4>
              <p class="text-sm text-secondary-text truncate">
                <span class="text-primary/80">{{ food.calorie }} kcal</span> • {{ food.protein }}g p • {{ food.carb }}g
                c • {{ food.fat }}g f
              </p>
            </div>
          </button>
        </div>
      </div>
    </Transition>
  </div>
</template>