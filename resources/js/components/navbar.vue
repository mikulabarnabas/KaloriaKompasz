<script setup>
import { computed, onMounted, ref } from "vue";
import Menubar from "primevue/menubar";
import Button from "primevue/button";
import Menu from "primevue/menu";
import { router, usePage } from "@inertiajs/vue3";
import axios from "axios";
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const page = usePage();

const user = computed(() => page.props.auth?.user ?? null);
const isLoggedIn = computed(() => Boolean(user.value));
const lang = ref(page.props.locale.toUpperCase())

function goHome() {
  router.visit("/");
}

const items = ref([
  {
    label: t("navbar.foodDiary"),
    icon: "fa-solid fa-bowl-food",
    command: () => router.visit("/fdiary"),
  },
  {
    label: t("navbar.workoutDiary"),
    icon: "fa-solid fa-heart",
    command: () => router.visit("/wdiary"),
  },
  {
    label: t("navbar.stats"),
    icon: "fa-solid fa-chart-column",
    command: () => router.visit("/stats"),
  },
  {
    label: t("navbar.help"),
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
    label: t("navbar.profile"),
    icon: "pi pi-user",
    command: () => router.visit("/profile"),
  },
  { separator: true },
  { label: t("navbar.logout"), icon: "pi pi-sign-out", command: logout },
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
  if (saved === "light") document.documentElement.classList.remove("my-app-dark");
  syncThemeFromDom();
});

const themeIcon = computed(() => (isDark.value ? "pi pi-sun" : "pi pi-moon"));
const themeAriaLabel = computed(() =>
  isDark.value ? t("navbar.themeLight") : t("navbar.themeDark")
);

function toggleDarkMode() {
  document.documentElement.classList.toggle("my-app-dark");
  syncThemeFromDom();
  localStorage.setItem("theme", isDark.value ? "dark" : "light");
}

console.log(lang)

async function changeLang() {
  if (lang.value == 'EN') await axios.patch(`/lang/hu`);
  else if (lang.value == 'HU') await axios.patch(`/lang/en`);
  window.location.reload();
}
</script>

<template>
  <Menubar :model="items" :breakpoint="'1150px'" class="w-full border-0 shadow-md rounded-none px-2 sm:px-4">
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
      <div class="flex items-center gap-1">
        <Button :label="lang" class="p-button-outlined" @click='changeLang()'/>

        <template v-if="isLoggedIn">
          <Menu ref="accountMenuRef" :model="accountItems" popup />
          <Button :label="user?.name ?? t('navbar.profile')" icon="pi pi-user" class="hide-on-mobile p-button-text" @click="toggleAccountMenu" />
          <Button label="" icon="pi pi-user" class="hide-on-desktop p-button-text" @click="toggleAccountMenu" />
        </template>

        <template v-else>
          <Button :label="t('navbar.signIn')" icon="pi pi-sign-in" class="hide-on-mobile p-button-outlined" @click="goToLogin" />
          <Button :label="t('navbar.registration')" icon="pi pi-user-plus" class="hide-on-mobile p-button-outlined" @click="goToRegister" />
          <Button label="" icon="pi pi-sign-in" class="hide-on-desktop p-button-outlined" @click="goToLogin" />
        </template>

        <Button :icon="themeIcon" class="p-button-text" rounded text :aria-label="themeAriaLabel"
          @click="toggleDarkMode" />
      </div>
    </template>
  </Menubar>
</template>

<style scoped>
@media (max-width: 768px) {
  .hide-on-mobile {
    display: none !important;
  }
}

@media (min-width: 769px) {
  .hide-on-desktop {
    display: none !important;
  }
}
</style>
