<script setup>
import { router, usePage } from "@inertiajs/vue3"
import { computed, onMounted, ref } from "vue"
import { trans as t } from 'laravel-vue-i18n';

const user = computed(() => usePage().props.auth?.user)
const isDark = ref(true)
const mobileMenuOpen = ref(false)

onMounted(() => {
  isDark.value = localStorage.getItem("theme") !== "light"
  if (isDark.value) {
    document.documentElement.classList.add("dark")
  } else {
    document.documentElement.classList.remove("dark")
  }
})

function toggleTheme() {
  isDark.value = !isDark.value
  document.documentElement.classList.toggle("dark")
  localStorage.setItem("theme", isDark.value ? "dark" : "light")
}

function navigate(path) {
  mobileMenuOpen.value = false
  router.visit(path)
}
</script>

<template>
  <header class="sticky top-0 z-50 border-b border-neutral-border bg-background-dark/80 backdrop-blur">
    <div class="max-w-7xl mx-auto px-4 md:px-6 py-4 flex justify-between items-center">

      <div class="flex items-center gap-2 md:gap-3 cursor-pointer shrink-0" @click="router.visit('/')">
        <svg class="size-8 md:size-10 text-primary shrink-0" viewBox="0 0 40 40" fill="none">
          <rect x="1" y="1" width="38" height="38" rx="12" fill="currentColor" fill-opacity="0.1" stroke="currentColor"
            stroke-opacity="0.2" />
          <g transform="translate(8,8) scale(1)" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="M16.2 7.8l-2 5.6-5.6 2 2-5.6 5.6-2z"></path>
            <path d="M12 2v2M12 20v2M2 12h2M20 12h2" opacity="0.5"></path>
          </g>
        </svg>
        <span class="font-black text-base sm:text-lg md:text-xl text-main-text whitespace-nowrap">Kalória Kompasz</span>
      </div>

      <nav class="hidden md:flex gap-8 text-lg font-medium text-main-text">
        <button @click="router.visit('/fdiary')" class="hover:text-primary transition-colors">
          {{ $t('navbar.foodDiary') }}
        </button>
        <button @click="router.visit('/wdiary')" class="hover:text-primary transition-colors">
          {{ t('navbar.workoutDiary') }}
        </button>
        <button @click="router.visit('/stats')" class="hover:text-primary transition-colors">
          {{ t('navbar.stats') }}
        </button>
      </nav>

      <div class="flex items-center gap-1 sm:gap-2">
        <button @click="toggleTheme"
          class="size-10 flex items-center justify-center rounded-lg hover:bg-white/5 transition-colors text-main-text shrink-0">
          <span class="material-symbols-outlined text-2xl leading-none!">
            {{ isDark ? 'light_mode' : 'dark_mode' }}
          </span>
        </button>

        <template v-if="user">
          <div class="hidden md:flex items-center gap-3">
            <span class="text-sm font-medium text-main-text">{{ user.name }}</span>
            <button @click="router.post('/logout')"
              class="bg-primary/10 border border-primary/20 text-primary px-4 py-2 rounded-xl font-bold hover:bg-primary hover:text-black transition-all">
              {{ t('navbar.logout') }}
            </button>
          </div>

          <div class="flex md:hidden items-center">
            <button class="size-10 flex items-center justify-center text-primary shrink-0">
              <span class="material-symbols-outlined text-2xl leading-none!">account_circle</span>
            </button>
            <button @click="router.post('/logout')"
              class="size-10 flex items-center justify-center text-main-text shrink-0 hover:text-main-text transition-colors">
              <span class="material-symbols-outlined text-2xl leading-none!">logout</span>
            </button>
          </div>
        </template>

        <template v-else>
          <button @click="router.visit('/login')"
            class="hidden md:block bg-primary text-black px-4 py-2 rounded-xl font-bold hover:scale-105 transition-transform whitespace-nowrap">
            {{ t('navbar.signIn') }}
          </button>
          <button @click="router.visit('/login')"
            class="size-10 flex items-center justify-center text-primary shrink-0">
            <span class="material-symbols-outlined text-2xl leading-none!">login</span>
          </button>
        </template>

        <button @click="mobileMenuOpen = !mobileMenuOpen"
          class="size-10 flex md:hidden items-center justify-center text-main-text hover:text-main-text transition-colors shrink-0">
          <span class="material-symbols-outlined text-3xl leading-none!">
            {{ mobileMenuOpen ? 'close' : 'menu' }}
          </span>
        </button>
      </div>
    </div>

    <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0 -translate-y-4"
      enter-to-class="opacity-100 translate-y-0" leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 -translate-y-4">
      <div v-if="mobileMenuOpen"
        class="md:hidden absolute top-full left-0 w-full bg-background-dark border-b border-neutral-border shadow-xl px-6 py-8 space-y-6 flex flex-col items-center text-center">
        <button @click="navigate('/fdiary')"
          class="text-lg font-medium text-main-text hover:text-primary w-full transition-colors">{{
            t('navbar.foodDiary') }}</button>
        <button @click="navigate('/wdiary')"
          class="text-lg font-medium text-main-text hover:text-primary w-full transition-colors">{{
            t('navbar.workoutDiary') }}</button>
        <button @click="navigate('/stats')"
          class="text-lg font-medium text-main-text/80 hover:text-primary w-full transition-colors">{{
            t('navbar.stats') }}</button>
      </div>
    </Transition>
  </header>
</template>