<script setup>
import { router, usePage } from "@inertiajs/vue3"
import { computed, onMounted, ref } from "vue"
import { trans as t, loadLanguageAsync, getActiveLanguage } from 'laravel-vue-i18n';

const user = computed(() => usePage().props.auth?.user)
const isDark = ref(true)
const mobileMenuOpen = ref(false)
const profileDropdownOpen = ref(false) // New state for the dropdown

// Move your click-outside directive here (or register it globally in your app.js)
const vClickOutside = {
  mounted(el, binding) {
    el.clickOutsideEvent = (event) => {
      if (!(el === event.target || el.contains(event.target))) binding.value();
    };
    document.addEventListener('click', el.clickOutsideEvent);
  },
  unmounted(el) {
    document.removeEventListener('click', el.clickOutsideEvent);
  },
};

async function changeLanguage(lang) {
  await loadLanguageAsync(lang);
}

onMounted(() => {
  isDark.value = localStorage.getItem("theme") !== "light"
  document.documentElement.classList.toggle("dark", isDark.value)
})

function toggleTheme() {
  isDark.value = !isDark.value
  document.documentElement.classList.toggle("dark")
  localStorage.setItem("theme", isDark.value ? "dark" : "light")
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
        <button @click="router.visit('/fdiary')" class="hover:text-primary transition-colors">{{ $t('navbar.foodDiary')
        }}</button>
        <button @click="router.visit('/wdiary')" class="hover:text-primary transition-colors">{{
          t('navbar.workoutDiary') }}</button>
        <button @click="router.visit('/stats')" class="hover:text-primary transition-colors">{{ t('navbar.stats')
        }}</button>
      </nav>

      <div class="flex items-center gap-1 sm:gap-2">
        <button @click="toggleTheme"
          class="size-10 flex items-center justify-center rounded-lg hover:bg-white/5 transition-colors text-main-text">
          <span class="material-symbols-outlined text-2xl leading-none!">{{ isDark ? 'light_mode' : 'dark_mode'
          }}</span>
        </button>

        <template v-if="user">
          <div class="hidden md:block relative" v-click-outside="() => profileDropdownOpen = false">

            <button @click="profileDropdownOpen = !profileDropdownOpen"
              class="flex items-center gap-2 h-10 px-4 rounded-lg text-main-text hover:bg-white/5 transition-colors">
              <span class="font-medium">{{ user.name }}</span>
              <span class="material-symbols-outlined transition-transform duration-200"
                :class="{ 'rotate-180': profileDropdownOpen }">
                expand_more
              </span>
            </button>

            <Transition enter-active-class="transition duration-200 ease-out"
              enter-from-class="opacity-0 -translate-y-2 scale-95" enter-to-class="opacity-100 translate-y-0 scale-100"
              leave-active-class="transition duration-150 ease-in"
              leave-from-class="opacity-100 translate-y-0 scale-100" leave-to-class="opacity-0 -translate-y-2 scale-95">
              <div v-if="profileDropdownOpen" class="absolute right-0 top-full pt-2 z-50 w-52">

                <div
                  class="relative bg-background-dark/95 border border-neutral-border rounded-2xl shadow-2xl p-2 backdrop-blur-xl flex flex-col gap-1">

                  <div class="absolute -top-[9px] right-6 w-4 h-4 overflow-hidden pointer-events-none">
                    <div
                      class="w-2.5 h-2.5 bg-background-dark/95 border-t border-l border-neutral-border rotate-45 mx-auto mt-[5px]">
                    </div>
                  </div>

                  <button @click="router.visit('/profile'); profileDropdownOpen = false"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/5 text-sm font-bold text-main-text transition-all">
                    <span class="material-symbols-outlined text-lg opacity-70">person</span>
                    {{ t('navbar.profile') }}
                  </button>

                  <button
                    @click="changeLanguage(getActiveLanguage() === 'hu' ? 'en' : 'hu'); profileDropdownOpen = false"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/5 text-sm font-bold text-main-text transition-all">
                    <span class="material-symbols-outlined text-lg opacity-70">language</span>
                    {{ getActiveLanguage() === 'hu' ? 'English' : 'Magyar' }}
                  </button>

                  <div class="h-px bg-neutral-border/50 my-1 mx-2"></div>

                  <button @click="router.post('/logout'); profileDropdownOpen = false"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-rose-500/10 text-sm font-black text-rose-500 transition-all">
                    <span class="material-symbols-outlined text-lg">logout</span>
                    {{ t('navbar.logout') }}
                  </button>

                </div>
              </div>
            </Transition>
          </div>
        </template>

        <template v-else>
          <button @click="router.visit('/login')" class="size-10 flex items-center justify-center text-primary"><span
              class="material-symbols-outlined text-2xl">login</span></button>
        </template>

        <button @click="mobileMenuOpen = !mobileMenuOpen"
          class="size-10 flex md:hidden items-center justify-center text-main-text">
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