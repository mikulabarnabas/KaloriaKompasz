<script setup>
import { ref } from "vue";
import Menubar from "primevue/menubar";
import Ripple from "primevue/ripple";
import Button from "primevue/button"

const items = ref([
  { label: "Ételnapló", icon: "fa-solid fa-bowl-food" },
  { label: "Edzésnapló", icon: "fa-solid fa-heart" },
  { label: "Statisztika", icon: "fa-solid fa-chart-column" },
  { label: "Segítség", icon: "pi pi-info-circle" },
]);


function toggleDarkMode() {
  document.documentElement.classList.toggle('my-app-dark');
}
</script>

<template>
  <Menubar :model="items" :breakpoint="'768px'" class="w-full border-0 shadow-md bg-white rounded-none px-2 sm:px-4">
    <!-- Left: brand -->
    <template #start>
      <div class="flex items-center gap-2">
        <span class="text-lg sm:text-2xl font-bold text-primary whitespace-nowrap">
          Kalória Kompasz
        </span>
      </div>
    </template>

    <!-- Custom item rendering -->
    <template #item="{ item, props }">
      <a v-ripple v-bind="props.action"
        class="flex items-center gap-2 rounded-md px-3 py-2 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary/30">
        <i v-if="item.icon" :class="item.icon"></i>
        <span class="min-w-0 truncate">{{ item.label }}</span>

        <span v-if="item.shortcut" class="ml-auto border border-gray-300 rounded bg-gray-100 text-xs px-1">
          {{ item.shortcut }}
        </span>
      </a>
    </template>

    <template #end>
      <Button label="Toggle Dark Mode" @click="toggleDarkMode()" />
    </template>
  </Menubar>
</template>

<script>
import Ripple from "primevue/ripple";

export default {
  directives: {
    ripple: Ripple,
  },
};
</script>