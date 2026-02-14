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

async function searchFood(page = 1) {
  if (!search.value) {
    searchedFoods.value = [];
    return;
  }
  try {
    const { data } = await axios.get(`/fdiary/getFoods/${search.value}/${page}`);
    searchedFoods.value = data.result;
    isDropdownOpen.value = true;
  } catch (error) {
    console.error("Search failed", error);
  }
}

const selectFood = (food) => {
  emit('select', food);
  search.value = ""; // Clear search after selection
  isDropdownOpen.value = false;
};

// Close dropdown when clicking outside (optional but recommended)
const closeDropdown = () => {
  setTimeout(() => { isDropdownOpen.value = false; }, 200);
};

watch(search, (val) => {
  if (val) searchFood(1);
  else searchedFoods.value = [];
});
</script>

<template>
  <div class="relative flex-1 group">
    <div class="relative flex items-center">
      <span class="material-symbols-outlined absolute left-4 text-primary">search</span>
      <input 
        v-model="search" 
        type="text" 
        :placeholder="placeholder"
        @blur="closeDropdown"
        @focus="isDropdownOpen = searchedFoods.length > 0"
        class="w-full pl-12 pr-12 py-3 bg-white/5 border border-white/10 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent text-white placeholder-slate-500 transition-all outline-none" 
      />
      <span class="material-symbols-outlined absolute right-4 text-slate-500 cursor-pointer hover:text-white transition-colors">barcode_scanner</span>
    </div>

    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="translate-y-1 opacity-0"
      enter-to-class="translate-y-0 opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="translate-y-0 opacity-100"
      leave-to-class="translate-y-1 opacity-0"
    >
      <div v-if="isDropdownOpen && searchedFoods.length > 0" 
           class="absolute top-full left-0 right-0 mt-2 bg-[#0d1a13] border border-white/10 rounded-2xl shadow-2xl overflow-hidden z-100 backdrop-blur-xl">
        
        <div class="max-h-100 overflow-y-auto divide-y divide-white/5">
          <button 
            v-for="food in searchedFoods" 
            :key="food.id"
            @click="selectFood(food)"
            class="w-full flex items-center gap-4 p-4 hover:bg-white/5 transition-colors text-left"
          >
            <div class="w-12 h-12 rounded-xl bg-white/10 shrink-0 overflow-hidden">
              <img v-if="food.image" :src="food.image" class="w-full h-full object-cover" />
              <div v-else class="w-full h-full flex items-center justify-center bg-primary/10 text-primary">
                <span class="material-symbols-outlined text-xl">restaurant</span>
              </div>
            </div>

            <div class="flex-1 min-w-0">
              <h4 class="text-white font-medium truncate">{{ food.name }}</h4>
              <p class="text-sm text-slate-400 truncate">
                {{ food.brand || 'Generic' }} • {{ food.unit_amount || 100 }}g • {{ food.kcal }} kcal
              </p>
            </div>
          </button>
        </div>
      </div>
    </Transition>
  </div>
</template>