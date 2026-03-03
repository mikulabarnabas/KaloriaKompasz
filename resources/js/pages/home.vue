<script setup>
import AppLayout from "@/Layouts/AppLayout.vue"
import Accordion from "@/Components/accordion.vue"
import Button from "@/Components/glowingButton.vue"

const faqItems = [
  {
    question: "Miben más ez, mint a többi app?",
    answer: "Személyesebbek vagyunk. Itt nem egy multi algoritmusai mögé bújsz, hanem egy olyan eszközt kapsz, amit mi is nap mint nap használunk az edzéseinkhez."
  },
  {
    question: "Szinkronizálhatom az órámat?",
    answer: "Igen, folyamatosan dolgozunk az Apple Health és Google Fit integrációkon, hogy az adatok maguktól áramoljanak."
  },
  {
    question: "Ingyenes marad az alkalmazás?",
    answer: "Az alap funkciók (kalória és edzésnapló) örökre ingyenesek maradnak, mert hiszünk abban, hogy az egészség nem lehet pénzkérdés."
  }
];

</script>

<template>
  <AppLayout>
    <div class="relative w-full bg-background-dark">

      <div class="absolute inset-0 pointer-events-none z-0 opacity-40 dark:opacity-20"> <svg width="100%" height="100%"
          viewBox="0 0 100 1000" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M 80 0 
           C 80 150, 20 250, 20 400 
           S 90 550, 80 750 
           S 10 850, 50 1000" stroke="url(#gradient-path)" stroke-width="3" stroke-linecap="round"
            stroke-dasharray="12 12" vector-effect="non-scaling-stroke" class="route-path" />

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
                Több mint egy <br />
                <span class="text-primary italic">Kalória</span> & <span class="text-primary italic">Edzés</span> napló
              </h1>
              <p class="mt-8 max-w-2xl text-xl leading-relaxed text-secondary-text">
                Nem csak egy alkalmazást építünk, hanem a <span class="text-main-text">jövőd alapkövét</span>.
                Pontos követés és átlátható adatok, sallangok nélkül.
              </p>
              <div class="mt-10 flex flex-wrap gap-4">
                <Button @click="" icon="arrow_forward">
                  Vágj bele most
                </Button>
              </div>
            </div>
          </div>
        </section>

        <section class="bg-neutral-dark/30 py-24 px-6">
          <div class="mx-auto max-w-7xl">
            <div class="mb-16 flex flex-col items-center text-center">
              <h2 class="text-4xl font-black text-main-text md:text-5xl">A három pillér</h2>
              <div class="mt-4 h-1.5 w-24 rounded-full bg-primary"></div>
              <p class="mt-6 max-w-2xl text-lg text-secondary-text">
                Olyan ökoszisztémát alkottunk, amiben minden megvan, amire a fejlődésedhez szükséged lehet - a konyhától
                a konditeremig.
              </p>
            </div>
            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
              <div
                class="group relative flex flex-col gap-6 rounded-3xl border border-neutral-border bg-neutral-dark p-8 hover:border-primary/50 transition">
                <div
                  class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10 text-primary group-hover:bg-primary group-hover:text-background-dark transition">
                  <span class="material-symbols-outlined text-3xl font-bold">restaurant</span>
                </div>
                <div>
                  <h3 class="text-2xl font-bold text-main-text">Étrend napló</h3>
                  <p class="mt-3 text-secondary-text leading-relaxed">
                    Gyors és egyszerű étkezésrögzítés. Pontos makrotápanyag lebontás, hogy pontosan tudd, mit viszel be
                    a szervezetedbe.
                  </p>
                </div>
              </div>
              <div
                class="group relative flex flex-col gap-6 rounded-3xl border border-neutral-border bg-neutral-dark p-8 hover:border-primary/50 transition">
                <div
                  class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10 text-primary group-hover:bg-primary group-hover:text-background-dark transition">
                  <span class="material-symbols-outlined text-3xl font-bold">fitness_center</span>
                </div>
                <div>
                  <h3 class="text-2xl font-bold text-main-text">Edzésterv</h3>
                  <p class="mt-3 text-secondary-text leading-relaxed">
                    Saját rutinok összeállítása pillanatok alatt. Kövesd a fejlődésed sorozatról sorozatra, és törd át a
                    korlátaidat.
                  </p>
                </div>
              </div>
              <div
                class="group relative flex flex-col gap-6 rounded-3xl border border-neutral-border bg-neutral-dark p-8 hover:border-primary/50 transition">
                <div
                  class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10 text-primary group-hover:bg-primary group-hover:text-background-dark transition">
                  <span class="material-symbols-outlined text-3xl font-bold">insights</span>
                </div>
                <div>
                  <h3 class="text-2xl font-bold text-main-text">Statisztikák</h3>
                  <p class="mt-3 text-secondary-text leading-relaxed">
                    Látványos grafikonok a haladásodról. Lásd át az összefüggéseket az edzésed, az étkezésed és az
                    eredményeid között.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </section>

        <section class="py-24 px-6">
          <div class="mx-auto max-w-7xl flex flex-col items-center text-center">

            <div class="flex flex-col items-center gap-8 mb-16">
              <div class="max-w-2xl">
                <h2 class="text-4xl font-black text-main-text md:text-5xl">Ketten a világ ellen</h2>
                <p class="mt-6 text-lg text-secondary-text">
                  Egy fiatal, ambiciózus csapat vagyunk, akik nem érik be a középszerűvel. Ketten indítottuk el a
                  FitTrack-et azzal a céllal, hogy a piac legjobb platformját hozzuk létre nektek.
                </p>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-12 max-w-4xl mx-auto">

              <div class="group flex flex-col items-center text-center">
                <div
                  class="relative mb-6 h-48 w-48 overflow-hidden rounded-full border-4 border-transparent group-hover:border-primary transition">
                  <img alt="Alapító 1"
                    class="h-full w-full rounded-full object-cover grayscale group-hover:grayscale-0 transition"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuAAh2wlXleEXK9sxmGaVzhsMJXeaJecyfwhGr_F7aE9qGJ_UehP914nVYc2a1aiCKdAtcBnUhpTT4dltsxwC2jnHCiVvhwywv7PK9V8gK2JtyJwQb-yGr4O8mpVl8Wg7asqN8dTTjlj2i6i_obuvTJxT8hOWwvNPkwIP3NY9QvgnPne6x8LBtO82B5Z5a0MjGT30GUp9A-LxJ1p3Rb78cIKudSLcv2Ym4TH1J1Ib9WCkjRlz3SFV5G7ubOJBTtOpnymvHMX-E8CJeJN" />
                </div>
                <h4 class="text-xl font-bold text-main-text">Név 1</h4>
                <p class="text-sm font-medium text-primary uppercase tracking-widest">Alapító / Fejlesztő</p>
              </div>

              <div class="group flex flex-col items-center text-center">
                <div
                  class="relative mb-6 h-48 w-48 overflow-hidden rounded-full border-4 border-transparent group-hover:border-primary transition p-1">
                  <img alt="Alapító 2"
                    class="h-full w-full rounded-full object-cover grayscale group-hover:grayscale-0 transition"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuCTxUiUKhhbC_9IvJmhJ9UoRGDnrCtJ2xYHsYKU9706bTHtX6nkHzpPB7EfHDuzWcKfX86OWEhB6hclOjH54mIJncqG-a2lkMiJCTzdYe13XtVayhQCmD4MPdL5TnUits_88rwFbGf8V1V1VkEz9TgMbd98Akx4F71pmKiOHc3DHbGgxkwLfjb_SmukVKsYplyV-PSGvOYplCInAd_RERo2_5MnSRa_00Qeemg10kCvxYcKn2U-IC5fK0JGTIzz-B6VI3_hb5sf_4aa" />
                </div>
                <h4 class="text-xl font-bold text-main-text">Név 2</h4>
                <p class="text-sm font-medium text-primary uppercase tracking-widest">Alapító / Design</p>
              </div>

            </div>
          </div>
        </section>

        <section class="bg-neutral-dark/30 py-24 px-6">
          <div class="mx-auto max-w-3xl">
            <div class="mb-16 text-center">
              <h2 class="text-4xl font-black text-main-text">Gyakori kérdések</h2>
              <p class="mt-4 text-secondary-text">Minden, amit tudni érdemes a platformunkról.</p>
            </div>

            <Accordion :items="faqItems" />

          </div>
        </section>
      </main>
    </div>
  </AppLayout>
</template>

<style scoped></style>