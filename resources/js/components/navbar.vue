<script setup>
import { router, usePage } from "@inertiajs/vue3"
import { computed, onMounted, ref } from "vue"

import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const user = computed(() => usePage().props.auth?.user)
const isDark = ref(true)

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
</script>

<template >
  <header class="sticky top-0 z-50 border-b border-neutral-border bg-background-dark/80 backdrop-blur">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">

      <div class="flex items-center gap-3 cursor-pointer" @click="router.visit('/')">
        <svg class="size-10 text-primary" viewBox="0 0 40 40" fill="none">
          <rect x="1" y="1" width="38" height="38" rx="12" fill="currentColor" fill-opacity="0.1" stroke="currentColor"
            stroke-opacity="0.2" />
          <g transform="translate(8,8) scale(1)" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="M16.2 7.8l-2 5.6-5.6 2 2-5.6 5.6-2z"></path>
            <path d="M12 2v2M12 20v2M2 12h2M20 12h2" opacity="0.5"></path>
          </g>
        </svg>

        <span class="font-black text-xl">Kalória Kompasz</span>
      </div>

      <nav class="hidden md:flex gap-8 text-sm text-white/70">
        <a href="/fdiary" class="hover:text-primary">{{ t('navbar.foodDiary') }}</a>
        <a href="/wdiary" class="hover:text-primary">{{ t('navbar.workoutDiary') }}</a>
        <a href="/stats" class="hover:text-primary">{{ t('navbar.stats') }}</a>
      </nav>

      <div class="flex items-center gap-4">
        <button @click="toggleTheme" class="p-2 rounded-lg hover:bg-neutral-surface transition-colors text-text-main">
          <span class="material-symbols-outlined text-xl">
            {{ isDark ? 'light_mode' : 'dark_mode' }}
          </span>
        </button>

        <template v-if="user">
          <button @click="router.post('/logout')" class="text-sm">{{ t('navbar.logout') }}</button>
        </template>

        <template v-else>
          <button @click="router.visit('/login')" class="bg-primary text-black px-4 py-2 rounded-lg font-bold">{{ t('navbar.signIn') }}</button>
        </template>
      </div>

    </div>
  </header>
</template>
