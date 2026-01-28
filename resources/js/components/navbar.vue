<script setup>
import { computed, onMounted, ref } from "vue";
import Menubar from "primevue/menubar";
import Button from "primevue/button";
import Menu from "primevue/menu";
import { router, usePage } from "@inertiajs/vue3";
import axios from "axios";

const page = usePage();

const user = computed(() => page.props.auth?.user ?? null);
const isLoggedIn = computed(() => Boolean(user.value));

function goHome() {
  router.visit("/");
}

const items = ref([
  {
    label: "Ételnapló",
    icon: "fa-solid fa-bowl-food",
    command: () => router.visit("/fdiary"),
  },
  {
    label: "Edzésnapló",
    icon: "fa-solid fa-heart",
    command: () => router.visit("/wdiary"),
  },
  {
    label: "Statisztika",
    icon: "fa-solid fa-chart-column",
    command: () => router.visit("/stats"),
  },
  {
    label: "Segítség",
    icon: "pi pi-info-circle",
    command: () => router.visit("/help"),
  },
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
    command: () => router.visit("/profile"),
  },
  { separator: true },
  { label: "Logout", icon: "pi pi-sign-out", command: logout },
]);

const accountMenuRef = ref(null);

function toggleAccountMenu(event) {
  accountMenuRef.value?.toggle(event);
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
  isDark.value ? "Switch to light mode" : "Switch to dark mode"
);

function toggleDarkMode() {
  document.documentElement.classList.toggle("my-app-dark");
  syncThemeFromDom();
  localStorage.setItem("theme", isDark.value ? "dark" : "light");
}

function changeLang(lang) {
  axios.patch(`/lang/${lang}`)
  window.location.reload();
}

</script>

<template>
  <Menubar :model="items" :breakpoint="'768px'" class="w-full border-0 shadow-md rounded-none px-2 sm:px-4">
    <template #start>
      <button type="button" class="flex items-center gap-2 text-lg sm:text-2xl font-bold whitespace-nowrap"
        @click="goHome">
        Kalória Kompasz
      </button>
    </template>

    <template #item="{ item, props }">
      <a v-bind="props.action" class="flex items-center gap-2 rounded-md px-3 py-2">
        <i v-if="item.icon" :class="item.icon"></i>
        <span class="min-w-0 truncate">{{ item.label }}</span>
      </a>
    </template>

    <template #end>
      <div class="flex items-center gap-2">
        <Button @click='changeLang("hu")'>HU</Button>
        <Button @click='changeLang("en")'>EN</Button>
        <template v-if="isLoggedIn">
          <Menu ref="accountMenuRef" :model="accountItems" popup />
          <Button :label="user?.name ?? 'Account'" icon="pi pi-user" class="p-button-text" @click="toggleAccountMenu" />
        </template>

        <template v-else>
          <Button label="Sign in" icon="pi pi-sign-in" class="p-button-text" @click="goToLogin" />
          <Button label="Registration" icon="pi pi-user-plus" class="p-button-outlined" @click="goToRegister" />
        </template>

        <Button :icon="themeIcon" class="p-button-text" rounded text :aria-label="themeAriaLabel"
          @click="toggleDarkMode" />
      </div>
    </template>
  </Menubar>
</template>