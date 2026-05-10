<script setup>
import AppLayout from "@/Layouts/appLayout.vue"
import Accordion from "@/Components/accordion.vue"
import { computed, ref, onMounted } from "vue"
import { trans as t } from 'laravel-vue-i18n';
import Card from "@/Components/card.vue"
import { router } from "@inertiajs/vue3";
import { Device } from '@capacitor/device';

const faqItems = computed(() => [
  {
    question: t('home.faq_first_question'),
    answer: t('home.faq_first_answer')
  },
  {
    question: t('home.faq_second_question'),
    answer: t('home.faq_second_answer')
  },
  {
    question: t('home.faq_third_question'),
    answer: t('home.faq_third_answer')
  }
]);

const cards = computed(() => [
  {
    id: 1,
    title: t('home.card_first_title'),
    content: t('home.card_first_content'),
    icon: 'restaurant',
    route: '/fdiary'
  },
  {
    id: 2,
    title: t('home.card_second_title'),
    content: t('home.card_second_content'),
    icon: 'fitness_center',
    route: '/wdiary'
  },
  {
    id: 3,
    title: t('home.card_third_title'),
    content: t('home.card_third_content'),
    icon: 'insights',
    route: '/stats'
  }
]);

const isWeb = ref(false);

const checkPlatform = async () => {
  const info = await Device.getInfo();

  const isStandardWeb = info.platform === 'web';
  
  const isElectron = /electron/i.test(navigator.userAgent);

  if (isStandardWeb && !isElectron) {
    isWeb.value = true;
  } else {
    isWeb.value = false;
  }
};

checkPlatform();
</script>

