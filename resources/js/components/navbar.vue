<script setup>
import { router, usePage } from "@inertiajs/vue3"
import { computed, onMounted, ref } from "vue"

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

<template>
<header class="sticky top-0 z-50 border-b border-neutral-border bg-background-dark/80 backdrop-blur">
  <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">

    <div class="flex items-center gap-3 cursor-pointer" @click="router.visit('/')">
      <div class="h-10 w-10 bg-primary rounded-lg flex items-center justify-center text-black font-bold">
        K
      </div>
      <span class="font-black text-xl">KalóriaKompasz</span>
    </div>

    <nav class="hidden md:flex gap-8 text-sm text-white/70">
      <a href="/fdiary" class="hover:text-primary">Food</a>
      <a href="/wdiary" class="hover:text-primary">Workout</a>
      <a href="/stats" class="hover:text-primary">Stats</a>
    </nav>

    <div class="flex items-center gap-4">
      <button 
          @click="toggleTheme" 
          class="p-2 rounded-lg hover:bg-neutral-surface transition-colors text-text-main"
        >
          <span class="material-symbols-outlined text-xl">
            {{ isDark ? 'light_mode' : 'dark_mode' }}
          </span>
        </button>

      <template v-if="user">
        <button @click="router.post('/logout')" class="text-sm">Logout</button>
      </template>

      <template v-else>
        <button @click="router.visit('/login')" class="text-sm">Login</button>
        <button @click="router.visit('/register')" class="bg-primary text-black px-4 py-2 rounded-lg font-bold">
          Sign up
        </button>
      </template>
    </div>

  </div>
</header>
</template>
