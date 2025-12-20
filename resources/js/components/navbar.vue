<script setup>
import { computed, onMounted, ref } from "vue";
import Menubar from "primevue/menubar";
import Button from "primevue/button";
import Menu from "primevue/menu";
import { router, usePage } from "@inertiajs/vue3";

const page = usePage();

const user = computed(() => page.props.user ?? null);
const isLoggedIn = computed(() => Boolean(user.value));

const items = ref([
  { label: "Ételnapló", icon: "fa-solid fa-bowl-food" },
  { label: "Edzésnapló", icon: "fa-solid fa-heart" },
  { label: "Statisztika", icon: "fa-solid fa-chart-column" },
  { label: "Segítség", icon: "pi pi-info-circle" },
]);

function logout() {
  router.post("/logout");
}

function goToLogin() {
  router.visit("/login");
}

function goToRegister() {
  router.visit("/register");
}

const accountItems = computed(() => [
  {
    label: "Profile",
    icon: "pi pi-user",
    command: () => console.log("Profile"),
  },
  {
    label: "Settings",
    icon: "pi pi-cog",
    command: () => console.log("Settings"),
  },
  { separator: true },
  { label: "Logout", icon: "pi pi-sign-out", command: logout },
]);

const accountMenuRef = ref(null);

function toggleAccountMenu(event) {
  accountMenuRef.value.toggle(event);
}

const isDark = ref(false);

function syncThemeFromDom() {
  isDark.value = document.documentElement.classList.contains("my-app-dark");
}

onMounted(() => {
  const saved = localStorage.getItem("theme");
  if (saved === "dark") document.documentElement.classList.add("my-app-dark");
  if (saved === "light")
    document.documentElement.classList.remove("my-app-dark");
  syncThemeFromDom();
});

const themeIcon = computed(() => (isDark.value ? "pi pi-sun" : "pi pi-moon"));
const themeAriaLabel = computed(() =>
  isDark.value ? "Switch to light mode" : "Switch to dark mode",
);

function toggleDarkMode() {
  document.documentElement.classList.toggle("my-app-dark");
  syncThemeFromDom();
  localStorage.setItem("theme", isDark.value ? "dark" : "light");
}
</script>

<template>
  <Menubar
    :model="items"
    :breakpoint="'768px'"
    class="w-full border-0 shadow-md bg-white rounded-none px-2 sm:px-4"
  >
    <template #start>
      <div class="flex items-center gap-2">
        <span
          class="text-lg sm:text-2xl font-bold text-primary whitespace-nowrap"
        >
          Kalória Kompasz
        </span>
      </div>
    </template>

    <template #item="{ item, props }">
      <a
        v-bind="props.action"
        class="flex items-center gap-2 rounded-md px-3 py-2 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary/30"
      >
        <i v-if="item.icon" :class="item.icon"></i>
        <span class="min-w-0 truncate">{{ item.label }}</span>

        <span
          v-if="item.shortcut"
          class="ml-auto border border-gray-300 rounded bg-gray-100 text-xs px-1"
        >
          {{ item.shortcut }}
        </span>
      </a>
    </template>

    <template #end>
      <div class="flex items-center gap-2">
        <template v-if="isLoggedIn">
          <Menu ref="accountMenuRef" :model="accountItems" popup />

          <Button
            :label="user?.name ?? 'Account'"
            icon="pi pi-user"
            class="p-button-text"
            @click="toggleAccountMenu"
          />
        </template>

        <template v-else>
          <Button
            label="Sign in"
            icon="pi pi-sign-in"
            class="p-button-text"
            @click="goToLogin"
          />
          <Button
            label="Registration"
            icon="pi pi-user-plus"
            class="p-button-outlined"
            @click="goToRegister"
          />
        </template>

        <Button
          :icon="themeIcon"
          class="p-button-text"
          rounded
          text
          :aria-label="themeAriaLabel"
          @click="toggleDarkMode"
        />
      </div>
    </template>
  </Menubar>
</template>