<template>
  <AppLayout>
    <div class="relative w-full bg-background-dark">
      <div class="absolute inset-0 pointer-events-none z-0 opacity-40 dark:opacity-20">
        <svg width="100%" height="100%" viewBox="0 0 100 1000" preserveAspectRatio="none" fill="none"
          xmlns="http://www.w3.org/2000/svg">
          <path d="M 80 0 C 80 150, 20 250, 20 400 S 90 550, 80 750 S 10 850, 50 1000" stroke="url(#gradient-path)"
            stroke-width="3" stroke-linecap="round" stroke-dasharray="12 12" vector-effect="non-scaling-stroke"
            class="route-path" />
          <defs>
            <linearGradient id="gradient-path" x1="0" y1="0" x2="0" y2="1000" gradientUnits="userSpaceOnUse">
              <stop offset="0%" stop-color="#059669" stop-opacity="1" />
              <stop offset="100%" stop-color="#10b981" stop-opacity="0.6" />
            </linearGradient>
          </defs>
        </svg>
      </div>

      <main class="relative z-10 flex min-h-screen flex-col overflow-x-hidden">
        <section class="relative overflow-hidden px-6 py-24 lg:py-40 animate-fly-in">
          <div class="mx-auto max-w-7xl">
            <div class="max-w-4xl">
              <h1 class="text-6xl font-black leading-[1.05] text-main-text md:text-8xl tracking-tight">
                {{ $t('home.hero_title_1') }} <br />
                <span class="text-primary italic">{{ $t('home.hero_title_2') }}</span> & <span
                  class="text-primary italic">{{ $t('home.hero_title_3') }}</span> {{ $t('home.hero_title_4') }}
              </h1>
              <p class="mt-8 max-w-2xl text-xl leading-relaxed text-secondary-text"
                v-html="$t('home.hero_description')"></p>
            </div>
          </div>
        </section>

        <section class="bg-neutral-dark/30 py-24 px-6">
          <div class="mx-auto max-w-7xl">
            <div class="mb-16 flex flex-col items-center text-center">
              <h2 class="text-4xl font-black text-main-text md:text-5xl">{{ $t('home.pillars_title') }}</h2>
              <div class="mt-4 h-1.5 w-24 rounded-full bg-primary"></div>
              <p class="mt-6 max-w-2xl text-lg text-secondary-text">{{ $t('home.pillars_description') }}</p>
            </div>
            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
              <Card v-for="item in cards" :key="item.id" :title="item.title" :content="item.content" :icon="item.icon"
                @click="router.get(item.route)" />
            </div>
          </div>
        </section>

        <section v-if="isWeb" class="relative py-24 px-6 overflow-hidden bg-neutral-dark/50 border-y border-white/5">
          <div class="mx-auto max-w-7xl">
            <div class="flex flex-col items-center text-center gap-12">
              <div class="max-w-3xl">
                <h2 class="text-4xl font-black text-main-text md:text-6xl mb-6 tracking-tight">
                  {{ $t('home.download_section_title') }}
                </h2>
                <p class="text-xl text-secondary-text mb-10 leading-relaxed">
                  {{ $t('home.download_section_description') }}
                </p>

                <div class="flex flex-col sm:flex-row justify-center gap-6">
                  <a href="https://storage.googleapis.com/kaloriakompasz/KaloriaKompasz.apk" download="KaloriaKompasz.apk"
                    class="group inline-flex items-center justify-center gap-4 bg-primary hover:bg-primary/80 text-white font-extrabold py-5 px-10 rounded-2xl transition-all shadow-2xl shadow-primary/20">
                    <span class="material-symbols-outlined text-2xl">android</span>
                    {{ $t('home.download_android_button') }}
                  </a>

                  <a href="https://storage.googleapis.com/kaloriakompasz/Kal%C3%B3riaKompasz%20Setup%201.0.0.exe" download="KaloriaKompasz_Setup.exe"
                    class="group inline-flex items-center justify-center gap-4 bg-white/10 hover:bg-white/20 text-main-text font-extrabold py-5 px-10 rounded-2xl transition-all border border-white/10">
                    <span class="material-symbols-outlined text-2xl">desktop_windows</span>
                    {{ $t('home.download_desktop_button') }}
                  </a>
                </div>
              </div>
            </div>
          </div>
        </section>

        <section class="py-24 px-6">
          <div class="mx-auto max-w-7xl flex flex-col items-center text-center">
            <div class="flex flex-col items-center gap-8 mb-16">
              <div class="max-w-2xl">
                <h2 class="text-4xl font-black text-main-text md:text-5xl">{{ $t('home.team_section_title') }}</h2>
                <p class="mt-6 text-lg text-secondary-text">{{ $t('home.team_section_description') }}</p>
              </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-12 max-w-4xl mx-auto">
              <div class="group flex flex-col items-center text-center">
                <div
                  class="relative mb-6 h-48 w-48 overflow-hidden rounded-full border-4 border-transparent group-hover:border-primary transition">
                  <img alt="Founder 1"
                    class="h-full w-full rounded-full object-cover grayscale group-hover:grayscale-0 transition object-[25%_75%]"
                    :src="'/img/barna.jpg'" />
                </div>
                <h4 class="text-xl font-bold text-main-text">Mikula Barnabás</h4>
                <p class="text-sm font-medium text-primary uppercase tracking-widest">{{ $t('home.role_dev') }}</p>
              </div>
              <div class="group flex flex-col items-center text-center">
                <div
                  class="relative mb-6 h-48 w-48 overflow-hidden rounded-full border-4 border-transparent group-hover:border-primary transition p-1">
                  <img alt="Founder 2"
                    class="h-full w-full rounded-full object-cover grayscale group-hover:grayscale-0 transition object-top"
                    :src="'/img/geri.jpg'" />
                </div>
                <h4 class="text-xl font-bold text-main-text">Bóta Gergely</h4>
                <p class="text-sm font-medium text-primary uppercase tracking-widest">{{ $t('home.role_design') }}</p>
              </div>
            </div>
          </div>
        </section>

        <section class="bg-neutral-dark/30 py-24 px-6">
          <div class="mx-auto max-w-3xl">
            <div class="mb-16 text-center">
              <h2 class="text-4xl font-black text-main-text">{{ $t('home.faq_title') }}</h2>
              <p class="mt-4 text-secondary-text">{{ $t('home.faq_subtitle') }}</p>
            </div>
            <Accordion :items="faqItems" />
          </div>
        </section>
      </main>
    </div>
  </AppLayout>
</template>
