<script setup>
import { router, usePage } from "@inertiajs/vue3"
import { computed, ref } from "vue"
import { trans as t, loadLanguageAsync, getActiveLanguage } from 'laravel-vue-i18n';
import IconCompass from '@/Components/iconCompass.vue'
import DropDown from "@/Components/dropdownButton.vue";

const user = computed(() => usePage().props.auth?.user)
const mobileMenuOpen = ref(false)
const lang = ref(localStorage.lang || getActiveLanguage());
const theme = ref(localStorage.theme)

async function changeLanguage() {
  const nextLang = getActiveLanguage() == 'en' ? 'hu' : 'en'
  await loadLanguageAsync(nextLang);
  localStorage.lang = nextLang;
  lang.value = nextLang;
}

function toggleTheme() {
  document.documentElement.classList.toggle("dark")
  localStorage.theme = localStorage.theme == "dark" ? "light" : "dark";
  theme.value = localStorage.theme;
}

const userDropDown = computed(() => [
  { label: user.value.name, icon: 'person', function: () => router.get('/profile') },
  { label: lang.value == 'hu' ? 'English' : 'Magyar', icon: 'language', function: () => changeLanguage() },
  { label: theme.value == 'light' ? t('navbar.themeDark') : t('navbar.themeLight'), icon: theme.value == 'light' ? 'dark_mode' : 'light_mode', function: () => toggleTheme() },
  { label: t('navbar.logout'), icon: 'logout', function: () => router.post('/logout'), class: 'text-red-600 hover:text-red-600' }
])

const noUserDropDown = computed(() => [
  { label: "Login", icon: 'login', function: () => router.get('/profile') },
  { label: lang.value == 'hu' ? 'English' : 'Magyar', icon: 'language', function: () => changeLanguage() },
  { label: theme.value == 'light' ? t('navbar.themeDark') : t('navbar.themeLight'), icon: theme.value == 'light' ? 'dark_mode' : 'light_mode', function: () => toggleTheme() },
])
</script>

<template>
  <header class="sticky top-0 z-50 border-b border-neutral-border bg-background-dark/80 backdrop-blur">
    <div class="max-w-7xl mx-auto px-4 md:px-6 py-4 flex justify-between items-center">

      <div class="flex items-center gap-2 md:gap-3 cursor-pointer shrink-0 flex-1" @click="router.visit('/')">
        <IconCompass class="size-10 text-green-600" />
        <img src="" alt="">
        <span class="font-black text-base sm:text-lg md:text-xl text-main-text whitespace-nowrap">Kalória Kompasz</span>
      </div>

      <nav class="hidden lg:flex gap-8 text-lg font-medium text-main-text justify-center">
        <button @click="router.visit('/fdiary')" class="hover:text-primary transition-colors">{{ $t('navbar.foodDiary')
          }}</button>
        <button @click="router.visit('/wdiary')" class="hover:text-primary transition-colors">{{
          t('navbar.workoutDiary') }}</button>
        <button @click="router.visit('/stats')" class="hover:text-primary transition-colors">{{ t('navbar.stats')
          }}</button>
      </nav>

      <div class="flex items-center gap-1 sm:gap-2 flex-1 justify-end">
        <template v-if="user">
          <DropDown icon="person" :items="userDropDown"></DropDown>
        </template>
        <template v-else>
          <DropDown icon="person" :items="noUserDropDown"></DropDown>
        </template>

        <button @click="mobileMenuOpen = !mobileMenuOpen"
          class="size-10 flex lg:hidden items-center justify-center text-main-text">
          <span class="material-symbols-outlined text-3xl">{{ mobileMenuOpen ? 'close' : 'menu' }}</span>
        </button>
      </div>
    </div>

    <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0 -translate-y-4"
      enter-to-class="opacity-100 translate-y-0" leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 -translate-y-4">
      <div v-if="mobileMenuOpen"
        class="md:hidden absolute top-full left-0 w-full bg-background-dark border-b border-neutral-border shadow-xl px-6 py-8 space-y-6 flex flex-col items-center">
        <button @click="router.visit('/fdiary')" class="text-lg font-medium text-main-text hover:text-primary w-full">{{
          t('navbar.foodDiary') }}</button>
        <button @click="router.visit('/wdiary')" class="text-lg font-medium text-main-text hover:text-primary w-full">{{
          t('navbar.workoutDiary') }}</button>
        <button @click="router.visit('/stats')"
          class="text-lg font-medium text-main-text/80 hover:text-primary w-full">{{
            t('navbar.stats') }}</button>
      </div>
    </Transition>
  </header>
</template>